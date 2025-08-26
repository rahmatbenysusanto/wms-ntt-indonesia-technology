<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GeneralRoom;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\InventoryItem;
use App\Models\InventoryPackage;
use App\Models\InventoryPackageItem;
use App\Models\InventoryPackageItemSN;
use App\Models\Outbound;
use App\Models\OutboundDetail;
use App\Models\OutboundDetailSN;
use App\Models\PmRoom;
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

class PmRoomController extends Controller
{
    public function index(): View
    {
        $pmRoom = InventoryPackage::with('purchaseOrder', 'purchaseOrder.customer', 'user')->where('storage_id', 3)
            ->where('qty', '!=', 0)
            ->paginate(10);

        $title = "Pm Room";
        return view('pm-room.index', compact('title', 'pmRoom'));
    }

    public function detail(Request $request): View
    {
        $product = InventoryPackage::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'storage', 'purchaseOrder', 'purchaseOrder.customer')
            ->where('id', $request->query('id'))
            ->first();

        $title = "Pm Room";
        return view('pm-room.detail', compact('title', 'product'));
    }

    public function createBox(): View
    {
        $listItem = InventoryPackage::with('purchaseOrder', 'inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSn', 'inventoryPackageItem.purchaseOrderDetail')->where('storage_id', 3)->where('qty', '!=', 0)->get();

        $title = "Pm Room";
        return view('pm-room.create-box', compact('title', 'listItem'));
    }

    public function outbound(): View
    {
        $pmRoom = Outbound::with('customer')->where('type', 'pm room')->latest()->paginate(10);

        $title = "Pm Room Outbound";
        return view('pm-room.outbound.index', compact('title', 'pmRoom'));
    }

    public function create(): View
    {
        $salesDoc = InventoryPackage::with('purchaseOrder', 'storage')
            ->where('qty', '!=', 0)
            ->where('storage_id', 3)
            ->get();

        $customer = Customer::all();

        $title = "Pm Room Outbound";
        return view('pm-room.outbound.create', compact('title', 'customer', 'salesDoc'));
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
            ->where('storage_id', 3)
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

        $title = "Pm Room Outbound";
        return view('pm-room.outbound.return', compact('title', 'customer', 'salesDoc', 'storageRaw', 'masterBox'));
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
                'storage_id'            => 3,
                'number'                => 'PM-'.date('YmdHis').rand(111, 999),
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
                'type'          => 'pm room',
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
                            ->update([
                                'qty' => 0
                            ]);
                    }

                    // Decrement Stock
                    InventoryPackage::where('id', $product['inventoryPackageId'])->decrement('qty', $product['qtySelect']);
                    InventoryPackageItem::where('id', $product['inventoryPackageItemId'])->decrement('qty', $product['qtySelect']);

                    // Decrement Inventory
                    $inventory = Inventory::where('purchase_order_id', $product['purchaseOrderId'])->where('type', 'pm')->first();
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
                        'note'                      => 'Outbound PM Room',
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
                if ($request->post('deliveryDest') == 'gr') {
                    $storageId = 2;
                    $number = 'GR-'.date('ymdHis').rand(100, 999);
                } else {
                    $storageId = 4;
                    $number = 'SPARE-'.date('ymdHis').rand(100, 999);
                }

                $inventoryPackage = InventoryPackage::create([
                    'purchase_order_id' => $products[0]['purchaseOrderId'],
                    'storage_id'        => $storageId,
                    'number'            => $number,
                    'reff_number'       => 'Outbound From PM Room',
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
                            'note'                      => 'Outbound From PM Room to '.$request->post('deliveryDest') == 'gr' ? 'GR Room' : 'Spare Room',
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
                'type'          => 'pm room',
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
                            ->update([
                                'qty' => 0
                            ]);
                    }

                    // Decrement Stock
                    InventoryPackage::where('id', $product['inventoryPackageId'])->decrement('qty', $product['qtySelect']);
                    InventoryPackageItem::where('id', $product['inventoryPackageItemId'])->decrement('qty', $product['qtySelect']);

                    // Decrement Inventory
                    $inventory = Inventory::where('purchase_order_id', $product['purchaseOrderId'])->where('type', 'pm')->first();
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
                        'note'                      => 'Return PM Room',
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
                'return_from'           => 'pm',
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
                        'note'                      => 'Return From PM Room',
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
            ->where('storage_id', 3)
            ->get();

        $data = [
            'listBox' => $listBox,
        ];

        $pdf = Pdf::loadView('pdf.pm-room', $data)->setPaper('a4', 'landscape');;
        return $pdf->stream('PM Room.pdf');
    }

    public function downloadExcel(): StreamedResponse
    {
        $listBox = InventoryPackage::with('inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSN', 'inventoryPackageItem.purchaseOrderDetail', 'inventoryPackageItem.purchaseOrderDetail.purchaseOrder')
            ->where('storage_id', 3)
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

        $fileName = 'Report PM Room ' . date('Y-m-d H:i:s') . '.xlsx';
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
        return $pdf->stream('outbound PM Room '.$outbound->delivery_note_number.'.pdf');
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

        $fileName = 'Report Outbound PM Room' . date('Y-m-d_H-i-s') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');
        $response->headers->set('Cache-Control','max-age=0');

        return $response;
    }
}
