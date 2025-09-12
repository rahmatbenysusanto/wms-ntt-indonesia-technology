<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\InventoryItem;
use App\Models\InventoryPackage;
use App\Models\InventoryPackageItem;
use App\Models\InventoryPackageItemSN;
use App\Models\Product;
use App\Models\ProductPackage;
use App\Models\ProductPackageItem;
use App\Models\ProductPackageItemSN;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrderEditReq;
use App\Models\SerialNumber;
use App\Models\Storage;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InboundController extends Controller
{
    public function purchaseOrder(Request $request): View
    {
        $purchaseOrder = PurchaseOrder::with('vendor', 'customer', 'user');

        if ($request->query('purcDoc') != null) {
            $purchaseOrder = $purchaseOrder->where('purc_doc', $request->query('purcDoc'));
        }

        if ($request->query('vendor') != null) {
            $purchaseOrder = $purchaseOrder->where('vendor_id', $request->query('vendor'));
        }

        if ($request->query('customer') != null) {
            $purchaseOrder = $purchaseOrder->where('customer_id', $request->query('customer'));
        }

        if ($request->query('date') != null) {
            $purchaseOrder = $purchaseOrder->where('created_at', $request->query('date'));
        }

        $purchaseOrder = $purchaseOrder->latest()->paginate(10);

        $vendor = Vendor::all();
        $customer = Customer::all();

        $title = "Purchase Order";
        return view('inbound.purchase-order.index', compact('title', 'purchaseOrder', 'customer', 'vendor'));
    }

    public function purchaseOrderUpload(): View
    {
        $title = "Purchase Order";
        return view('inbound.purchase-order.upload', compact('title'));
    }

    /**
     * @throws GuzzleException
     */
    public function purchaseOrderUploadProcess(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            foreach ($request->post('purchaseOrder') as $item) {
                // Check apakah nomor PO sudah ada
                $checkPO = PurchaseOrder::where('purc_doc', $item['purc_doc'])->first();

                if ($checkPO) {
                    $checkPoDetail = PurchaseOrderDetail::where('purchase_order_id', $checkPO->id)
                        ->where('item', $item['item'])
                        ->first();
                    if ($checkPoDetail) {
                        continue;
                    }
                    $this->storePurchaseOrderDetail($checkPO, $item);
                } else {
                    // PO belum ada, buat PO terlebih dahulu
                    $checkVendor = Vendor::where('name', $item['vendor_name'])->first();
                    if ($checkVendor) {
                        $vendor = Vendor::find($checkVendor->id);
                    } else {
                        $vendor = Vendor::create([
                            'name' => $item['vendor_name'],
                        ]);
                    }

                    $checkCustomer = Customer::where('name', $item['customer_name'])->first();
                    if ($checkCustomer) {
                        $customer = Customer::find($checkCustomer->id);
                    } else {
                        $customer = Customer::create([
                            'name' => $item['customer_name'],
                        ]);
                    }

                    $purchaseOrder = PurchaseOrder::create([
                        'purc_doc'          => $item['purc_doc'],
                        'vendor_id'         => $vendor->id,
                        'customer_id'       => $customer->id,
                        'sales_doc_qty'     => 0,
                        'material_qty'      => 0,
                        'item_qty'          => 0,
                        'status'            => 'new',
                        'created_by'        => Auth::id() ?? 1
                    ]);

                    $this->storePurchaseOrderDetail($purchaseOrder, $item);
                }
            }

            DB::commit();
            return response()->json([
                'status' => true
            ], 201);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status' => 'error',
            ], 400);
        }
    }

    /**
     * @throws GuzzleException
     */

    private function storePurchaseOrderDetail($checkPO, mixed $item): void
    {
        $checkProduct = Product::where('material', $item['material'])->first();
        $productId = $checkProduct?->id ?? Product::create([
            'material'            => $item['material'],
            'po_item_desc'        => $item['po_item_desc'] ?? null,
            'prod_hierarchy_desc' => $item['prod_hierarchy_desc'] ?? null,
        ])->id;

        $formattedDate = !empty($item['date']) ? Carbon::createFromFormat('Y-m-d', $item['date'])->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $fxKey = "fx:USD:IDR:{$formattedDate}";
        $fx = Cache::remember($fxKey, 86400, function () use ($formattedDate) {
            $resp = Http::retry(3, 200)->timeout(10)->get("https://api.frankfurter.dev/v1/{$formattedDate}", [
                'base'    => 'USD',
                'symbols' => 'IDR',
            ]);

            if ($resp->failed()) {
                $resp = Http::retry(2, 300)->timeout(10)->get('https://api.frankfurter.dev/v1/latest', [
                    'base'    => 'USD',
                    'symbols' => 'IDR',
                ]);
            }

            $json = $resp->json() ?? [];
            return [
                'date' => $json['date'] ?? $formattedDate,
                'rate' => (float)($json['rates']['IDR'] ?? 0.0),
            ];
        });

        $apiDate  = $fx['date'];
        $usdToIDR = $fx['rate'];

        $rawPrice   = (float)($item['net_order_price'] ?? 0);
        $rawCurr    = strtoupper($item['currency'] ?? '');

        if ($rawPrice > 0 && $usdToIDR > 0) {
            if ($rawCurr === 'USD') {
                $netOrderPrice = round($rawPrice, 2);
                $currency      = 'USD';
                $priceIDR      = (int) round($rawPrice * $usdToIDR);
            } else {
                $netOrderPrice = round($rawPrice / $usdToIDR, 2);
                $currency      = 'IDR';
                $priceIDR      = (int) round($rawPrice);
            }
        } else {
            $netOrderPrice = 0.0;
            $currency      = $rawCurr ?: '';
            $priceIDR      = 0;
        }

        PurchaseOrderDetail::create([
            'purchase_order_id'     => $checkPO->id,
            'product_id'            => $productId,
            'status'                => 'new',
            'qty_quality_control'   => 0,
            'sales_doc'             => $item['sales_doc'] ?? null,
            'item'                  => $item['item'] ?? null,
            'material'              => $item['material'] ?? null,
            'po_item_desc'          => $item['po_item_desc'] ?? null,
            'prod_hierarchy_desc'   => $item['prod_hierarchy_desc'] ?? null,
            'acc_ass_cat'           => $item['acc_ass_cat'] ?? null,
            'vendor_name'           => $item['vendor_name'] ?? null,
            'customer_name'         => $item['customer_name'] ?? null,
            'stor_loc'              => $item['stor_loc'] ?? null,
            'sloc_desc'             => $item['sloc_desc'] ?? null,
            'valuation'             => $item['valuation'] ?? null,
            'po_item_qty'           => $item['po_item_qty'] ?? null,
            'net_order_price'       => $netOrderPrice,
            'currency'              => $currency,
            'price_idr'             => $priceIDR,
            'price_date'            => $apiDate,
        ]);

        $query        = PurchaseOrderDetail::where('purchase_order_id', $checkPO->id);
        $salesDocsQty = (clone $query)->distinct('sales_doc')->count();
        $materialQty  = (clone $query)->distinct('material')->count();
        $itemQty      = (clone $query)->sum('po_item_qty');

        PurchaseOrder::where('id', $checkPO->id)->update([
            'sales_doc_qty' => $salesDocsQty,
            'material_qty'  => $materialQty,
            'item_qty'      => $itemQty,
        ]);
    }

    public function changeStatusPurchaseOrder(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->post('type') == 'approved') {
            PurchaseOrder::find($request->post('id'))->update([
                'status' => 'open'
            ]);
        } else {
            PurchaseOrder::find($request->post('id'))->update([
                'status' => 'cancel'
            ]);
        }

        return response()->json([
            'status' => true
        ]);
    }

    public function purchaseOrderDetail(Request $request): View
    {
        $purchaseOrder = PurchaseOrder::with('user')->find($request->query('id'));
        $products = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))->get();

        $title = 'Purchase Order';
        return view('inbound.purchase-order.detail', compact('title', 'products', 'purchaseOrder'));
    }

    public function qualityControl(Request $request): View
    {
        $purchaseOrder = PurchaseOrder::with('vendor', 'customer', 'user');

        if ($request->query('purcDoc') != null) {
            $purchaseOrder = $purchaseOrder->where('purc_doc', $request->query('purcDoc'));
        }

        if ($request->query('vendor') != null) {
            $purchaseOrder = $purchaseOrder->where('vendor_id', $request->query('vendor'));
        }

        if ($request->query('customer') != null) {
            $purchaseOrder = $purchaseOrder->where('customer_id', $request->query('customer'));
        }

        if ($request->query('date') != null) {
            $purchaseOrder = $purchaseOrder->where('created_at', $request->query('date'));
        }

        $purchaseOrder = $purchaseOrder->latest()
            ->whereIn('status', ['open', 'process'])
            ->paginate(10);

        $vendor = Vendor::all();
        $customer = Customer::all();

        $title = "Quality Control";
        return view('inbound.quality-control.index', compact('title', 'purchaseOrder', 'vendor', 'customer'));
    }

    public function qualityControlList(Request $request): View
    {
        $purchaseOrder = PurchaseOrder::find($request->query('id'));
        $sales_doc = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))
            ->groupBy('sales_doc')
            ->select([
                'sales_doc',
            ])
            ->get();

        foreach ($sales_doc as $doc) {
            $doc->product_qty = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))
                ->where('sales_doc', $doc->sales_doc)
                ->count();
            $doc->item_qty = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))
                ->where('sales_doc', $doc->sales_doc)
                ->sum('po_item_qty');

            $status = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))
                ->where('sales_doc', $doc->sales_doc)
                ->where('status', 'new')
                ->count();

            $doc->status = $status == 0 ? 'done' : 'process';
        }

        $title = "Quality Control";
        return view('inbound.quality-control.process', compact('title', 'sales_doc', 'purchaseOrder'));
    }

    public function qualityControlProcess(Request $request): View
    {
        $purchaseOrder = PurchaseOrder::find($request->query('id'));
        $data = PurchaseOrderDetail::where('purchase_order_id', $purchaseOrder->id)
            ->where('status', 'new')
            ->orderBy('product_id')
            ->get();

        $products = [];
        foreach ($data as $item) {
            if ($item->po_item_qty > $item->qty_qc) {
                $products[] = [
                    'id'        => $item->id,
                    'sku'       => $item->material,
                    'name'      => $item->po_item_desc,
                    'type'      => $item->prod_hierarchy_desc,
                    'qty'       => $item->po_item_qty - $item->qty_qc,
                    'item'      => $item->item,
                    'qty_qc'    => 0,
                    'qty_qc_done' => $item->qty_qc,
                    'sales_doc' => $item->sales_doc,
                ];
            }
        }

        $title = "Quality Control";
        return view('inbound.quality-control.qc', compact('title', 'request', 'products', 'purchaseOrder'));
    }

    public function qualityControlStoreProcess(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            Log::info(json_encode($request->all()));

            $purchaseOrder = PurchaseOrder::find($request->post('purchaseOrderId'));

            foreach ($request->post('qualityControl') as $products) {
                $directOutbound = false;

                $productPackage = ProductPackage::create([
                    'purchase_order_id' => $request->post('purchaseOrderId'),
                    'qty_item'          => count($products),
                    'qty'               => 0,
                    'status'            => 'open',
                    'created_by'        => Auth::id(),
                ]);

                $qty = 0;
                foreach ($products as $product) {
                    $purchaseOrderDetail = PurchaseOrderDetail::where('id', $product['id'])->first();
                    $productPackageItem = ProductPackageItem::create([
                        'product_package_id'        => $productPackage->id,
                        'product_id'                => $purchaseOrderDetail->product_id,
                        'purchase_order_detail_id'  => $product['id'],
                        'is_parent'                 => $product['parent'],
                        'qty'                       => $product['qty'] - $product['qtyDirect']
                    ]);

                    foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                        ProductPackageItemSN::create([
                            'product_package_item_id'   => $productPackageItem->id,
                            'serial_number'             => $serialNumber,
                        ]);
                    }

                    PurchaseOrderDetail::where('id', $product['id'])->increment('qty_qc', $product['qty']);
                    $purchaseOrderDetail = PurchaseOrderDetail::where('id', $product['id'])->first();
                    if ($purchaseOrderDetail->qty_qc == $purchaseOrderDetail->po_item_qty) {
                        $status = 'qc';
                    } else {
                        $status = 'new';
                    }
                    PurchaseOrderDetail::where('id', $product['id'])->update(['status' => $status]);

                    $qty += $product['qty'];

                    if ($product['putAwayStep'] == 0) {
                        $directOutbound = true;

                        if (count($product['SnDirect']) == $product['qty']) {
                            ProductPackage::where('id', $productPackage->id)->update(['status' => 'done']);
                        }
                    }
                }

                ProductPackage::where('id', $productPackage->id)->update([
                    'qty' => $qty,
                ]);


                // Create Direct Outbound langsung di inventory
                if ($directOutbound) {
                    $inventoryPackage = InventoryPackage::create([
                        'purchase_order_id' => $request->post('purchaseOrderId'),
                        'storage_id'        => 1,
                        'number'            => 'PA-'.date('YmdHis').rand(100, 999),
                        'reff_number'       => 'Cross Docking',
                        'qty_item'          => 0,
                        'qty'               => 0,
                        'sales_docs'        => json_encode([]),
                        'product_package_id'=> $productPackage->id,
                        'created_by'        => Auth::id(),
                    ]);

                    $qtyItemDirect = 0;
                    $qtyDirect = 0;
                    $salesDocsDirect = [];

                    // Insert Inventory Package Item
                    foreach ($products as $product) {
                        if ($product['putAwayStep'] == 0) {
                            $purchaseOrderDetail = PurchaseOrderDetail::where('id', $product['id'])->first();

                            $qtyDirectSN = count($product['SnDirect'] ?? []);

                            $productPackageItem = ProductPackageItem::create([
                                'product_package_id'        => $productPackage->id,
                                'product_id'                => $purchaseOrderDetail->product_id,
                                'purchase_order_detail_id'  => $product['id'],
                                'is_parent'                 => $product['parent'],
                                'direct_outbound'           => 1,
                                'qty'                       => $qtyDirectSN
                            ]);

                            $inventoryPackageItem = InventoryPackageItem::create([
                                'inventory_package_id'      => $inventoryPackage->id,
                                'product_id'                => $purchaseOrderDetail->product_id,
                                'purchase_order_detail_id'  => $purchaseOrderDetail->id,
                                'is_parent'                 => $product['parent'],
                                'direct_outbound'           => 1,
                                'qty'                       => $qtyDirectSN,
                            ]);

                            // Insert Inventory Package Item SN
                            foreach ($product['SnDirect'] ?? [] as $snDirect) {
                                InventoryPackageItemSN::create([
                                    'inventory_package_item_id' => $inventoryPackageItem->id,
                                    'serial_number'             => $snDirect,
                                    'qty'                       => 1
                                ]);

                                ProductPackageItemSN::create([
                                    'product_package_item_id'   => $productPackageItem->id,
                                    'serial_number'             => $snDirect,
                                ]);
                            }

                            // Tambah Stock Inventory
                            $checkInventory = Inventory::where('purchase_order_id', $request->post('purchaseOrderId'))->where('type', 'inv')->first();
                            if ($checkInventory != null) {
                                Inventory::where('id', $checkInventory->id)->increment('stock', $qtyDirectSN);
                                $inventoryId = $checkInventory->id;
                            } else {
                                $inventory = Inventory::create([
                                    'purchase_order_id' => $request->post('purchaseOrderId'),
                                    'stock'             => $qtyDirectSN,
                                    'type'              => 'inv'
                                ]);
                                $inventoryId = $inventory->id;
                            }

                            InventoryDetail::create([
                                'inventory_id'              => $inventoryId,
                                'purchase_order_detail_id'  => $purchaseOrderDetail->id,
                                'storage_id'                => 1,
                                'inventory_package_item_id' => $inventoryPackageItem->id,
                                'sales_doc'                 => $purchaseOrderDetail->sales_doc,
                                'qty'                       => $qtyDirectSN,
                            ]);

                            $checkInventoryItem = InventoryItem::where('purc_doc', $purchaseOrder->purc_doc)
                                ->where('sales_doc', $purchaseOrderDetail->sales_doc)
                                ->where('product_id', $purchaseOrderDetail->product_id)
                                ->where('storage_id', 1)
                                ->where('type', 'inv')
                                ->first();
                            if ($checkInventoryItem != null) {
                                InventoryItem::where('id', $checkInventoryItem->id)->increment('stock', $qtyDirectSN);
                            } else {
                                InventoryItem::create([
                                    'purc_doc'      => $purchaseOrder->purc_doc,
                                    'sales_doc'     => $purchaseOrderDetail->sales_doc,
                                    'product_id'    => $purchaseOrderDetail->product_id,
                                    'storage_id'    => 1,
                                    'stock'         => $qtyDirectSN,
                                    'type'          => 'inv'
                                ]);
                            }

                            // Inventory History
                            InventoryHistory::create([
                                'purchase_order_id'             => $purchaseOrder->id,
                                'purchase_order_detail_id'      => $purchaseOrderDetail->id,
                                'inventory_package_item_id'     => $inventoryPackageItem->id,
                                'qty'                           => $qtyDirectSN,
                                'type'                          => 'inbound',
                                'serial_number'                 => json_encode($product['SnDirect']),
                                'created_by'                    => Auth::id()
                            ]);

                            $qtyItemDirect++;
                            $qtyDirect += $qtyDirectSN;
                            $salesDocsDirect[] = $purchaseOrderDetail->sales_doc;
                        }
                    }

                    InventoryPackage::where('id', $inventoryPackage->id)->update([
                        'qty_item'          => $qtyItemDirect,
                        'qty'               => $qtyDirect,
                        'sales_docs'        => json_encode(array_unique($salesDocsDirect)),
                    ]);
                }
            }

            // Check Purchase Order
            $checkPO = PurchaseOrderDetail::where('purchase_order_id', $request->post('purchaseOrderId'))
                ->where('status', 'new')
                ->count();
            if ($checkPO == 0) {
                $status = 'done';
            } else {
                $status = 'process';
            }

            PurchaseOrder::where('id', $request->post('purchaseOrderId'))->update([
                'status' => $status
            ]);

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

    public function putAway(Request $request): View
    {
        $putAway = ProductPackage::with('purchaseOrder', 'purchaseOrder.customer')->latest()->paginate(10);

        foreach ($putAway as $product) {
            $productPackageItemParent = ProductPackageItem::with('product')->where('product_package_id', $product->id)->where('is_parent', 1)->first();
            $productPackageItem = ProductPackageItem::with('purchaseOrderDetail')->where('product_package_id', $product->id)->get();

            $sales_docs = [];
            foreach ($productPackageItem as $item) {
                $sales_docs[] = $item->purchaseOrderDetail->sales_doc;
            }

            $product->purchase_order = '';
            $product->sales_doc = array_unique($sales_docs);
            $product->product = $productPackageItemParent;
        }

        $title = 'Put Away';
        return view('inbound.put-away.index', compact('title', 'putAway'));
    }

    public function putAwayDetail(Request $request): View
    {
        $products = InventoryPackage::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'storage', 'purchaseOrder', 'purchaseOrder.customer')->where('product_package_id', $request->query('id'))->get();

        $title = 'Put Away';
        return view('inbound.put-away.detail', compact('title', 'products'));
    }

    public function putAwayDetailOpen(Request $request): View
    {
        $products = ProductPackage::with('productPackageItem', 'productPackageItem.purchaseOrderDetail', 'productPackageItem.productPackageItemSn', 'purchaseOrder')->where('id', $request->query('id'))->get();

        $title = 'Put Away';
        return view('inbound.put-away.detail-open', compact('title', 'products'));
    }

    public function putAwayProcess(Request $request): View
    {
        $products = ProductPackageItem::with([
                'purchaseOrderDetail' => function ($purchaseOrderDetail) {
                    $purchaseOrderDetail->select([
                        'id', 'item', 'material', 'po_item_desc', 'prod_hierarchy_desc', 'sales_doc'
                    ]);
                },
                'productPackageItemSn'
            ])->where('product_package_id', $request->query('id'))
            ->get();

        $storageRaw = Storage::where('raw', '!=', '-')
            ->where('area', null)
            ->where('rak', null)
            ->where('bin', null)
            ->whereNotIn('id', [1,2,3,4])
            ->get();

        $title = 'Put Away';
        return view('inbound.put-away.process', compact('title', 'products', 'storageRaw'));
    }

    public function putAwayEdit(Request $request): View
    {
        $products = ProductPackageItem::with('productPackageItemSn', 'purchaseOrderDetail')
            ->where('product_package_id', $request->query('id'))
            ->orderBy('is_parent', 'desc')
            ->get();

        $title = 'Put Away';
        return view('inbound.put-away.edit', compact('title', 'products'));
    }

    public function findSerialNumber(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->get('type') == 'parent') {
            $serialNumber = SerialNumber::where('product_parent_id', $request->get('id'))->where('product_parent_detail_id', $request->get('detail'))->select(['id', 'serial_number'])->get();
        } else {
            $serialNumber = SerialNumber::where('product_child_id', $request->get('id'))->where('product_child_detail_id', $request->get('detail'))->select(['id', 'serial_number'])->get();
        }

        return response()->json([
            'data' => $serialNumber
        ]);
    }

    public function findSNInventory(Request $request): \Illuminate\Http\JsonResponse
    {
        $serialNumber = InventoryPackageItemSN::where('inventory_package_item_id', $request->get('id'))->get();

        return response()->json([
            'data' => $serialNumber
        ]);
    }

    public function purchaseOrderSerialNumber(Request $request): View
    {
        $purchaseOrder = PurchaseOrder::find($request->query('id'));

        $title = 'Purchase Order';
        return view('inbound.purchase-order.serial-number', compact('title', 'purchaseOrder'));
    }

    public function putAwayStore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $listBox = $request->post('box');
            $putAwayNumber = 'PA-'.date('ymdHis').rand(111, 999);
            $numberBox = 1;
            $totalBox = count($listBox);
            $stock = 0;

            $productPackage = ProductPackage::find($request->post('productPackageId'));
            $checkInventory = Inventory::where('purchase_order_id', $productPackage->purchase_order_id)->where('type', 'inv')->first();
            if ($checkInventory == null) {
                $inventory = Inventory::create([
                    'purchase_order_id'     => $productPackage->purchase_order_id,
                    'stock'                 => 0,
                    'type'                  => 'inv',
                ]);

                $inventoryId = $inventory->id;
            } else {
                $inventoryId = $checkInventory->id;
            }

            foreach ($listBox as $box) {
                $salesDocs = [];
                $qtyItem = 0;
                $qty = 0;

                $inventoryPackage = InventoryPackage::create([
                    'purchase_order_id'     => $productPackage->purchase_order_id,
                    'storage_id'            => $request->post('bin'),
                    'number'                => $putAwayNumber.'-'.$numberBox,
                    'reff_number'           => $numberBox.' of '.$totalBox,
                    'qty_item'              => 0,
                    'qty'                   => 0,
                    'sales_docs'            => json_encode([]),
                    'product_package_id'    => $productPackage->id,
                    'created_by'            => Auth::id()
                ]);

                foreach ($box['parent'] ?? [] as $parent) {
                    $inventoryPackageItem = InventoryPackageItem::create([
                        'inventory_package_id'      => $inventoryPackage->id,
                        'product_id'                => $parent['productId'],
                        'purchase_order_detail_id'  => $parent['purchaseOrderDetailId'],
                        'is_parent'                 => 1,
                        'qty'                       => $parent['qtySelect']
                    ]);

                    foreach ($parent['serialNumber'] ?? [] as $serialNumber) {
                        InventoryPackageItemSN::create([
                            'inventory_package_item_id' => $inventoryPackageItem->id,
                            'serial_number'             => $serialNumber,
                            'qty'                       => 1
                        ]);
                    }

                    InventoryDetail::create([
                        'inventory_id'              => $inventoryId,
                        'purchase_order_detail_id'  => $parent['purchaseOrderDetailId'],
                        'storage_id'                => $request->post('bin'),
                        'inventory_package_item_id' => $inventoryPackageItem->id,
                        'sales_doc'                 => $parent['salesDoc'],
                        'qty'                       => $parent['qtySelect'],
                        'aging_date'                => date('Y-m-d H:i:s'),
                    ]);

                    InventoryHistory::create([
                        'purchase_order_id'             => $productPackage->purchase_order_id,
                        'purchase_order_detail_id'      => $parent['purchaseOrderDetailId'],
                        'inventory_package_item_id'     => $inventoryPackageItem->id,
                        'qty'                           => $parent['qtySelect'],
                        'type'                          => 'inbound',
                        'serial_number'                 => json_encode($parent['serialNumber']),
                        'created_by'                    => Auth::id()
                    ]);

                    $salesDocs[] = $parent['salesDoc'];
                    $stock += $parent['qtySelect'];
                    $qtyItem++;
                    $qty += $parent['qtySelect'];
                }

                foreach ($box['child'] ?? [] as $child) {
                    $inventoryPackageItem = InventoryPackageItem::create([
                        'inventory_package_id'      => $inventoryPackage->id,
                        'product_id'                => $child['productId'],
                        'purchase_order_detail_id'  => $child['purchaseOrderDetailId'],
                        'is_parent'                 => 0,
                        'qty'                       => $child['qtySelect'],
                    ]);

                    foreach ($child['serialNumber'] ?? [] as $serialNumber) {
                        InventoryPackageItemSN::create([
                            'inventory_package_item_id' => $inventoryPackageItem->id,
                            'serial_number'             => $serialNumber,
                            'qty'                       => 1
                        ]);
                    }

                    InventoryDetail::create([
                        'inventory_id'              => $inventoryId,
                        'purchase_order_detail_id'  => $child['purchaseOrderDetailId'],
                        'storage_id'                => $request->post('bin'),
                        'inventory_package_item_id' => $inventoryPackageItem->id,
                        'sales_doc'                 => $child['salesDoc'],
                        'qty'                       => $child['qtySelect'],
                        'aging_date'                => date('Y-m-d H:i:s'),
                    ]);

                    InventoryHistory::create([
                        'purchase_order_id'             => $productPackage->purchase_order_id,
                        'purchase_order_detail_id'      => $child['purchaseOrderDetailId'],
                        'inventory_package_item_id'     => $inventoryPackageItem->id,
                        'qty'                           => $child['qtySelect'],
                        'type'                          => 'inbound',
                        'serial_number'                 => json_encode($child['serialNumber']),
                        'created_by'                    => Auth::id()
                    ]);

                    $salesDocs[] = $child['salesDoc'];
                    $stock += $child['qtySelect'];
                    $qtyItem++;
                    $qty += $child['qtySelect'];
                }

                InventoryPackage::where('id', $inventoryPackage->id)->update([
                    'sales_docs'            => json_encode(array_unique($salesDocs)),
                    'qty_item'              => $qtyItem,
                    'qty'                   => $qty
                ]);

                $numberBox++;
            }

            // Inventory
            Inventory::where('id', $inventoryId)->increment('stock', $stock);

            ProductPackage::where('id', $productPackage->id)->update([
                'status'    => 'done'
            ]);

            DB::commit();
            return response()->json([
                'status'    => true,
                'data'      => $request->post('productPackageId')
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

    public function qualityControlProcessCcw(Request $request): View
    {
        $purcDocDetail = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))->where('status', 'new')->get();

        $title = 'Quality Control';
        return view('inbound.quality-control.ccw.index', compact('title', 'purcDocDetail'));
    }

    public function qualityControlStoreProcessCcw(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $fileName = $request->post('fileName');
            $path = storage_path('app/private/json_uploads/' . $fileName);
            $compare = json_decode(file_get_contents($path), true);
            $purchaseOrder = PurchaseOrder::find($request->post('purchaseOrderId'));

            $grouped = [];
            $parents = [];

            foreach ($compare as $item) {
                if (preg_match('/^\d+\.0$/', $item['lineNumber'])) {
                    $key = explode('.', $item['lineNumber'])[0];
                    $parents[$key] = [
                        'parent' => $item,
                        'children' => []
                    ];
                }
            }

            foreach ($compare as $item) {
                if (preg_match('/^(\d+)\.0\./', $item['lineNumber'], $match)) {
                    $key = $match[1];
                    if (isset($parents[$key])) {
                        $parents[$key]['children'][] = $item;
                    }
                }
            }

            $grouped = array_values($parents);

            foreach ($grouped as $item) {
                $directOutbound = false;
                $qty_item = 0;
                $qty = 0;
                $productPackage = ProductPackage::create([
                    'purchase_order_id' => $request->post('purchaseOrderId'),
                    'qty_item'          => 0,
                    'qty'               => 0,
                    'status'            => 'open',
                    'created_by'        => Auth::id()
                ]);

                if ($item['parent']['putAwayStep'] == 0) {
                    $directOutbound = true;
                }

                foreach ($item['parent']['salesDoc'] as $parent) {
                    $purchaseOrderDetail = PurchaseOrderDetail::find($parent['id']);
                    $productPackageItem = ProductPackageItem::create([
                        'product_package_id'        => $productPackage->id,
                        'product_id'                => $purchaseOrderDetail->product_id,
                        'purchase_order_detail_id'  => $parent['id'],
                        'is_parent'                 => 1,
                        'qty'                       => $parent['qty'],
                    ]);

                    foreach ($parent['serialNumber'] ?? [] as $serialNumber) {
                        ProductPackageItemSN::create([
                            'product_package_item_id'  => $productPackageItem->id,
                            'serial_number'            => $serialNumber == "" ? rand(111111,999999) : $serialNumber,
                        ]);
                    }

                    PurchaseOrderDetail::where('id', $parent['id'])
                        ->update([
                            'status' => 'qc',
                            'qty_qc' => $parent['qty'],
                        ]);

                    $qty_item++;
                    $qty += $parent['qty'];
                }

                foreach ($item['children'] as $child) {
                    foreach ($child['salesDoc'] as $childDetail) {
                        $purchaseOrderDetail = PurchaseOrderDetail::find($childDetail['id']);
                        $productPackageItem = ProductPackageItem::create([
                            'product_package_id'        => $productPackage->id,
                            'product_id'                => $purchaseOrderDetail->product_id,
                            'purchase_order_detail_id'  => $childDetail['id'],
                            'is_parent'                 => 0,
                            'qty'                       => $childDetail['qty'],
                        ]);

                        foreach ($childDetail['serialNumber'] ?? [] as $serialNumber) {
                            ProductPackageItemSN::create([
                                'product_package_item_id'  => $productPackageItem->id,
                                'serial_number'            => $serialNumber == "" ? rand(111111,999999) : $serialNumber,
                            ]);
                        }

                        PurchaseOrderDetail::where('id', $childDetail['id'])
                            ->update([
                                'status' => 'qc',
                                'qty_qc' => $childDetail['qty'],
                            ]);

                        $qty_item++;
                        $qty += $childDetail['qty'];
                    }

                    if ($child['putAwayStep'] == 0) {
                        $directOutbound = true;
                    }
                }

                ProductPackage::where('id', $productPackage->id)->update([
                    'qty_item'  => $qty_item,
                    'qty'       => $qty,
                ]);

                // Direct Outbound
                if ($directOutbound) {
                    $inventoryPackage = InventoryPackage::create([
                        'purchase_order_id' => $request->post('purchaseOrderId'),
                        'storage_id'        => 1,
                        'number'            => 'PA-'.date('YmdHis').rand(100, 999),
                        'reff_number'       => 'Cross Docking',
                        'qty_item'          => 0,
                        'qty'               => 0,
                        'sales_docs'        => json_encode([]),
                        'product_package_id'=> $productPackage->id,
                        'created_by'        => Auth::id(),
                    ]);

                    $qtyItemDirect = 0;
                    $qtyDirect = 0;
                    $salesDocsDirect = [];

                    // Parent
                    if ($item['parent']['putAwayStep'] == 0) {
                        foreach ($item['parent']['salesDoc'] as $salesDoc) {
                            if ($salesDoc['qtyDirect'] != 0) {

                                $purchaseOrderDetail = PurchaseOrderDetail::find($salesDoc['id']);
                                $inventoryPackageItem = InventoryPackageItem::create([
                                    'inventory_package_id'      => $inventoryPackage->id,
                                    'product_id'                => $purchaseOrderDetail->product_id,
                                    'purchase_order_detail_id'  => $purchaseOrderDetail->id,
                                    'is_parent'                 => 0,
                                    'direct_outbound'           => 1,
                                    'qty'                       => $salesDoc['qtyDirect'],
                                ]);

                                // Serial Number
                                foreach ($salesDoc['snDirect'] ?? [] as $serialNumber) {
                                    InventoryPackageItemSN::create([
                                        'inventory_package_item_id' => $inventoryPackageItem->id,
                                        'serial_number'             => $serialNumber,
                                        'qty'                       => 1
                                    ]);
                                }

                                // Inventory
                                $checkInventory = Inventory::where('purchase_order_id', $purchaseOrderDetail->purchase_order_id)->where('type', 'inv')->first();
                                if ($checkInventory != null) {
                                    Inventory::where('id', $checkInventory->id)->increment('stock', $salesDoc['qtyDirect']);
                                    $inventoryId = $checkInventory->id;
                                } else {
                                    $inventory = Inventory::create([
                                        'purchase_order_id' => $purchaseOrderDetail->purchase_order_id,
                                        'stock'             => $salesDoc['qtyDirect'],
                                        'type'              => 'inv'
                                    ]);
                                    $inventoryId = $inventory->id;
                                }

                                InventoryDetail::create([
                                    'inventory_id'              => $inventoryId,
                                    'purchase_order_detail_id'  => $purchaseOrderDetail->id,
                                    'storage_id'                => 1,
                                    'inventory_package_item_id' => $inventoryPackageItem->id,
                                    'sales_doc'                 => $purchaseOrderDetail->sales_doc,
                                    'qty'                       => $salesDoc['qtyDirect'],
                                ]);

                                $checkInventoryItem = InventoryItem::where('purc_doc', $purchaseOrder->purc_doc)
                                    ->where('sales_doc', $purchaseOrderDetail->sales_doc)
                                    ->where('product_id', $purchaseOrderDetail->product_id)
                                    ->where('storage_id', 1)
                                    ->where('type', 'inv')
                                    ->first();
                                if ($checkInventoryItem != null) {
                                    InventoryItem::where('id', $checkInventoryItem->id)->increment('stock', $salesDoc['qtyDirect']);
                                } else {
                                    InventoryItem::create([
                                        'purc_doc'      => $purchaseOrder->purc_doc,
                                        'sales_doc'     => $purchaseOrderDetail->sales_doc,
                                        'product_id'    => $purchaseOrderDetail->product_id,
                                        'storage_id'    => 1,
                                        'stock'         => $salesDoc['qtyDirect'],
                                        'type'          => 'inv'
                                    ]);
                                }

                                // Inventory History
                                InventoryHistory::create([
                                    'purchase_order_id'             => $purchaseOrder->id,
                                    'purchase_order_detail_id'      => $purchaseOrderDetail->id,
                                    'inventory_package_item_id'     => $inventoryPackageItem->id,
                                    'qty'                           => $salesDoc['qtyDirect'],
                                    'type'                          => 'inbound',
                                    'serial_number'                 => json_encode($salesDoc['snDirect']),
                                    'created_by'                    => Auth::id()
                                ]);

                                $qtyDirect = $salesDoc['qtyDirect'];
                                $qtyItemDirect++;
                                $salesDocsDirect[] = $salesDoc['salesDoc'];
                            }
                        }
                    }

                    // Child
                    foreach ($item['children'] as $child) {
                        if ($child['putAwayStep'] == 0) {
                            foreach ($child['salesDoc'] as $salesDoc) {
                                if ($salesDoc['qtyDirect'] != 0) {

                                    $purchaseOrderDetail = PurchaseOrderDetail::find($salesDoc['id']);
                                    $inventoryPackageItem = InventoryPackageItem::create([
                                        'inventory_package_id'      => $inventoryPackage->id,
                                        'product_id'                => $purchaseOrderDetail->product_id,
                                        'purchase_order_detail_id'  => $purchaseOrderDetail->id,
                                        'is_parent'                 => 0,
                                        'direct_outbound'           => 1,
                                        'qty'                       => $salesDoc['qtyDirect'],
                                    ]);

                                    // Serial Number
                                    foreach ($salesDoc['snDirect'] ?? [] as $serialNumber) {
                                        InventoryPackageItemSN::create([
                                            'inventory_package_item_id' => $inventoryPackageItem->id,
                                            'serial_number'             => $serialNumber,
                                            'qty'                       => 1
                                        ]);
                                    }

                                    // Inventory
                                    $checkInventory = Inventory::where('purchase_order_id', $purchaseOrderDetail->purchase_order_id)->where('type', 'inv')->first();
                                    if ($checkInventory != null) {
                                        Inventory::where('id', $checkInventory->id)->increment('stock', $salesDoc['qtyDirect']);
                                        $inventoryId = $checkInventory->id;
                                    } else {
                                        $inventory = Inventory::create([
                                            'purchase_order_id' => $purchaseOrderDetail->purchase_order_id,
                                            'stock'             => $salesDoc['qtyDirect'],
                                            'type'              => 'inv'
                                        ]);
                                        $inventoryId = $inventory->id;
                                    }

                                    InventoryDetail::create([
                                        'inventory_id'              => $inventoryId,
                                        'purchase_order_detail_id'  => $purchaseOrderDetail->id,
                                        'storage_id'                => 1,
                                        'inventory_package_item_id' => $inventoryPackageItem->id,
                                        'sales_doc'                 => $purchaseOrderDetail->sales_doc,
                                        'qty'                       => $salesDoc['qtyDirect'],
                                    ]);

                                    $checkInventoryItem = InventoryItem::where('purc_doc', $purchaseOrder->purc_doc)
                                        ->where('sales_doc', $purchaseOrderDetail->sales_doc)
                                        ->where('product_id', $purchaseOrderDetail->product_id)
                                        ->where('storage_id', 1)
                                        ->where('type', 'inv')
                                        ->first();
                                    if ($checkInventoryItem != null) {
                                        InventoryItem::where('id', $checkInventoryItem->id)->increment('stock', $salesDoc['qtyDirect']);
                                    } else {
                                        InventoryItem::create([
                                            'purc_doc'      => $purchaseOrder->purc_doc,
                                            'sales_doc'     => $purchaseOrderDetail->sales_doc,
                                            'product_id'    => $purchaseOrderDetail->product_id,
                                            'storage_id'    => 1,
                                            'stock'         => $salesDoc['qtyDirect'],
                                            'type'          => 'inv'
                                        ]);
                                    }

                                    // Inventory History
                                    InventoryHistory::create([
                                        'purchase_order_id'             => $purchaseOrder->id,
                                        'purchase_order_detail_id'      => $purchaseOrderDetail->id,
                                        'inventory_package_item_id'     => $inventoryPackageItem->id,
                                        'qty'                           => $salesDoc['qtyDirect'],
                                        'type'                          => 'inbound',
                                        'serial_number'                 => json_encode($salesDoc['snDirect']),
                                        'created_by'                    => Auth::id()
                                    ]);

                                    $qtyDirect = $salesDoc['qtyDirect'];
                                    $qtyItemDirect++;
                                    $salesDocsDirect[] = $salesDoc['salesDoc'];
                                }
                            }
                        }
                    }

                    InventoryPackage::where('id', $inventoryPackage->id)->update([
                        'qty_item'  => $qtyItemDirect,
                        'qty'       => $qtyDirect,
                        'sales_docs'=> json_encode(array_unique($salesDocsDirect)),
                    ]);
                }

            }

            // Check Purchase Order
            $checkPO = PurchaseOrderDetail::where('purchase_order_id', $request->post('purchaseOrderId'))
                ->where('status', 'new')
                ->count();
            if ($checkPO == 0) {
                $status = 'done';
            } else {
                $status = 'process';
            }

            PurchaseOrder::where('id', $request->post('purchaseOrderId'))->update([
                'status' => $status
            ]);

            DB::commit();
            return response()->json([
                'status' => true
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status' => false
            ]);
        }
    }

    public function uploadFileCCW(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $file = $request->file('json_file');

        if (!$file || !$file->isValid()) {
            return back()->withErrors(['json_file' => 'File tidak valid.']);
        }

        $fileName = time() . rand(1000, 9999) . '.json';

        $file->storeAs('json_uploads', $fileName);

        $data = json_decode(file_get_contents($file), true);
        $total = is_array($data) ? count($data) : 0;

        return response()->json([
            'fileName'  => $fileName,
            'total'     => $total,
            'data'      => $data,
        ]);
    }

    public function editPurchaseOrder(Request $request): View
    {
        $purchaseOrder = PurchaseOrderEditReq::with('requestBy', 'approvedBy')->latest()->paginate(10);

        $title = 'Purchase Order';
        return view('inbound.purchase-order.edit.index', compact('title', 'purchaseOrder'));
    }

    public function editPurchaseOrderProduct(): View
    {
        $purchaseOrder = PurchaseOrder::whereIn('status', ['new', 'open', 'process'])->get();

        $title = 'Purchase Order';
        return view('inbound.purchase-order.edit.product', compact('title', 'purchaseOrder'));
    }

    public function listMaterialEditPO(Request $request): \Illuminate\Http\JsonResponse
    {
        $purchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $request->get('id'))->where('status', 'new')->get();

        return response()->json([
            'data' => $purchaseOrderDetail
        ]);
    }

    public function editPurchaseOrderRequestEdit(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            foreach ($request->post('editProducts') as $requestEdit) {
                PurchaseOrderEditReq::create([
                    'purchase_order_id' => $requestEdit['data']['purchase_order_id'],
                    'request_by'        => Auth::id(),
                    'type'              => $requestEdit['ket'],
                    'note'              => $requestEdit['data']['note'],
                    'status'            => 'pending',
                    'details'           => json_encode($requestEdit['data'])
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => true
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status' => false
            ]);
        }
    }

    public function editPurchaseOrderDetail(Request $request): View
    {
        $purchaseOrder = PurchaseOrderEditReq::with('requestBy', 'approvedBy')->find($request->query('id'));

        $title = 'Purchase Order';
        return view('inbound.purchase-order.edit.detail', compact('title', 'purchaseOrder'));
    }

    public function editPurchaseOrderApproved(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            PurchaseOrderEditReq::where('id', $request->get('id'))->update([
                'status'        => 'approved',
                'approved_by'   => Auth::id(),
                'approved_at'   => date('Y-m-d H:i:s'),
            ]);

            // Simpan hasil perubahan
            $detail = PurchaseOrderEditReq::find($request->get('id'));

            if ($detail->type == 'delete') {
                // Delete Purchase Order Detail
                PurchaseOrderDetail::where('id', $detail->id)->delete();
            } else {
                // Edit Purchase Order Detail
                $data = json_decode($detail->details, true);
                unset($data['note']);

                PurchaseOrderDetail::where('id', $detail->id)
                    ->update($data);
            }

            $query = PurchaseOrderDetail::where('purchase_order_id', $detail->purchase_order_id);
            $salesDocsQty = (clone $query)->distinct('sales_doc')->count();
            $materialQty  = (clone $query)->distinct('material')->count();
            $itemQty = (clone $query)->sum('po_item_qty');

            PurchaseOrder::where('id', $detail->purchase_order_id)->update([
                'sales_doc_qty'  => $salesDocsQty,
                'material_qty'   => $materialQty,
                'item_qty'       => $itemQty,
            ]);

            DB::commit();
            return response()->json([
                'status' => true
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status' => false
            ]);
        }
    }

    public function editPurchaseOrderCancel(Request $request): \Illuminate\Http\JsonResponse
    {
        PurchaseOrderEditReq::where('id', $request->get('id'))->update([
            'status' => 'cancel'
        ]);

        return response()->json([
            'status' => true
        ]);
    }

    public function indexMobile(Request $request): View
    {
        $purchaseOrder = PurchaseOrder::with('customer', 'user', 'purchaseOrderDetail')
            ->when($request->query('purcDoc'), function ($q) use ($request) {
                $q->where('purc_doc', 'LIKE', '%'.$request->query('purcDoc').'%');
            })
            ->whereHas('customer', function ($customer) use ($request) {
                if ($request->query('customer') != null) {
                    $customer->where('name', 'LIKE', '%'.$request->query('customer').'%');
                }
            })
            ->whereHas('purchaseOrderDetail', function ($purchaseOrderDetail) use ($request) {
                if ($request->query('salesDoc') != null) {
                    $purchaseOrderDetail->where('sales_doc', 'LIKE', '%'.$request->query('salesDoc').'%');
                }

                if ($request->query('material') != null) {
                    $purchaseOrderDetail->where('material', 'LIKE', '%'.$request->query('material').'%');
                }
            })
            ->latest()
            ->paginate(10)
            ->appends([
                'purcDoc'   => $request->query('purcDoc'),
                'salesDoc'  => $request->query('salesDoc'),
                'material'  => $request->query('material'),
                'customer'  => $request->query('customer'),
                'search'    => $request->query('search')
            ]);

        $customer = Customer::all();
        $products = Product::all();

        return view('mobile.inbound.index', compact('purchaseOrder', 'customer', 'products'));
    }

    public function indexDetailMobile(Request $request): View
    {
        $purchaseOrder = PurchaseOrder::with('customer', 'user')->where('id', $request->query('id'))->first();
        $purchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))
            ->when($request->query('salesDoc'), function ($q) use ($request) {
                $q->where('sales_doc', 'LIKE', '%'.$request->query('salesDoc').'%');
            })
            ->when($request->query('material'), function ($q) use ($request) {
                $q->where('material', 'LIKE', '%'.$request->query('material').'%');
            })
            ->select([
                'sales_doc',
                DB::raw('SUM(po_item_qty) as qty'),
                DB::raw('count(sales_doc) as qtyProduct'),
                DB::raw('SUM(qty_qc) as qtyQc')
            ])
            ->groupBy('sales_doc')
            ->get();

        return view('mobile.inbound.detail', compact('purchaseOrderDetail', 'purchaseOrder'));
    }

    public function indexDetailSoMobile(Request $request): View
    {
        $salesDoc = $request->query('so');
        $purchaseOrderId = $request->query('po');

        $purchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $purchaseOrderId)
            ->where('sales_doc', $salesDoc)
            ->when($request->query('material'), function ($q) use ($request) {
                $q->where('material', 'LIKE', '%'.$request->query('material').'%');
            })
            ->get();

        return view('mobile.inbound.detail-so', compact('purchaseOrderDetail', 'purchaseOrderId'));
    }

    public function purchaseOrderDownloadExcel(Request $request): StreamedResponse
    {
        $purchaseOrder = PurchaseOrder::with('customer', 'user', 'vendor')->where('id', $request->query('id'))->first();
        $purchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Purc Doc');
        $sheet->setCellValue('A2', 'Vendor');
        $sheet->setCellValue('A3', 'Customer');
        $sheet->setCellValue('A4', 'Date');
        $sheet->setCellValue('A5', 'Created By');

        $sheet->setCellValue('B1', $purchaseOrder->purc_doc);
        $sheet->setCellValue('B2', $purchaseOrder->vendor->name);
        $sheet->setCellValue('B3', $purchaseOrder->customer->name);
        $sheet->setCellValue('B4', $purchaseOrder->created_at);
        $sheet->setCellValue('B5', $purchaseOrder->user->name);

        $sheet->setCellValue('A7', 'Sales Doc');
        $sheet->setCellValue('B7', 'Item');
        $sheet->setCellValue('C7', 'Material');
        $sheet->setCellValue('D7', 'PO Item Desc');
        $sheet->setCellValue('E7', 'Prod Hierarchy Desc');
        $sheet->setCellValue('F7', 'PO Item QTY');
        $sheet->setCellValue('G7', 'Net Order Price');
        $sheet->setCellValue('H7', 'QTY Quality Control');
        $sheet->setCellValue('I7', 'Serial Number');

        $column = 8;
        foreach ($purchaseOrderDetail as $detail) {
            if ($detail->qty_qc == 0) {
                $this->extracted($sheet, $column, $detail);

                $column++;
            } else {
                $serialNumber = DB::table('product_package_item')
                    ->leftJoin('product_package_item_sn', 'product_package_item_sn.product_package_item_id', '=', 'product_package_item.id')
                    ->where('product_package_item.purchase_order_detail_id', $detail->id)
                    ->select([
                        'product_package_item_sn.serial_number'
                    ])
                    ->get();

                foreach ($serialNumber as $index => $item) {
                    if ($index == 0) {
                        $this->extracted($sheet, $column, $detail);
                    } else {
                        $sheet->setCellValue('A' . $column, '');
                        $sheet->setCellValue('B' . $column, '');
                        $sheet->setCellValue('C' . $column, '');
                        $sheet->setCellValue('D' . $column, '');
                        $sheet->setCellValue('E' . $column, '');
                        $sheet->setCellValue('F' . $column, '');
                        $sheet->setCellValue('G' . $column, '');
                        $sheet->setCellValue('H' . $column, '');

                    }
                    $sheet->setCellValue('I' . $column, $item->serial_number);
                    $column++;
                }
            }
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $fileName = 'Report Purchase Order '. $purchaseOrder->purc_doc. ' '. date('Y-m-d') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment;filename=\"$fileName\"");
        $response->headers->set('Cache-Control','max-age=0');

        return $response;
    }

    public function indexDetailSoSnMobile(Request $request): View
    {
        $serialNumber = DB::table('inventory_package')
            ->leftJoin('inventory_package_item', 'inventory_package_item.inventory_package_id', '=', 'inventory_package.id')
            ->leftJoin('inventory_package_item_sn', 'inventory_package_item_sn.inventory_package_item_id', '=', 'inventory_package_item.id')
            ->where('inventory_package_item.purchase_order_detail_id', $request->query('id'))
            ->whereNotIn('inventory_package.storage_id', [1,2,3,4])
            ->where('inventory_package_item.qty', '!=', 0)
            ->where('inventory_package_item_sn.qty', '!=', 0)
            ->select([
                'inventory_package_item_sn.serial_number'
            ])
            ->get();

        return view('mobile.inbound.sn', compact('request', 'serialNumber'));
    }

    /**
     * @param Worksheet $sheet
     * @param int $column
     * @param mixed $detail
     * @return void
     */
    public function extracted(Worksheet $sheet, int $column, mixed $detail): void
    {
        $sheet->setCellValue('A' . $column, $detail->sales_doc);
        $sheet->setCellValue('B' . $column, $detail->item);
        $sheet->setCellValue('C' . $column, $detail->material);
        $sheet->setCellValue('D' . $column, $detail->po_item_desc);
        $sheet->setCellValue('E' . $column, $detail->prod_hierarchy_desc);
        $sheet->setCellValue('F' . $column, $detail->po_item_qty);
        $sheet->setCellValue('G' . $column, $detail->net_order_price);
        $sheet->setCellValue('H' . $column, $detail->qty_qc);
    }

    public function purchaseOrderDownloadPdf(Request $request): \Illuminate\Http\Response
    {
        $purchaseOrder = PurchaseOrder::with('customer', 'user', 'vendor')->where('id', $request->query('id'))->first();
        $purchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))->get();

        foreach ($purchaseOrderDetail as $detail) {
            $detail->serialNumber = DB::table('product_package_item')
                ->leftJoin('product_package_item_sn', 'product_package_item_sn.product_package_item_id', '=', 'product_package_item.id')
                ->where('product_package_item.purchase_order_detail_id', $detail->id)
                ->select([
                    'product_package_item_sn.serial_number'
                ])
                ->get();
        }

        $data = [
            'purchaseOrder'          => $purchaseOrder,
            'purchaseOrderDetail'    => $purchaseOrderDetail,
        ];

        $pdf = Pdf::loadView('pdf.inbound', $data)->setPaper('a4', 'landscape');;
        return $pdf->download('Purchase Order '.$purchaseOrder->purc_doc.'.pdf');
    }
}
















































