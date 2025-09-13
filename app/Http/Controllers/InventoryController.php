<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\InventoryPackage;
use App\Models\InventoryPackageItem;
use App\Models\InventoryPackageItemSN;
use App\Models\Product;
use App\Models\PurchaseOrderDetail;
use App\Models\Storage;
use App\Models\TransferLocation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $inventory = InventoryDetail::with('purchaseOrderDetail', 'purchaseOrderDetail.purchaseOrder')
            ->whereNotIn('storage_id', [1,2,3,4])
            ->where('qty', '!=', 0)
            ->whereHas('purchaseOrderDetail', function ($query) use ($request) {
                if ($request->query('material') != null) {
                    $query->where('material', $request->query('material'));
                }
            })
            ->whereHas('purchaseOrderDetail.purchaseOrder', function ($query) use ($request) {
                if ($request->query('purcDoc') != null) {
                    $query->where('purc_doc', $request->query('purcDoc'));
                }
            })
            ->when($request->query('salesDoc'), function ($query) use ($request) {
                $query->where('sales_doc', $request->query('salesDoc'));
            })
            ->select([
                'sales_doc',
                'purchase_order_detail_id'
            ])
            ->groupBy('sales_doc', 'purchase_order_detail_id')
            ->paginate(10);

        foreach ($inventory as $inv) {
            $queryInv = DB::table('inventory_detail')
                ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                ->leftJoin('purchase_order', 'purchase_order_detail.purchase_order_id', '=', 'purchase_order.id')
                ->where('inventory_detail.qty', '!=', 0)
                ->where('inventory_detail.sales_doc', $inv->sales_doc)
                ->where('inventory_detail.purchase_order_detail_id', $inv->purchase_order_detail_id)
                ->select([
                    'purchase_order.purc_doc',
                    'purchase_order_detail.material',
                    'purchase_order_detail.po_item_desc',
                    'purchase_order_detail.prod_hierarchy_desc',
                    'purchase_order_detail.product_id',
                    'inventory_detail.qty'
                ])
                ->get();

            $qty = 0;
            foreach ($queryInv as $item) {
                $qty += $item->qty;
            }

            $inv->qty = $qty;
            $inv->purc_doc = $queryInv[0]->purc_doc;
            $inv->material = $queryInv[0]->material;
            $inv->po_item_desc = $queryInv[0]->po_item_desc;
            $inv->prod_hierarchy_desc = $queryInv[0]->prod_hierarchy_desc;
            $inv->product_id = $queryInv[0]->product_id;
        }

        $products = Product::all();

        $title = 'Inventory';
        return view('inventory.index', compact('title', 'inventory', 'products'));
    }

    public function indexDetail(Request $request): View
    {
        $salesDoc = $request->query('salesDoc');
        $productId = $request->query('id');

        $product = DB::table('purchase_order_detail')
            ->leftJoin('purchase_order', 'purchase_order_detail.purchase_order_id', '=', 'purchase_order.id')
            ->where('sales_doc', $salesDoc)
            ->where('product_id', $productId)
            ->select([
                'purchase_order.purc_doc',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
            ])
            ->first();

        // Box
        $dataBox = DB::table('inventory_package_item')
            ->leftJoin('purchase_order_detail', 'inventory_package_item.purchase_order_detail_id', '=', 'purchase_order_detail.id')
            ->leftJoin('inventory_package', 'inventory_package_item.inventory_package_id', '=', 'inventory_package.id')
            ->leftJoin('storage', 'storage.id', '=', 'inventory_package.storage_id')
            ->where('purchase_order_detail.sales_doc', $salesDoc)
            ->where('inventory_package_item.product_id', $productId)
            ->where('inventory_package_item.qty', '!=', 0)
            ->whereNotIn('storage.id', [1,2,3,4])
            ->select([
                'inventory_package.number',
                'inventory_package.reff_number',
                'inventory_package.return',
                'inventory_package.return_from',
                'storage.raw',
                'storage.area',
                'storage.rak',
                'storage.bin',
                'inventory_package_item.id AS inventory_package_item_id',
                'inventory_package_item.qty',
            ])
            ->get();

        foreach ($dataBox as $item) {
            $item->serial_number = DB::table('inventory_package_item_sn')
                ->where('inventory_package_item_id', $item->inventory_package_item_id)
                ->where('qty', '!=', 0)
                ->get();
        }

        // Outbound
        $dataOutbound = DB::table('outbound_detail')
            ->leftJoin('outbound', 'outbound.id', '=', 'outbound_detail.outbound_id')
            ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
            ->leftJoin('purchase_order_detail', 'inventory_package_item.purchase_order_detail_id', '=', 'purchase_order_detail.id')
            ->leftJoin('users', 'users.id', '=', 'outbound.created_by')
            ->where('purchase_order_detail.sales_doc', $salesDoc)
            ->where('purchase_order_detail.product_id', $productId)
            ->latest('outbound.delivery_date')
            ->select([
                'outbound.purc_doc',
                'outbound.number',
                'outbound.type',
                'outbound.deliv_loc',
                'outbound.deliv_dest',
                'outbound.delivery_date',
                'outbound.delivery_note_number',
                'outbound_detail.qty',
                'outbound_detail.id AS outbound_detail_id',
                'users.name'
            ])
            ->get();

        foreach ($dataOutbound as $item) {
            $item->serial_number = DB::table('outbound_detail_sn')
                ->where('outbound_detail_id', $item->outbound_detail_id)
                ->get();
        }

        $title = 'Inventory';
        return view('inventory.index-detail', compact('title', 'dataOutbound', 'dataBox', 'product'));
    }

    public function aging(Request $request): View
    {
        $inventory = InventoryDetail::with('purchaseOrderDetail.purchaseOrder', 'purchaseOrderDetail', 'storage', 'inventoryPackageItem', 'inventoryPackageItem.inventoryPackage')
            ->where('qty', '!=', 0)
            ->whereHas('purchaseOrderDetail.purchaseOrder', function ($purchaseOrder) use ($request) {
                if ($request->query('purc_doc')) {
                    $purchaseOrder->where('purc_doc', $request->query('purc_doc'));
                }
            })
            ->whereHas('purchaseOrderDetail', function ($purchaseOrderDetail) use ($request) {
                if ($request->query('material')) {
                    $purchaseOrderDetail->where('material', $request->query('material'));
                }
            })
            ->whereHas('storage', function ($storage) {
                $storage->whereNotIn('id', [1,2,3,4]);
            })
            ->when($request->query('sales_doc'), function ($q) use ($request) {
                $q->where('sales_doc', $request->query('sales_doc'));
            })
            ->latest()
            ->paginate(10)
            ->appends([
                'purc_doc'  => $request->query('purc_doc'),
                'sales_doc' => $request->query('sales_doc'),
                'material'  => $request->query('material'),
            ]);

        $title = 'Inventory Aging';
        return view('inventory.aging', compact('title', 'inventory'));
    }

    public function box(Request $request): View
    {
        $box = InventoryPackage::with('purchaseOrder', 'user', 'storage')
            ->whereNotIn('storage_id', [1,2,3,4])
            ->where('qty', '!=', 0)
            ->whereHas('purchaseOrder', function ($purchaseOrder) use ($request) {
                if ($request->query('purcDoc')) {
                    $purchaseOrder->where('purc_doc', $request->query('purc_doc'));
                }
            })
            ->when($request->query('salesDoc'), function ($q) use ($request) {
                $q->where('purc_doc', '%' . $request->query('salesDoc') . '%');
            })
            ->when($request->query('paNumber'), function ($q) use ($request) {
                $q->where('number', $request->query('paNumber'));
            })
            ->latest()
            ->paginate(10);

        $title = "Inventory Box";
        return view('inventory.box.index', compact('title', 'box'));
    }

    public function boxDetail(Request $request): View
    {
        $products = InventoryPackage::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'storage', 'purchaseOrder', 'purchaseOrder.customer')->where('id', $request->query('id'))->get();

        $title = "Inventory Box";
        return view('inventory.box.detail', compact('title', 'products'));
    }

    public function detail(Request $request): View
    {
        $inventoryDetail = DB::table('inventory_detail')
            ->leftJoin('inventory_package_item', 'inventory_detail.inventory_package_item_id', '=', 'inventory_package_item.id')
            ->leftJoin('inventory_package', 'inventory_package_item.inventory_package_id', '=', 'inventory_package.id')
            ->leftJoin('inventory_package_item_sn', 'inventory_package_item_sn.inventory_package_item_id', '=', 'inventory_package_item.id')
            ->where('inventory_detail.id', $request->query('id'))
            ->select([
                'inventory_package_item_sn.serial_number',
                'inventory_package.number',
                'inventory_package.reff_number'
            ])
            ->get();

        $title = 'Inventory Aging';
        return view('inventory.detail', compact('title', 'inventoryDetail'));
    }

    public function cycleCount(): View
    {
        $cycleCount = InventoryHistory::with('purchaseOrder', 'purchaseOrderDetail', 'user', 'inventoryPackageItem.inventoryPackage', 'inventoryPackageItem.inventoryPackage.storage')->latest()->paginate(10);

        $title = 'Cycle Count';
        return view('inventory.cycle-count', compact('title', 'cycleCount'));
    }

    public function cycleCountDetail(Request $request): View
    {
        $cycleCount = InventoryHistory::with('purchaseOrder', 'purchaseOrderDetail', 'user', 'outbound', 'inventoryPackageItem.inventoryPackage', 'inventoryPackageItem.inventoryPackage.storage')->where('id', $request->query('id'))->first();

        $title = 'Cycle Count';
        return view('inventory.cycle-count-detail', compact('title', 'cycleCount'));
    }

    public function cycleCountDownloadPDF(Request $request): \Illuminate\Http\Response
    {
        $cycleCount = InventoryHistory::with('purchaseOrder', 'purchaseOrderDetail', 'inventoryPackageItem.inventoryPackage', 'inventoryPackageItem.inventoryPackage.storage')
            ->whereBetween('created_at', [$request->get('startDate').' 00:00:00', $request->get('endDate').' 23:59:59']);

        if ($request->get('type') != 'all') {
            $cycleCount = $cycleCount->where('type', $request->get('type'));
        }

        $cycleCount = $cycleCount->get();

        $data = [
            'cycleCount' => $cycleCount
        ];

        $pdf = Pdf::loadView('pdf.cycle-count', $data)->setPaper('a4', 'landscape');;
        return $pdf->stream('Cycle Count.pdf');
    }

    public function cycleCountDownloadExcel(Request $request): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $cycleCount = InventoryHistory::with('purchaseOrder', 'purchaseOrderDetail', 'inventoryPackageItem.inventoryPackage', 'inventoryPackageItem.inventoryPackage.storage')
            ->whereBetween('created_at', [$request->get('startDate').' 00:00:00', $request->get('endDate').' 23:59:59']);

        if ($request->get('type') != 'all') {
            $cycleCount = $cycleCount->where('type', $request->get('type'));
        }

        $cycleCount = $cycleCount->get();

        $sheet->setCellValue('A1', 'Purc Doc');
        $sheet->setCellValue('B1', 'Sales Doc');
        $sheet->setCellValue('C1', 'Material');
        $sheet->setCellValue('D1', 'PO Item Desc');
        $sheet->setCellValue('E1', 'Prod Hierarchy Desc');
        $sheet->setCellValue('F1', 'QTY');
        $sheet->setCellValue('G1', 'Storage Location');
        $sheet->setCellValue('H1', 'Type');
        $sheet->setCellValue('I1', 'Date');
        $sheet->setCellValue('J1', 'Serial Number');

        $column = 2;
        foreach ($cycleCount as $item) {
            $sheet->setCellValue('A'.$column, data_get($item, 'purchaseOrderDetail.purchaseOrder.purc_doc', ''));
            $sheet->setCellValue('B'.$column, data_get($item, 'purchaseOrderDetail.sales_doc', ''));
            $sheet->setCellValue('C'.$column, data_get($item, 'purchaseOrderDetail.material', ''));
            $sheet->setCellValue('D'.$column, data_get($item, 'purchaseOrderDetail.po_item_desc', ''));
            $sheet->setCellValue('E'.$column, data_get($item, 'purchaseOrderDetail.prod_hierarchy_desc', ''));
            $sheet->setCellValue('F'.$column, (string) $item->qty);

            if (in_array($item->inventoryPackageItem->inventoryPackage->storage->id, [2,3,4])) {
                $storage = $item->inventoryPackageItem->inventoryPackage->storage->raw;
            } else if ($item->inventoryPackageItem->inventoryPackage->storage->id == 1) {
                $storage = 'Cross Docking';
            } else {
                $storage = $item->inventoryPackageItem->inventoryPackage->storage->raw.' - '.$item->inventoryPackageItem->inventoryPackage->storage->area.' - '.$item->inventoryPackageItem->inventoryPackage->storage->rak.' - '.$item->inventoryPackageItem->inventoryPackage->storage->bin;
            }

            $sheet->setCellValue('G'.$column, $storage);
            $sheet->setCellValue('H'.$column, (string) $item->type);
            $sheet->setCellValue('I'.$column, optional($item->created_at)->format('Y-m-d H:i:s') ?? '');

            $serials = json_decode($item->serial_number ?: '[]', true) ?: [];

            if (count($serials) === 0) {
                $column++;
            } else {
                foreach ($serials as $sn) {
                    $sheet->setCellValue('J'.$column, (string) $sn);
                    $column++;
                }
            }
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $fileName = 'Report Cycle Count ' . date('Y-m-d H:i:s') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment;filename=\"$fileName\"");
        $response->headers->set('Cache-Control','max-age=0');

        return $response;
    }

    public function transferLocation(Request $request): View
    {
        $transfer = TransferLocation::with('inventoryPackage', 'oldLocation', 'newLocation', 'user')->latest()->paginate(10);

        $title = 'Transfer Location';
        return view('inventory.transfer-location.index', compact('title', 'transfer'));
    }

    public function transferLocationCreate(): View
    {
        $listBox = InventoryPackage::where('qty', '!=', 0)
            ->whereNotIn('storage_id', [1,2,3,4])
            ->get();

        $storageRaw = Storage::whereNull('area')->whereNull('rak')->whereNull('bin')->whereNotIn('id', [1,2,3,4])->get();

        $title = 'Transfer Location';
        return view('inventory.transfer-location.create', compact('title', 'listBox', 'storageRaw'));
    }

    public function transferLocationFindNumber(Request $request): \Illuminate\Http\JsonResponse
    {
        $package = InventoryPackage::with('purchaseOrder')->where('number', $request->get('paNumber'))->first();
        $products = InventoryPackageItem::with('purchaseOrderDetail')->where('inventory_package_id', $package->id)->get();

        return response()->json([
            'status'    => true,
            'data'      => $products
        ]);
    }

    public function transferLocationStore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $inventoryPackage = InventoryPackage::where('number', $request->post('paNumber'))->first();
            $inventoryPackageItem = InventoryPackageItem::where('inventory_package_id', $inventoryPackage->id)->get();

            TransferLocation::create([
                'inventory_package_id'  => $inventoryPackage->id,
                'old_storage'           => $inventoryPackage->storage_id,
                'new_storage'           => $request->post('storageId'),
                'created_by'            => Auth::id()
            ]);

            InventoryPackage::where('number', $request->post('paNumber'))
                ->update([
                    'storage_id' => $request->post('storageId'),
                ]);

            foreach ($inventoryPackageItem as $item) {
                InventoryDetail::where('inventory_package_item_id', $item->id)
                    ->update([
                        'storage_id' => $request->post('storageId'),
                    ]);
            }

            DB::commit();
            return response()->json([
                'status'    => true,
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status'    => false,
            ]);
        }
    }

    public function changeTypeProduct(Request $request): \Illuminate\Http\JsonResponse
    {
        InventoryPackageItem::where('id', $request->post('id'))->update([
            'is_parent' => $request->post('isParent') == 1 ? 0 : 1,
        ]);

        return response()->json([
            'status'    => true,
        ]);
    }

    public function changeNewBox(Request $request): View
    {
        $inventoryPackage = InventoryPackage::with('storage', 'purchaseOrder')->find($request->query('packageId'));
        $listBox = InventoryPackage::whereNotIn('storage_id', [1,2,3,4])->whereNot('id', $request->query('packageId'))->get();
        $products = DB::table('inventory_package_item')
            ->leftJoin('inventory_package_item_sn', 'inventory_package_item_sn.inventory_package_item_id', '=', 'inventory_package_item.id')
            ->leftJoin('purchase_order_detail', 'inventory_package_item.purchase_order_detail_id', '=', 'purchase_order_detail.id')
            ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
            ->where('inventory_package_item.inventory_package_id', $inventoryPackage->id)
            ->where('inventory_package_item.qty', '!=', 0)
            ->select([
                'inventory_package_item.id',
                'inventory_package_item.inventory_package_id',
                'inventory_package_item.product_id',
                'inventory_package_item.is_parent',
                'inventory_package_item.inventory_item_id',
                'purchase_order.purc_doc',
                'purchase_order_detail.id AS purchase_order_detail_id',
                'purchase_order_detail.item',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                'inventory_package_item_sn.id AS sn_id',
                'inventory_package_item_sn.serial_number',
                'purchase_order_detail.purchase_order_id'
            ])
            ->get();

        $storageRaw = Storage::where('raw', '!=', '-')
            ->where('area', null)
            ->where('rak', null)
            ->where('bin', null)
            ->whereNotIn('id', [1,2,3,4])
            ->get();

        $title = 'Inventory Box';
        return view('inventory.box.change-new-box', compact('title', 'inventoryPackage', 'listBox', 'products', 'storageRaw'));
    }

    public function changeNewBoxStore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $inventoryPackage = InventoryPackage::create([
                'purchase_order_id' => $request->post('purchaseOrderId'),
                'storage_id'        => $request->post('storageId'),
                'number'            => 'PA-'.date('YmdHis').rand(100, 999),
                'reff_number'       => $request->post('reffNumber'),
                'qty_item'          => 0,
                'qty'               => 0,
                'sales_docs'        => json_encode([]),
                'created_by'        => Auth::id()
            ]);

            $qtyItem = 0;
            foreach ($request->post('changeProducts') as $product) {
                $checkInventoryPackageItem = InventoryPackageItem::where('inventory_package_id', $inventoryPackage->id)
                    ->where('product_id', $product['product_id'])
                    ->where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                    ->first();

                if ($checkInventoryPackageItem == null) {
                    $inventoryPackageItem = InventoryPackageItem::create([
                        'inventory_package_id'      => $inventoryPackage->id,
                        'product_id'                => $product['product_id'],
                        'purchase_order_detail_id'  => $product['purchase_order_detail_id'],
                        'is_parent'                 => $product['is_parent'],
                        'qty'                       => 1,
                    ]);

                    $inventory = Inventory::where('purchase_order_id', $product['purchase_order_id'])->where('type', 'inv')->first();
                    $productAging = InventoryDetail::where('inventory_package_item_id', $product['id'])
                        ->where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                        ->first();

                    InventoryDetail::create([
                        'inventory_id'              => $inventory->id,
                        'purchase_order_detail_id'  => $product['purchase_order_detail_id'],
                        'storage_id'                => $inventoryPackage->storage_id,
                        'inventory_package_item_id' => $inventoryPackageItem->id,
                        'sales_doc'                 => $product['sales_doc'],
                        'qty'                       => 1,
                        'aging_date'                => $productAging->aging_date,
                    ]);

                    $inventoryPackageItemId = $inventoryPackageItem->id;
                } else {
                    $productAging = InventoryDetail::where('inventory_package_item_id', $product['id'])
                        ->where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                        ->first();
                    $checkInventoryDetail = InventoryDetail::where('storage_id', $inventoryPackage->storage_id)
                        ->where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                        ->where('inventory_package_item_id', $checkInventoryPackageItem->id)
                        ->whereDate('aging_date', Carbon::parse($productAging->aging_date)->format('Y-m-d'))
                        ->first();
                    if ($checkInventoryDetail == null) {
                        $inventory = Inventory::where('purchase_order_id', $product['purchase_order_id'])->where('type', 'inv')->first();
                        InventoryDetail::create([
                            'inventory_id'              => $inventory->id,
                            'purchase_order_detail_id'  => $product['purchase_order_detail_id'],
                            'storage_id'                => $inventoryPackage->storage_id,
                            'inventory_package_item_id' => $checkInventoryPackageItem->id,
                            'sales_doc'                 => $product['sales_doc'],
                            'qty'                       => 1,
                            'aging_date'                => $productAging->aging_date,
                        ]);
                    } else {
                        InventoryDetail::where('id', $checkInventoryDetail->id)->increment('qty', 1);
                    }

                    InventoryPackageItem::where('id', $checkInventoryPackageItem->id)->increment('qty', 1);
                    $inventoryPackageItemId = $checkInventoryPackageItem->id;
                }

                InventoryPackageItemSN::create([
                    'inventory_package_item_id' => $inventoryPackageItemId,
                    'serial_number'             => $product['serial_number'],
                    'qty'                       => 1
                ]);

                // Decrement Inventory Package
                InventoryPackage::where('id', $product['inventory_package_id'])->decrement('qty', 1);
                InventoryPackageItem::where('id', $product['id'])->decrement('qty', 1);
                InventoryPackageItemSN::where('id', $product['sn_id'])->delete();
            }

            // Update QTY Inv Package
            InventoryPackage::where('id', $inventoryPackage->id)->update([
                'qty_item'  => $qtyItem,
                'qty'       => count($request->post('changeProducts'))
            ]);

            DB::commit();
            return response()->json([
                'status'    => true,
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status'    => false,
            ]);
        }
    }

    public function changeBox(Request $request): View
    {
        $inventoryPackage = InventoryPackage::with('storage', 'purchaseOrder')->find($request->query('packageId'));
        $listBox = InventoryPackage::whereNotIn('storage_id', [1,2,3,4])->whereNot('id', $request->query('packageId'))->get();
        $products = DB::table('inventory_package_item')
            ->leftJoin('inventory_package_item_sn', 'inventory_package_item_sn.inventory_package_item_id', '=', 'inventory_package_item.id')
            ->leftJoin('purchase_order_detail', 'inventory_package_item.purchase_order_detail_id', '=', 'purchase_order_detail.id')
            ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
            ->where('inventory_package_item.inventory_package_id', $inventoryPackage->id)
            ->where('inventory_package_item.qty', '!=', 0)
            ->select([
                'inventory_package_item.id',
                'inventory_package_item.inventory_package_id',
                'inventory_package_item.product_id',
                'inventory_package_item.is_parent',
                'inventory_package_item.inventory_item_id',
                'purchase_order.purc_doc',
                'purchase_order_detail.id AS purchase_order_detail_id',
                'purchase_order_detail.item',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                'inventory_package_item_sn.id AS sn_id',
                'inventory_package_item_sn.serial_number',
                'purchase_order_detail.purchase_order_id'
            ])
            ->get();

        $title = 'Inventory Box';
        return view('inventory.box.change', compact('title', 'inventoryPackage', 'listBox', 'products'));
    }

    public function changeBoxStore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $inventoryPackage = InventoryPackage::find($request->post('newBox'));

            foreach ($request->post('changeProducts') as $product) {
                $checkPackageItem = InventoryPackageItem::where('inventory_package_id', $inventoryPackage->id)
                    ->where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                    ->first();

                if ($checkPackageItem == null) {
                    $newInventoryPackageItem = InventoryPackageItem::where('inventory_package_id', $inventoryPackage->id)
                        ->where('product_id', $product['product_id'])
                        ->where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                        ->where('type', 'inv')
                        ->first();

                    if ($newInventoryPackageItem == null) {
                        $inventoryPackageItem = InventoryPackageItem::create([
                            'inventory_package_id'      => $inventoryPackage->id,
                            'product_id'                => $product['product_id'],
                            'purchase_order_detail_id'  => $product['purchase_order_detail_id'],
                            'is_parent'                 => $product['is_parent'],
                            'qty'                       => 1,
                        ]);
                        $inventoryPackageItemId = $inventoryPackageItem->id;
                    } else {
                        InventoryPackageItem::where('id', $newInventoryPackageItem->id)->increment('qty', 1);
                        $inventoryPackageItemId = $newInventoryPackageItem->id;
                    }

                    $productAging = InventoryDetail::where('inventory_package_item_id', $product['id'])
                        ->where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                        ->first();
                    $checkInventoryDetail = InventoryDetail::where('storage_id', $inventoryPackage->storage_id)
                        ->where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                        ->where('inventory_package_item_id', $inventoryPackageItemId)
                        ->whereDate('aging_date', Carbon::parse($productAging->aging_date)->format('Y-m-d'))
                        ->first();
                    if ($checkInventoryDetail == null) {
                        $inventory = Inventory::where('purchase_order_id', $product['purchase_order_id'])->where('type', 'inv')->first();
                        InventoryDetail::create([
                            'inventory_id'              => $inventory->id,
                            'purchase_order_detail_id'  => $product['purchase_order_detail_id'],
                            'storage_id'                => $inventoryPackage->storage_id,
                            'inventory_package_item_id' => $inventoryPackageItemId,
                            'sales_doc'                 => $product['sales_doc'],
                            'qty'                       => 1,
                            'aging_date'                => $productAging->aging_date,
                        ]);
                    } else {
                        InventoryDetail::where('id', $checkInventoryDetail->id)->increment('qty', 1);
                    }

                    // Decrement Inventory Detail
                    InventoryDetail::where('id', $productAging->id)->decrement('qty', 1);
                } else {
                    $productAging = InventoryDetail::where('inventory_package_item_id', $product['id'])
                        ->where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                        ->first();
                    $checkInventoryDetail = InventoryDetail::where('storage_id', $inventoryPackage->storage_id)
                        ->where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                        ->where('inventory_package_item_id', $checkPackageItem->id)
                        ->whereDate('aging_date', Carbon::parse($productAging->aging_date)->format('Y-m-d'))
                        ->first();
                    if ($checkInventoryDetail == null) {
                        $inventory = Inventory::where('purchase_order_id', $product['purchase_order_id'])->where('type', 'inv')->first();
                        $inventoryPackageItem = InventoryPackageItem::create([
                            'inventory_package_id'      => $inventoryPackage->id,
                            'product_id'                => $product['product_id'],
                            'purchase_order_detail_id'  => $product['purchase_order_detail_id'],
                            'is_parent'                 => $product['is_parent'],
                            'qty'                       => 1,
                        ]);
                        InventoryDetail::create([
                            'inventory_id'              => $inventory->id,
                            'purchase_order_detail_id'  => $product['purchase_order_detail_id'],
                            'storage_id'                => $inventoryPackage->storage_id,
                            'inventory_package_item_id' => $inventoryPackageItem->id,
                            'sales_doc'                 => $product['sales_doc'],
                            'qty'                       => 1,
                            'aging_date'                => $productAging->aging_date,
                        ]);
                        $inventoryPackageItemId = $inventoryPackageItem->id;
                    } else {
                        InventoryPackageItem::where('id', $checkPackageItem->id)->increment('qty', 1);
                        InventoryDetail::where('id', $checkInventoryDetail->id)->increment('qty', 1);
                        $inventoryPackageItemId = $checkPackageItem->id;
                    }

                    // Decrement Inventory Detail
                    InventoryDetail::where('id', $productAging->id)->decrement('qty', 1);
                }

                InventoryPackageItemSN::where('id', $product['sn_id'])->update(['inventory_package_item_id' => $inventoryPackageItemId]);

                // Increment
                InventoryPackage::where('id', $inventoryPackage->id)->increment('qty', 1);

                // Decrement
                InventoryPackage::where('id', $product['inventory_package_id'])->decrement('qty', 1);
                InventoryPackageItem::where('id', $product['id'])->decrement('qty', 1);
            }

            DB::commit();
            return response()->json([
                'status'    => true,
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status'    => false,
            ]);
        }
    }

    // Mobile App
    public function indexMobile(Request $request): View
    {
        $inventory = DB::table('inventory')
            ->leftJoin('inventory_detail', 'inventory_detail.inventory_id', '=', 'inventory.id')
            ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
            ->leftJoin('purchase_order', 'purchase_order.id', '=', 'inventory.purchase_order_id')
            ->leftJoin('customer', 'customer.id', '=', 'purchase_order.customer_id')
            ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'inventory_detail.inventory_package_item_id')
            ->where('inventory.type', 'inv')
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

        $customer = Customer::all();
        $products = Product::all();

        return view('mobile.inventory.index', compact('inventory', 'customer', 'products'));
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
                $query->whereNotIn('storage_id', [1,2,3,4]);
            })
            ->sum('qty');

        $inventoryNominal = $inventoryPackageItem * $purchaseOrderDetail->net_order_price;

        $outboundDetail = DB::table('outbound_detail')
            ->leftJoin('outbound', 'outbound.id', '=', 'outbound_detail.outbound_id')
            ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
            ->where('inventory_package_item.purchase_order_detail_id', $purchaseOrderDetail->id)
            ->where('outbound.type', 'outbound')
            ->sum('outbound_detail.qty');
        $outboundNominal = $outboundDetail * $purchaseOrderDetail->net_order_price;

        $serialNumberOutbound = DB::table('outbound')
            ->leftJoin('outbound_detail', 'outbound_detail.outbound_id', '=', 'outbound.id')
            ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
            ->leftJoin('outbound_detail_sn', 'outbound_detail_sn.outbound_detail_id', '=', 'outbound_detail.id')
            ->where('outbound.type', 'outbound')
            ->where('outbound.status','outbound')
            ->where('inventory_package_item.purchase_order_detail_id', $purchaseOrderDetail->id)
            ->select([
                'outbound_detail_sn.serial_number'
            ])
            ->get();

        $serialNumberStock = DB::table('inventory_package')
            ->leftJoin('inventory_package_item', 'inventory_package_item.inventory_package_id', '=', 'inventory_package.id')
            ->leftJoin('inventory_package_item_sn', 'inventory_package_item_sn.inventory_package_item_id', '=', 'inventory_package_item.id')
            ->whereNotIn('inventory_package.storage_id', [1,2,3,4])
            ->where('inventory_package_item.qty', '!=', 0)
            ->where('inventory_package_item_sn.qty', '!=', 0)
            ->where('inventory_package_item.purchase_order_detail_id', $purchaseOrderDetail->id)
            ->select([
                'serial_number',
            ])
            ->get();

        return view('mobile.inventory.detail', compact('product', 'inventoryPackageItem', 'inventoryNominal', 'outboundDetail', 'outboundNominal', 'serialNumberStock', 'serialNumberOutbound'));
    }

    public function boxMobile(): View
    {
        $box = InventoryPackage::with('purchaseOrder', 'storage')->whereNotIn('storage_id', [1,2,3,4])->latest()->paginate(5);

        return view('mobile.inventory.box', compact('box'));
    }

    public function boxDetailMobile(Request $request): View
    {
        $box = InventoryPackage::with('purchaseOrder', 'storage')->where('id', $request->query('id'))->first();
        $detail = InventoryPackageItem::with('purchaseOrderDetail')->where('inventory_package_id', $request->query('id'))->get();

        return view('mobile.inventory.box-detail', compact('box', 'detail'));
    }

    public function agingMobile(): View
    {
        $queryAging = DB::table('inventory_detail')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_detail.purchase_order_detail_id')
            ->where('qty', '!=', 0);

        $agingType1 = (clone $queryAging)->whereBetween('inventory_detail.aging_date', [Carbon::now()->subDays(90)->startOfDay(), Carbon::now()->subDays(0)->endOfDay()])
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        $agingType2 = (clone $queryAging)->whereBetween('inventory_detail.aging_date', [Carbon::now()->subDays(180)->startOfDay(), Carbon::now()->subDays(91)->endOfDay()])
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        $agingType3 = (clone $queryAging)->whereBetween('inventory_detail.aging_date', [Carbon::now()->subDays(365)->startOfDay(), Carbon::now()->subDays(181)->endOfDay()])
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        $agingType4 = (clone $queryAging)->where('inventory_detail.aging_date', '<', Carbon::now()->subDays(365)->startOfDay())
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        return view('mobile.inventory.aging', compact('agingType1', 'agingType2', 'agingType3', 'agingType4'));
    }

    public function agingDetailMobile(Request $request): View
    {
        switch ($request->query('type')) {
            case 1:
                $text = '1 - 90 Day';
                $start = Carbon::now()->subDays(90)->startOfDay();
                $end = Carbon::now()->subDays(1)->endOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->whereBetween('inventory_detail.aging_date', [$start, $end])
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.aging_date',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->paginate(10)
                    ->appends([
                        'type' => $request->query('type')
                    ]);

                break;
            case 2:
                $text = '91 - 180 Day';
                $start = Carbon::now()->subDays(180)->startOfDay();
                $end = Carbon::now()->subDays(91)->endOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->whereBetween('inventory_detail.aging_date', [$start, $end])
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.aging_date',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->paginate(10)
                    ->appends([
                        'type' => $request->query('type')
                    ]);

                break;
            case 3:
                $text = '181 - 365 Day';
                $start = Carbon::now()->subDays(365)->startOfDay();
                $end = Carbon::now()->subDays(181)->endOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->whereBetween('inventory_detail.aging_date', [$start, $end])
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'inventory_detail.aging_date',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->paginate(10)
                    ->appends([
                        'type' => $request->query('type')
                    ]);
                break;
            case 4:
                $text = '> 365 Day';
                $start = Carbon::now()->subDays(365)->startOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->where('inventory_detail.aging_date', '<', $start)
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'inventory_detail.aging_date',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->paginate(10)
                    ->appends([
                        'type' => $request->query('type')
                    ]);
                break;
        }

        $type = $request->query('type');
        return view('mobile.inventory.aging-detail', compact('text', 'inventoryDetail', 'type'));
    }

    public function downloadExcel(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Purc Doc');
        $sheet->setCellValue('B1', 'Sales Doc');
        $sheet->setCellValue('C1', 'Material');
        $sheet->setCellValue('D1', 'PO Item Desc');
        $sheet->setCellValue('E1', 'Prod Hierarchy Desc');
        $sheet->setCellValue('F1', 'Stock');
        $sheet->setCellValue('G1', 'Nominal');
        $sheet->setCellValue('H1', 'Serial Number');

        $inventoryDetail = DB::table('inventory_detail')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_detail.purchase_order_detail_id')
            ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
            ->where('inventory_detail.qty', '!=', 0)
            ->whereNotIn('inventory_detail.storage_id', [1,2,3,4])
            ->select([
                'purchase_order.purc_doc',
                'purchase_order_detail.id',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                DB::raw('SUM(inventory_detail.qty) as qty'),
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as nominal'),
            ])
            ->groupBy([
                'purchase_order.purc_doc',
                'purchase_order_detail.id',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
            ])
            ->get();

        $column = 2;
        foreach ($inventoryDetail as $detail) {
            $serialNumber = DB::table('inventory_package_item')
                ->leftJoin('inventory_package_item_sn', 'inventory_package_item_sn.inventory_package_item_id', '=', 'inventory_package_item.id')
                ->where('inventory_package_item.purchase_order_detail_id', $detail->id)
                ->where('inventory_package_item_sn.qty', '!=', 0)
                ->select([
                    'inventory_package_item_sn.serial_number',
                ])
                ->get();

            $sheet->setCellValue('A' . $column, $detail->purc_doc);
            $sheet->setCellValue('B' . $column, $detail->sales_doc);
            $sheet->setCellValue('C' . $column, $detail->material);
            $sheet->setCellValue('D' . $column, $detail->po_item_desc);
            $sheet->setCellValue('E' . $column, $detail->prod_hierarchy_desc);
            $sheet->setCellValue('F' . $column, $detail->qty);
            $sheet->setCellValue('G' . $column, $detail->nominal);

            foreach ($serialNumber as $index => $serial) {
                if ($index != 0) {
                    $sheet->setCellValue('A' . $column, '');
                    $sheet->setCellValue('B' . $column, '');
                    $sheet->setCellValue('C' . $column, '');
                    $sheet->setCellValue('D' . $column, '');
                    $sheet->setCellValue('E' . $column, '');
                    $sheet->setCellValue('F' . $column, '');
                    $sheet->setCellValue('G' . $column, '');
                }
                $sheet->setCellValue('H' . $column, $serial->serial_number);
                $column++;
            }
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $fileName = 'Report Inventory ' . date('Y-m-d H:i:s') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment;filename=\"$fileName\"");
        $response->headers->set('Cache-Control','max-age=0');

        return $response;
    }

    public function downloadExcelAging(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Purc Doc');
        $sheet->setCellValue('B1', 'Sales Doc');
        $sheet->setCellValue('C1', 'Material');
        $sheet->setCellValue('D1', 'PO Item Desc');
        $sheet->setCellValue('E1', 'Prod Hierarchy Desc');
        $sheet->setCellValue('F1', 'Stock');
        $sheet->setCellValue('G1', 'Nominal');
        $sheet->setCellValue('H1', 'Aging Date');
        $sheet->setCellValue('I1', 'Serial Number');

        $inventoryAging = InventoryDetail::with('purchaseOrderDetail.purchaseOrder', 'purchaseOrderDetail', 'storage', 'inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSN', 'inventoryPackageItem.inventoryPackage')
            ->where('qty', '!=', 0)
            ->whereHas('storage', function ($storage) {
                $storage->whereNotIn('id', [1,2,3,4]);
            })
            ->whereHas('inventoryPackageItem.inventoryPackageItemSN', function ($inventoryPackageItemSn) {
                $inventoryPackageItemSn->where('qty', '!=', 0);
            })
            ->latest()
            ->get();

        $column = 2;
        foreach ($inventoryAging as $detail) {
            $sheet->setCellValue('A' . $column, $detail->purchaseOrderDetail->purchaseOrder->purc_doc);
            $sheet->setCellValue('B' . $column, $detail->purchaseOrderDetail->sales_doc);
            $sheet->setCellValue('C' . $column, $detail->purchaseOrderDetail->material);
            $sheet->setCellValue('D' . $column, $detail->purchaseOrderDetail->po_item_desc);
            $sheet->setCellValue('E' . $column, $detail->purchaseOrderDetail->prod_hierarchy_desc);
            $sheet->setCellValue('F' . $column, $detail->qty);
            $sheet->setCellValue('G' . $column, $detail->qty * $detail->purchaseOrderDetail->net_order_price);
            $sheet->setCellValue('H' . $column, $detail->aging_date);

            foreach ($detail->inventoryPackageItem->inventoryPackageItemSN ?? [] as $index => $inventoryPackageItemSN) {
                if ($index != 0) {
                    $sheet->setCellValue('A' . $column, '');
                    $sheet->setCellValue('B' . $column, '');
                    $sheet->setCellValue('C' . $column, '');
                    $sheet->setCellValue('D' . $column, '');
                    $sheet->setCellValue('E' . $column, '');
                    $sheet->setCellValue('F' . $column, '');
                    $sheet->setCellValue('G' . $column, '');
                    $sheet->setCellValue('H' . $column, '');
                }
                $sheet->setCellValue('I' . $column, $inventoryPackageItemSN->serial_number);
                $column++;
            }
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $fileName = 'Report Aging ' . date('Y-m-d H:i:s') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment;filename=\"$fileName\"");
        $response->headers->set('Cache-Control','max-age=0');

        return $response;
    }

    public function downloadPdfAging(): \Illuminate\Http\Response
    {
        $inventoryAging = InventoryDetail::with('purchaseOrderDetail.purchaseOrder', 'purchaseOrderDetail', 'storage', 'inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSN', 'inventoryPackageItem.inventoryPackage')
            ->where('qty', '!=', 0)
            ->whereHas('storage', function ($storage) {
                $storage->whereNotIn('id', [1,2,3,4]);
            })
            ->withWhereHas('inventoryPackageItem.inventoryPackageItemSN', function ($q) {
                $q->where('qty', '!=', 0);
            })
            ->latest()
            ->get();

        $data = [
            'inventoryAging' => $inventoryAging,
        ];

        $pdf = Pdf::loadView('pdf.aging', $data)->setPaper('a4', 'landscape');
        $agent = new Agent();
        if ($agent->isDesktop()) {
            return $pdf->stream('Product Aging.pdf');
        } else {
            return $pdf->download('Product Aging.pdf');
        }
    }

    public function downloadExcelBox(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $listBox = InventoryPackage::with('purchaseOrder', 'user', 'storage', 'inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSN', 'inventoryPackageItem.purchaseOrderDetail')
            ->whereNotIn('storage_id', [1,2,3,4])
            ->where('qty', '!=', 0)
            ->withWhereHas('inventoryPackageItem.inventoryPackageItemSN', function ($q) {
                $q->where('qty', '!=', 0);
            })
            ->get();

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
        $sheet->setCellValue('L1', 'Note Return');

        $column = 2;
        foreach ($listBox as $detail) {
            foreach ($detail->inventoryPackageItem as $index => $item) {
                if ($index == 0) {
                    $sheet->setCellValue('A' . $column, $detail->number);
                    $sheet->setCellValue('B' . $column, $detail->reff_number);
                    $sheet->setCellValue('C' . $column, $detail->storage->raw.'-'.$detail->storage->area.'-'.$detail->storage->rak.'-'.$detail->storage->bin);
                    $sheet->setCellValue('D' . $column, $detail->purchaseOrder->purc_doc);
                    $sheet->setCellValue('E' . $column, $item->purchaseOrderDetail->sales_doc);
                    $sheet->setCellValue('F' . $column, $item->purchaseOrderDetail->item);
                    $sheet->setCellValue('G' . $column, $item->purchaseOrderDetail->material);
                    $sheet->setCellValue('H' . $column, $item->purchaseOrderDetail->po_item_desc);
                    $sheet->setCellValue('I' . $column, $item->purchaseOrderDetail->prod_hierarchy_desc);
                    $sheet->setCellValue('J' . $column, $item->qty);
                    $sheet->setCellValue('L' . $column, $detail->note);
                } else {
                    $sheet->setCellValue('D' . $column, $detail->purchaseOrder->purc_doc);
                    $sheet->setCellValue('E' . $column, $item->purchaseOrderDetail->sales_doc);
                    $sheet->setCellValue('F' . $column, $item->purchaseOrderDetail->item);
                    $sheet->setCellValue('G' . $column, $item->purchaseOrderDetail->material);
                    $sheet->setCellValue('H' . $column, $item->purchaseOrderDetail->po_item_desc);
                    $sheet->setCellValue('I' . $column, $item->purchaseOrderDetail->prod_hierarchy_desc);
                    $sheet->setCellValue('J' . $column, $item->qty);
                }

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

        $fileName = 'Report Box Inventory ' . date('Y-m-d H:i:s') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment;filename=\"$fileName\"");
        $response->headers->set('Cache-Control','max-age=0');

        return $response;
    }

    public function downloadPdfBox(Request $request): \Illuminate\Http\Response
    {
        $listBox = InventoryPackage::with('purchaseOrder', 'user', 'storage', 'inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSN', 'inventoryPackageItem.purchaseOrderDetail')
            ->whereNotIn('storage_id', [1,2,3,4])
            ->where('qty', '!=', 0)
            ->withWhereHas('inventoryPackageItem.inventoryPackageItemSN', function ($q) {
                $q->where('qty', '!=', 0);
            })
            ->get();

        $data = [
            'listBox' => $listBox,
        ];

        $pdf = Pdf::loadView('pdf.box', $data)->setPaper('a4', 'landscape');;
        return $pdf->stream('Box Product.pdf');
    }

    public function downloadPdf(): \Illuminate\Http\Response
    {
        $inventoryDetail = DB::table('inventory_detail')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_detail.purchase_order_detail_id')
            ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
            ->where('inventory_detail.qty', '!=', 0)
            ->whereNotIn('inventory_detail.storage_id', [1,2,3,4])
            ->select([
                'purchase_order.purc_doc',
                'purchase_order_detail.id',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                DB::raw('SUM(inventory_detail.qty) as stock'),
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as nominal'),
            ])
            ->groupBy([
                'purchase_order.purc_doc',
                'purchase_order_detail.id',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
            ])
            ->get();

        foreach ($inventoryDetail as $detail) {
            $detail->serialNumber = DB::table('inventory_package_item')
                ->leftJoin('inventory_package_item_sn', 'inventory_package_item_sn.inventory_package_item_id', '=', 'inventory_package_item.id')
                ->where('inventory_package_item.purchase_order_detail_id', $detail->id)
                ->where('inventory_package_item_sn.qty', '!=', 0)
                ->select([
                    'inventory_package_item_sn.serial_number',
                ])
                ->get();
        }

        $data = [
            'inventoryDetail' => $inventoryDetail,
        ];

        $pdf = Pdf::loadView('pdf.product-list', $data)->setPaper('a4', 'landscape');;
        return $pdf->stream('Product List.pdf');
    }

    public function agingDetailPdf(Request $request): \Illuminate\Http\Response
    {
        switch ($request->query('type')) {
            case 1:
                $start = Carbon::now()->subDays(90)->startOfDay();
                $end = Carbon::now()->subDays(1)->endOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->whereBetween('inventory_detail.aging_date', [$start, $end])
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.aging_date',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->get();

                break;
            case 2:
                $start = Carbon::now()->subDays(180)->startOfDay();
                $end = Carbon::now()->subDays(91)->endOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->whereBetween('inventory_detail.aging_date', [$start, $end])
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.aging_date',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->get();

                break;
            case 3:
                $start = Carbon::now()->subDays(365)->startOfDay();
                $end = Carbon::now()->subDays(181)->endOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->whereBetween('inventory_detail.aging_date', [$start, $end])
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'inventory_detail.aging_date',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->get();
                break;
            case 4:
                $start = Carbon::now()->subDays(365)->startOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->where('inventory_detail.aging_date', '<', $start)
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'inventory_detail.aging_date',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->get();
                break;
        }

        $data = [
            'inventoryDetail' => $inventoryDetail,
        ];

        $pdf = Pdf::loadView('pdf.aging-detail', $data)->setPaper('a4', 'landscape');;
        return $pdf->stream('Produk Aging.pdf');
    }
}
