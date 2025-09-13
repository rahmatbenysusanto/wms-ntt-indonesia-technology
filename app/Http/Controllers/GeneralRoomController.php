<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GeneralRoom;
use App\Models\GeneralRoomDetail;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\InventoryItem;
use App\Models\InventoryPackage;
use App\Models\InventoryPackageItem;
use App\Models\InventoryPackageItemSN;
use App\Models\InventoryParent;
use App\Models\Outbound;
use App\Models\OutboundDetail;
use App\Models\OutboundDetailSN;
use App\Models\Product;
use App\Models\PurchaseOrderDetail;
use App\Models\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GeneralRoomController extends Controller
{
    public function index(Request $request): View
    {
        $generalRoom = InventoryPackage::with('purchaseOrder', 'purchaseOrder.customer', 'user')
            ->where('storage_id', 2)
            ->where('qty', '!=', 0)
            ->whereHas('purchaseOrder', function ($query) use ($request) {
                if ($request->query('purcDoc') != null) {
                    $query->where('purc_doc', $request->query('purcDoc'));
                }
            })
            ->whereHas('purchaseOrder', function ($query) use ($request) {
                if ($request->query('client') != null) {
                    $query->where('client_id', $request->query('client'));
                }
            })
            ->when($request->query('salesDoc'), function ($query) use ($request) {
                $query->where('sales_docs', 'LIKE', '%'.$request->query('salesDoc').'%');
            })
            ->paginate(10)
            ->appends([
                'purcDoc'   => $request->query('purcDoc'),
                'salesDoc'  => $request->query('salesDoc'),
                'client'    => $request->query('client'),
            ]);

        $customers = Customer::all();

        $title = "General Room";
        return view('general-room.index', compact('title', 'generalRoom', 'customers'));
    }

    public function detail(Request $request): View
    {
        $product = InventoryPackage::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'storage', 'purchaseOrder', 'purchaseOrder.customer')
            ->where('id', $request->query('id'))
            ->first();

        $title = "General Room";
        return view('general-room.detail', compact('title', 'product'));
    }

    public function createBox(): View
    {
        $listItem = InventoryPackage::with('purchaseOrder', 'inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSn', 'inventoryPackageItem.purchaseOrderDetail')->where('storage_id', 2)->where('qty', '!=', 0)->get();

        $title = "General Room";
        return view('general-room.create-box', compact('title', 'listItem'));
    }

    public function outboundAll(Request $request): \Illuminate\Http\JsonResponse
    {
        GeneralRoom::where('id', $request->post('id'))->update(['status' => 'outbound']);

        return response()->json([
            'status' => true
        ]);
    }

    public function outbound(Request $request): View
    {
        $generalRoom = Outbound::with('customer')
            ->where('type', 'general room')
            ->when($request->query('purcDoc'), function ($query) use ($request) {
                $query->where('purc_doc', 'like', '%' . $request->query('purcDoc') . '%');
            })
            ->when($request->query('salesDoc'), function ($query) use ($request) {
                $query->where('sales_doc', 'like', '%' . $request->query('salesDoc') . '%');
            })
            ->when($request->query('client'), function ($query) use ($request) {
                $query->where('customer_id', $request->query('client'));
            })
            ->latest()
            ->paginate(10)
            ->appends([
                'purcDoc'   => $request->query('purcDoc'),
                'salesDoc'  => $request->query('salesDoc'),
                'client'    => $request->query('client'),
            ]);

        $customers = Customer::all();

        $title = "General Room Outbound";
        return view('general-room.outbound.index', compact('title', 'generalRoom', 'customers'));
    }

    public function create(): View
    {
        $salesDoc = InventoryPackage::with('purchaseOrder', 'storage')
            ->where('qty', '!=', 0)
            ->where('storage_id', 2)
            ->get();

        $customer = Customer::all();

        $title = "General Room Outbound";
        return view('general-room.outbound.create', compact('title', 'customer', 'salesDoc'));
    }

    public function return(): View
    {
        $storageRaw = Storage::whereNotIn('id', [1,2,3,4])
            ->where('raw', '!=', '-')
            ->whereNull('area')
            ->whereNull('rak')
            ->whereNull('bin')
            ->get();

        $salesDoc = InventoryPackage::with('purchaseOrder', 'storage')
            ->where('qty', '!=', 0)
            ->where('storage_id', 2)
            ->get();

        $customer = Customer::all();

        $dataMasterBox = InventoryPackage::whereNotIn('storage_id', [1,2,3,4])
            ->where('qty', '!=', 0)
            ->get();

        $masterBox = [];
        foreach ($dataMasterBox as $box) {
            $explode = explode("-", $box->number);
            $masterBox[] = $explode[0].'-'.$explode[1];
        }
        $masterBox = array_unique($masterBox);

        $title = "General Room Outbound";
        return view('general-room.outbound.return', compact('title', 'customer', 'salesDoc', 'storageRaw', 'masterBox'));
    }

    public function createBoxStore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $purchaseOrder = $request->post('purchaseOrder');
            $check = array_unique($purchaseOrder);

            if (count($check) != 1) {
                abort(400, 'Purc Doc Harus Sama!');
            }

            $inventoryPackage = InventoryPackage::create([
                'purchase_order_id'     => $purchaseOrder[0],
                'storage_id'            => 2,
                'number'                => 'GR-'.date('YmdHis').rand(111, 999),
                'reff_number'           => $request->post('boxName'),
                'qty_item'              => 0,
                'qty'                   => $request->post('qty'),
                'sales_docs'            => json_encode($request->post('salesDocs')),
                'created_by'            => Auth::id()
            ]);

            foreach ($request->post('products') as $product) {
                InventoryPackage::where('id', $product['inventoryPackageId'])->decrement('qty', $product['qtySelect']);
                InventoryPackageItem::where('id', $product['inventoryPackageItemId'])->decrement('qty', $product['qtySelect']);

                // Create Inventory Package Item
                $checkInventoryPackageItem = InventoryPackageItem::where('inventory_package_id', $inventoryPackage->id)
                    ->where('purchase_order_detail_id', $product['purchaseOrderDetailId'])
                    ->first();
                if ($checkInventoryPackageItem != null) {
                    InventoryPackageItem::where('id', $checkInventoryPackageItem->id)->increment('qty', $product['qtySelect']);
                    $inventoryPackageItemId = $checkInventoryPackageItem->id;
                } else {
                    $inventoryPackageItem = InventoryPackageItem::create([
                        'inventory_package_id'      => $inventoryPackage->id,
                        'product_id'                => $product['productId'],
                        'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                        'is_parent'                 => $product['is_parent'],
                        'qty'                       => $product['qtySelect']
                    ]);
                    $inventoryPackageItemId = $inventoryPackageItem->id;
                }

                foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                    InventoryPackageItemSN::where('serial_number', $serialNumber['serial_number'])
                        ->where('inventory_package_item_id', $product['inventoryPackageItemId'])
                        ->where('id', $serialNumber['id'])
                        ->update([
                            'qty' => 0
                        ]);

                    InventoryPackageItemSN::create([
                        'inventory_package_item_id' => $inventoryPackageItemId,
                        'serial_number'             => $serialNumber['serial_number'],
                        'qty'                       => 1
                    ]);
                }
            }

            $qty_item = InventoryPackageItem::where('inventory_package_id', $inventoryPackage->id)->count();
            InventoryPackage::where('id', $inventoryPackage->id)->update([
                'qty_item' => $qty_item
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getTraceAsString());
            Log::error($err->getLine());
            return response()->json([
                'status' => false,
            ]);
        }
    }

    public function createOutbound(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $products = $request->post('products');
            $customer = Customer::find($request->post('customerId'));
            $qty_item = 0;
            $qty = 0;
            $salesDocs = [];

            $outbound = Outbound::create([
                'customer_id'   => $customer->id,
                'purc_doc'      => $products[0]['purcDoc'],
                'sales_docs'    => json_encode([]),
                'outbound_date' => $request->post('deliveryDate'),
                'number'        => 'INV-' . date('ymdHis') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'qty_item'      => 0,
                'qty'           => 0,
                'type'          => 'general room',
                'status'        => 'outbound',
                'deliv_loc'     => $request->post('delivLocation'),
                'deliv_dest'    => $request->post('deliveryDest'),
                'delivery_date' => $request->post('deliveryDate'),
                'delivery_note_number' => $request->post('deliveryNoteNumber'),
                'created_by'    => Auth::id()
            ]);

            foreach ($products as $product) {
                if ($product['disable'] == 0 && $product['qtySelect'] != 0 && $product['qtySelect'] <= $product['qty']) {
                    // Insert Outbound Detail
                    $outboundDetail = OutboundDetail::create([
                        'outbound_id'               => $outbound->id,
                        'inventory_package_item_id' => $product['inventoryPackageItemId'],
                        'qty'                       => $product['qtySelect'],
                    ]);

                    foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                        OutboundDetailSN::create([
                            'outbound_detail_id'        => $outboundDetail->id,
                            'inventory_package_item_id' => $product['inventoryPackageItemId'],
                            'serial_number'             => $serialNumber['serialNumber'],
                        ]);

                        InventoryPackageItemSN::where('inventory_package_item_id', $product['inventoryPackageItemId'])
                            ->where('serial_number', $serialNumber['serialNumber'])
                            ->where('id', $serialNumber['id'])
                            ->update([
                                'qty' => 0
                            ]);
                    }

                    // Decrement Stock
                    InventoryPackage::where('id', $product['inventoryPackageId'])->decrement('qty', $product['qtySelect']);
                    InventoryPackageItem::where('id', $product['inventoryPackageItemId'])->decrement('qty', $product['qtySelect']);

                    // Decrement Inventory
                    $inventory = Inventory::where('purchase_order_id', $product['purchaseOrderId'])->where('type', 'gr')->first();
                    Inventory::where('id', $inventory->id)->decrement('stock', $product['qtySelect']);
                    InventoryDetail::where('inventory_package_item_id', $product['inventoryPackageItemId'])->where('inventory_id', $inventory->id)->decrement('qty', $product['qtySelect']);

                    // Inventory History
                    InventoryHistory::create([
                        'purchase_order_id'         => $product['purchaseOrderId'],
                        'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                        'outbound_id'               => $outbound->id,
                        'inventory_package_item_id' => $product['inventoryPackageItemId'],
                        'qty'                       => $product['qtySelect'],
                        'type'                      => 'outbound',
                        'created_by'                => Auth::id(),
                        'note'                      => 'Outbound General Room',
                    ]);

                    $qty_item++;
                    $qty += $product['qtySelect'];
                    $salesDocs[] = $product['salesDoc'];
                }
            }

            Outbound::where('id', $outbound->id)->update([
                'qty_item'      => $qty_item,
                'qty'           => $qty,
                'sales_docs'    => json_encode(array_unique($salesDocs)),
            ]);

            // Outbound To PM Room or Spare Room
            if ($request->post('deliveryDest') != 'client') {
                if ($request->post('deliveryDest') == 'pm') {
                    $storageId = 3;
                    $number = 'PM-'.date('ymdHis').rand(100, 999);
                } else {
                    $storageId = 4;
                    $number = 'SPARE-'.date('ymdHis').rand(100, 999);
                }

                $inventoryPackage = InventoryPackage::create([
                    'purchase_order_id' => $products[0]['purchaseOrderId'],
                    'storage_id'        => $storageId,
                    'number'            => $number,
                    'reff_number'       => 'Outbound From General Room',
                    'qty_item'          => $qty_item,
                    'qty'               => $qty,
                    'sales_docs'        => json_encode($salesDocs),
                    'note'              => $request->post('note') ?? '',
                    'created_by'        => Auth::id()
                ]);

                foreach ($products as $product) {
                    if ($product['disable'] == 0 && $product['qtySelect'] != 0 && $product['qtySelect'] <= $product['qty']) {
                        $checkInventoryPackageItem = InventoryPackageItem::where('inventory_package_id', $inventoryPackage->id)
                            ->where('purchase_order_detail_id', $product['purchaseOrderDetailId'])
                            ->first();

                        if ($checkInventoryPackageItem == null) {
                            $inventoryPackageItem = InventoryPackageItem::create([
                                'inventory_package_id'      => $inventoryPackage->id,
                                'product_id'                => $product['productId'],
                                'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                                'is_parent'                 => $product['isParent'],
                                'qty'                       => $product['qtySelect'],
                            ]);

                            foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                                InventoryPackageItemSN::create([
                                    'inventory_package_item_id' => $inventoryPackageItem->id,
                                    'serial_number'             => $serialNumber['serialNumber'],
                                    'qty'                       => 1
                                ]);
                            }
                        } else {
                            InventoryPackageItem::where('id', $checkInventoryPackageItem->id)->increment('qty', $product['qtySelect']);
                            foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                                InventoryPackageItemSN::create([
                                    'inventory_package_item_id' => $checkInventoryPackageItem->id,
                                    'serial_number'             => $serialNumber['serialNumber'],
                                    'qty'                       => 1
                                ]);
                            }
                        }

                        // Inventory
                        $checkInventory = Inventory::where('purchase_order_id', $product['purchaseOrderId'])->where('type', $request->post('deliveryDest'))->first();
                        if ($checkInventory == null) {
                            Inventory::create([
                                'purchase_order_id' => $product['purchaseOrderId'],
                                'stock'             => $product['qtySelect'],
                                'type'              => $request->post('deliveryDest'),
                            ]);
                        } else {
                            Inventory::where('id', $checkInventory->id)->increment('stock', $product['qtySelect']);
                        }

                        // Inventory History
                        InventoryHistory::create([
                            'purchase_order_id'         => $product['purchaseOrderId'],
                            'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                            'outbound_id'               => $outbound->id,
                            'inventory_package_item_id' => $product['inventoryPackageItemId'],
                            'qty'                       => $product['qtySelect'],
                            'type'                      => 'outbound',
                            'created_by'                => Auth::id(),
                            'note'                      => 'Outbound From General Room to '.$request->post('deliveryDest') == 'pm' ? 'PM Room' : 'Spare Room',
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getTraceAsString());
            return response()->json([
                'status' => false,
            ]);
        }
    }

    public function returnStore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $products = $request->post('products');
            $customer = Customer::find($request->post('customerId'));
            $qty_item = 0;
            $qty = 0;
            $salesDocs = [];

            $outbound = Outbound::create([
                'customer_id'   => $customer->id,
                'purc_doc'      => $products[0]['purcDoc'],
                'sales_docs'    => json_encode([]),
                'outbound_date' => $request->post('outboundDate'),
                'number'        => 'INV-' . date('ymdHis') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'qty_item'      => 0,
                'qty'           => 0,
                'type'          => 'general room',
                'status'        => 'return',
                'deliv_loc'     => '-',
                'deliv_dest'    => '-',
                'note'          => $request->post('note'),
                'created_by'    => Auth::id()
            ]);

            foreach ($products as $product) {
                if ($product['disable'] == 0 && $product['qtySelect'] != 0 && $product['qtySelect'] <= $product['qty']) {
                    // Insert Outbound Detail
                    $outboundDetail = OutboundDetail::create([
                        'outbound_id'               => $outbound->id,
                        'inventory_package_item_id' => $product['inventoryPackageItemId'],
                        'qty'                       => $product['qtySelect'],
                    ]);

                    foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                        OutboundDetailSN::create([
                            'outbound_detail_id'        => $outboundDetail->id,
                            'inventory_package_item_id' => $product['inventoryPackageItemId'],
                            'serial_number'             => $serialNumber['serialNumber'],
                        ]);

                        InventoryPackageItemSN::where('inventory_package_item_id', $product['inventoryPackageItemId'])
                            ->where('serial_number', $serialNumber['serialNumber'])
                            ->where('id', $serialNumber['id'])
                            ->update([
                                'qty' => 0
                            ]);
                    }

                    // Decrement Stock
                    InventoryPackage::where('id', $product['inventoryPackageId'])->decrement('qty', $product['qtySelect']);
                    InventoryPackageItem::where('id', $product['inventoryPackageItemId'])->decrement('qty', $product['qtySelect']);

                    // Decrement Inventory
                    $inventory = Inventory::where('purchase_order_id', $product['purchaseOrderId'])->where('type', 'gr')->first();
                    Inventory::where('id', $inventory->id)->decrement('stock', $product['qtySelect']);
                    InventoryDetail::where('inventory_package_item_id', $product['inventoryPackageItemId'])->where('inventory_id', $inventory->id)->decrement('qty', $product['qtySelect']);

                    // Inventory History
                    InventoryHistory::create([
                        'purchase_order_id'         => $product['purchaseOrderId'],
                        'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                        'outbound_id'               => $outbound->id,
                        'inventory_package_item_id' => $product['inventoryPackageItemId'],
                        'qty'                       => $product['qtySelect'],
                        'type'                      => 'outbound',
                        'created_by'                => Auth::id(),
                        'note'                      => 'Return General Room',
                    ]);

                    $qty_item++;
                    $qty += $product['qtySelect'];
                    $salesDocs[] = $product['salesDoc'];
                }
            }

            Outbound::where('id', $outbound->id)->update([
                'qty_item'      => $qty_item,
                'qty'           => $qty,
                'sales_docs'    => json_encode(array_unique($salesDocs)),
            ]);

            // Insert QTY To Inventory
            $inventoryPackage = InventoryPackage::create([
                'purchase_order_id'     => $products[0]['purchaseOrderId'],
                'storage_id'            => $request->post('bin'),
                'number'                => $request->post('masterBox') ?? 'PA-'.date('ymdHis').rand(100, 999),
                'reff_number'           => $request->post('boxName'),
                'qty'                   => $qty,
                'qty_item'              => $qty_item,
                'sales_docs'            => json_encode(array_unique($salesDocs)),
                'note'                  => $request->post('note'),
                'return'                => 1,
                'return_from'           => 'gr',
                'created_by'            => Auth::id(),
            ]);

            foreach ($products as $product) {
                if ($product['disable'] == 0 && $product['qtySelect'] != 0 && $product['qtySelect'] <= $product['qty']) {
                    $inventoryPackageItem = InventoryPackageItem::create([
                        'inventory_package_id'      => $inventoryPackage->id,
                        'product_id'                => $product['productId'],
                        'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                        'is_parent'                 => $product['isParent'],
                        'qty'                       => $product['qtySelect'],
                    ]);

                    foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                        InventoryPackageItemSN::create([
                            'inventory_package_item_id' => $inventoryPackageItem->id,
                            'serial_number'             => $serialNumber['serialNumber'],
                            'qty'                       => 1,
                        ]);
                    }

                    // Inventory Detail
                    $productAging = InventoryDetail::where('inventory_package_item_id', $product['inventoryPackageItemId'])
                        ->where('purchase_order_detail_id', $product['purchaseOrderDetailId'])
                        ->first();
                    $checkInventoryDetail = InventoryDetail::where('storage_id', $request->post('bin'))
                        ->where('purchase_order_detail_id', $product['purchaseOrderDetailId'])
                        ->where('inventory_package_item_id', $inventoryPackageItem->id)
                        ->whereDate('aging_date', Carbon::parse($productAging->aging_date)->format('Y-m-d'))
                        ->first();
                    if ($checkInventoryDetail == null) {
                        $inventory = Inventory::where('purchase_order_id', $product['purchaseOrderId'])->where('type', 'inv')->first();
                        InventoryDetail::create([
                            'inventory_id'              => $inventory->id,
                            'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                            'storage_id'                => $request->post('bin'),
                            'inventory_package_item_id' => $inventoryPackageItem->id,
                            'sales_doc'                 => $product['salesDoc'],
                            'qty'                       => 1,
                            'aging_date'                => $productAging->aging_date,
                        ]);
                    } else {
                        InventoryDetail::where('id', $checkInventoryDetail->id)->increment('qty', $product['qtySelect']);
                    }

                    // Increment Inventory
                    $inventory = Inventory::where('purchase_order_id', $product['purchaseOrderId'])->where('type', 'inv')->first();
                    Inventory::where('id', $inventory->id)->increment('stock', $product['qtySelect']);
                    InventoryDetail::where('inventory_package_item_id', $product['inventoryPackageItemId'])->where('inventory_id', $inventory->id)->increment('qty', $product['qtySelect']);

                    InventoryHistory::create([
                        'purchase_order_id'         => $product['purchaseOrderId'],
                        'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                        'outbound_id'               => $outbound->id,
                        'inventory_package_item_id' => $product['inventoryPackageItemId'],
                        'qty'                       => $product['qtySelect'],
                        'type'                      => 'inbound',
                        'created_by'                => Auth::id(),
                        'note'                      => 'Return From General Room',
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status' => false,
            ]);
        }
    }

    public function downloadPdf(): \Illuminate\Http\Response
    {
        $listBox = InventoryPackage::with('inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSN', 'inventoryPackageItem.purchaseOrderDetail', 'inventoryPackageItem.purchaseOrderDetail.purchaseOrder')
            ->where('storage_id', 2)
            ->get();

        $data = [
            'listBox' => $listBox,
        ];

        $pdf = Pdf::loadView('pdf.general-room', $data)->setPaper('a4', 'landscape');;
        return $pdf->stream('General Room.pdf');
    }

    public function downloadExcel(): StreamedResponse
    {
        $listBox = InventoryPackage::with('inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSN', 'inventoryPackageItem.purchaseOrderDetail', 'inventoryPackageItem.purchaseOrderDetail.purchaseOrder')
            ->where('storage_id', 2)
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'PA Number');
        $sheet->setCellValue('B1', 'Reff Number');
        $sheet->setCellValue('C1', 'Storage');
        $sheet->setCellValue('D1', 'Purc Doc');
        $sheet->setCellValue('E1', 'Sales Doc');
        $sheet->setCellValue('F1', 'Item');
        $sheet->setCellValue('G1', 'Material');
        $sheet->setCellValue('H1', 'PO Item Desc');
        $sheet->setCellValue('I1', 'Prod Hierarchy Desc');
        $sheet->setCellValue('J1', 'QTY');
        $sheet->setCellValue('K1', 'Serial Number');

        $column = 2;
        foreach ($listBox as $detail) {
            foreach ($detail->inventoryPackageItem as $index => $item) {
                if ($index == 0) {
                    $sheet->setCellValue('A' . $column, $detail->number);
                    $sheet->setCellValue('B' . $column, $detail->reff_number);
                    $sheet->setCellValue('C' . $column, $detail->storage->raw.'-'.$detail->storage->area.'-'.$detail->storage->rak.'-'.$detail->storage->bin);
                }
                $sheet->setCellValue('D' . $column, $detail->purchaseOrder->purc_doc);
                $sheet->setCellValue('E' . $column, $item->purchaseOrderDetail->sales_doc);
                $sheet->setCellValue('F' . $column, $item->purchaseOrderDetail->item);
                $sheet->setCellValue('G' . $column, $item->purchaseOrderDetail->material);
                $sheet->setCellValue('H' . $column, $item->purchaseOrderDetail->po_item_desc);
                $sheet->setCellValue('I' . $column, $item->purchaseOrderDetail->prod_hierarchy_desc);
                $sheet->setCellValue('J' . $column, $item->qty);

                foreach ($item->inventoryPackageItemSN as $serialNumber) {
                    $sheet->setCellValue('K' . $column, $serialNumber->serial_number);
                    $column++;
                }
            }
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $fileName = 'Report General Room ' . date('Y-m-d H:i:s') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment;filename=\"$fileName\"");
        $response->headers->set('Cache-Control','max-age=0');

        return $response;
    }

    public function outboundDownloadPdf(Request $request): \Illuminate\Http\Response
    {
        $outbound = Outbound::with('customer')->where('id', $request->query('id'))->first();
        $outboundDetail = OutboundDetail::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'outboundDetailSN')->where('outbound_id', $request->query('id'))->get();

        $data = [
            'outbound'          => $outbound,
            'outboundDetail'    => $outboundDetail,
        ];

        $pdf = Pdf::loadView('pdf.outbound', $data);
        return $pdf->stream('outbound General Room '.$outbound->delivery_note_number.'.pdf');
    }

    public function outboundDownloadExcel(Request $request): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $outbound = Outbound::with('customer')
            ->where('id', $request->query('id'))
            ->firstOrFail();

        $sheet->setCellValue('A1', 'From');
        $sheet->setCellValue('A2', 'NTT Global Technology');

        $sheet->setCellValue('C1', 'To');
        $sheet->setCellValue('C2', $outbound->customer->name);

        $sheet->setCellValue('E1', 'No : '.$outbound->delivery_note_number);
        $sheet->setCellValue('E2', $outbound->created_at);

        $outboundDetail = OutboundDetail::with([
            'inventoryPackageItem',
            'inventoryPackageItem.purchaseOrderDetail',
            'outboundDetailSN'
        ])
            ->where('outbound_id', $request->query('id'))
            ->get();

        $sheet->setCellValue('A7', 'No');
        $sheet->setCellValue('B7', 'Product');
        $sheet->setCellValue('C7', 'Description');
        $sheet->setCellValue('D7', 'QTY');
        $sheet->setCellValue('E7', 'Serial Number');

        $row = 8;
        $no  = 1;

        foreach ($outboundDetail as $detail) {
            $pod = optional(optional($detail->inventoryPackageItem)->purchaseOrderDetail);

            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, (string) $pod->sales_doc);

            $descLines = [
                'Material: ' . (string) $pod->material,
                'PO Item Desc: ' . (string) $pod->po_item_desc,
                'Hierarchy Desc: ' . (string) $pod->prod_hierarchy_desc,
            ];
            $sheet->setCellValue('C' . $row, implode("\n", $descLines));
            $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('D' . $row, (float) $detail->qty);
            $snList = [];
            foreach ($detail->outboundDetailSN as $sn) {
                $snList[] = (string) $sn->serial_number;
            }
            $sheet->setCellValue('E' . $row, implode("\n", $snList));
            $sheet->getStyle('E' . $row)->getAlignment()->setWrapText(true);

            $row++;
            $no++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function() use ($writer) {
            if (ob_get_length()) { @ob_end_clean(); }
            $writer->save('php://output');
        });

        $fileName = 'Report Outbound General Room' . date('Y-m-d_H-i-s') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');
        $response->headers->set('Cache-Control','max-age=0');

        return $response;
    }

    // Mobile View

    public function indexMobile(Request $request): View
    {
        $inventory = DB::table('inventory')
            ->leftJoin('inventory_detail', 'inventory_detail.inventory_id', '=', 'inventory.id')
            ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
            ->leftJoin('purchase_order', 'purchase_order.id', '=', 'inventory.purchase_order_id')
            ->leftJoin('customer', 'customer.id', '=', 'purchase_order.customer_id')
            ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'inventory_detail.inventory_package_item_id')
            ->where('inventory.type', 'gr')
            ->where('inventory_detail.qty', '!=', 0)
            ->when($request->query('purcDoc'), function ($query) use ($request) {
                $query->where('purchase_order.purc_doc', 'LIKE', '%'.$request->query('purcDoc').'%');
            })
            ->when($request->query('salesDoc'), function ($query) use ($request) {
                $query->where('purchase_order_detail.sales_doc', 'LIKE', '%'.$request->query('salesDoc').'%');
            })
            ->when($request->query('material'), function ($query) use ($request) {
                $query->where('purchase_order_detail.material', 'LIKE', '%'.$request->query('material').'%');
            })
            ->when($request->query('customer'), function ($query) use ($request) {
                $query->where('customer.name', 'LIKE', '%'.$request->query('customer').'%');
            })
            ->select([
                'purchase_order.purc_doc',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.product_id',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                'inventory_package_item.is_parent',
                DB::raw('SUM(inventory_detail.qty) AS qty'),
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) AS nominal'),
            ])
            ->groupBy([
                'purchase_order.purc_doc',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.product_id',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                'inventory_package_item.is_parent',
            ])
            ->paginate(5)
            ->appends([
                'purcDoc'   => $request->query('purcDoc'),
                'salesDoc'  => $request->query('salesDoc'),
                'material'  => $request->query('material'),
                'customer'  => $request->query('customer'),
                'search'    =>  $request->query('search'),
            ]);

        return view('mobile.gr.index', compact('inventory'));
    }

    public function indexDetailMobile(Request $request): View
    {
        $purcDoc = $request->query('po');
        $salesDoc = $request->query('so');
        $productId = $request->query('id');

        $product = Product::find($productId);
        $purchaseOrderDetail = PurchaseOrderDetail::where('sales_doc', $salesDoc)->where('product_id', $productId)->first();

        $inventoryPackageItem = InventoryPackageItem::with('inventoryPackage')
            ->where('purchase_order_detail_id', $purchaseOrderDetail->id)
            ->whereHas('inventoryPackage', function ($query) {
                $query->where('storage_id', 2);
            })
            ->sum('qty');

        $inventoryNominal = $inventoryPackageItem * $purchaseOrderDetail->net_order_price;

        $outboundDetail = DB::table('outbound_detail')
            ->leftJoin('outbound', 'outbound.id', '=', 'outbound_detail.outbound_id')
            ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
            ->where('inventory_package_item.purchase_order_detail_id', $purchaseOrderDetail->id)
            ->where('outbound.type', 'general room')
            ->sum('outbound_detail.qty');

        $outboundNominal = $outboundDetail * $purchaseOrderDetail->net_order_price;

        $serialNumberOutbound = DB::table('outbound')
            ->leftJoin('outbound_detail', 'outbound_detail.outbound_id', '=', 'outbound.id')
            ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
            ->leftJoin('outbound_detail_sn', 'outbound_detail_sn.outbound_detail_id', '=', 'outbound_detail.id')
            ->where('outbound.type', 'general room')
            ->where('outbound.status','outbound')
            ->where('inventory_package_item.purchase_order_detail_id', $purchaseOrderDetail->id)
            ->select([
                'outbound_detail_sn.serial_number'
            ])
            ->get();

        $serialNumberStock = DB::table('inventory_package')
            ->leftJoin('inventory_package_item', 'inventory_package_item.inventory_package_id', '=', 'inventory_package.id')
            ->leftJoin('inventory_package_item_sn', 'inventory_package_item_sn.inventory_package_item_id', '=', 'inventory_package_item.id')
            ->where('inventory_package.storage_id', 2)
            ->where('inventory_package_item.qty', '!=', 0)
            ->where('inventory_package_item_sn.qty', '!=', 0)
            ->where('inventory_package_item.purchase_order_detail_id', $purchaseOrderDetail->id)
            ->select([
                'serial_number',
            ])
            ->get();

        return view('mobile.gr.detail', compact('product', 'inventoryPackageItem', 'inventoryNominal', 'outboundDetail', 'outboundNominal', 'serialNumberStock', 'serialNumberOutbound'));
    }
}

































