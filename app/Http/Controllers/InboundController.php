<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryChild;
use App\Models\InventoryChildDetail;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\InventoryParent;
use App\Models\InventoryParentDetail;
use App\Models\Product;
use App\Models\ProductChild;
use App\Models\ProductChildDetail;
use App\Models\ProductParent;
use App\Models\ProductParentDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\QualityControl;
use App\Models\QualityControlDetail;
use App\Models\QualityControlItem;
use App\Models\SerialNumber;
use App\Models\Storage;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

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

    public function purchaseOrderUploadProcess(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $masterPO = [];
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
                        'sales_docs_qty'    => 0,
                        'material_qty'      => 0,
                        'items_qty'         => 0,
                        'status'            => 'new',
                        'created_by'        => Auth::id() ?? 1
                    ]);

                    $masterPO[] = $purchaseOrder->id;

                    $this->storePurchaseOrderDetail($purchaseOrder, $item);
                }
            }

            foreach ($masterPO as $po) {
                $query = PurchaseOrderDetail::where('purchase_order_id', $po);

                $salesDocsQty = (clone $query)->groupBy('sales_doc')->count();
                $materialQty = (clone $query)->groupBy('material')->count();
                $itemQty = (clone $query)->sum('po_item_qty');

                PurchaseOrder::where('id', $po)->update([
                    'sales_docs_qty' => $salesDocsQty,
                    'material_qty'   => $materialQty,
                    'items_qty'      => $itemQty,
                ]);
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

    private function storePurchaseOrderDetail($checkPO, mixed $item): void
    {
        // Check Products Master
        $checkProduct = Product::where('material', $item['material'])->first();
        if ($checkProduct) {
            $productId = $checkProduct->id;
        } else {
            $product = Product::create([
                'material'              => $item['material'],
                'po_item_desc'          => $item['po_item_desc'],
                'prod_hierarchy_desc'   => $item['prod_hierarchy_desc'],
            ]);
            $productId = $product->id;
        }

        PurchaseOrderDetail::create([
            'purchase_order_id'     => $checkPO->id,
            'product_id'            => $productId,
            'status'                => 'new',
            'qty_quality_control'   => 0,
            'sales_doc'             => $item['sales_doc'],
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
            'net_order_price'       => $item['net_order_price'] ?? null,
            'currency'              => $item['currency'] ?? null,
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
            ->orderBy('product_id')
            ->get();

        $products = [];
        foreach ($data as $item) {
            if ($item->po_item_qty > $item->qty_quality_control) {
                $products[] = [
                    'id'        => $item->id,
                    'sku'       => $item->material,
                    'name'      => $item->po_item_desc,
                    'type'      => $item->prod_hierarchy_desc,
                    'qty'       => $item->po_item_qty - $item->qty_quality_control,
                    'item'      => $item->item,
                    'qty_qc'    => 0,
                    'qty_qc_done' => $item->qty_quality_control,
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

            $purchaseOrder = PurchaseOrder::find($request->post('purchaseOrderId'));

            foreach ($request->post('qualityControl') as $qualityControl) {
                // Insert Parent
                $product = Product::where('material', $qualityControl['sku'])->first();
                $qtyProductParent = 0;
                $dataSalesDoc = [];
                $productParent = ProductParent::create([
                    'product_id'        => $product->id,
                    'purchase_order_id' => $request->post('purchaseOrderId'),
                    'qty'               => 0,
                    'storage_id'        => $qualityControl['putAwayStep'] == 0 ? 1 : null
                ]);

                $qualityControl['parent'][0]['sn'] = $qualityControl['sn'];
                foreach ($qualityControl['parent'] as $parent) {
                    $product = Product::where('material', $parent['sku'])->first();
                    $purcOrderDetail = PurchaseOrderDetail::find($parent['id']);
                    $productParentDetail = ProductParentDetail::create([
                        'product_parent_id'         => $productParent->id,
                        'product_id'                => $product->id,
                        'purchase_order_detail_id'  => $parent['id'],
                        'sales_doc'                 => $purcOrderDetail->sales_doc,
                        'qty'                       => $parent['qty']
                    ]);

                    foreach ($parent['sn'] ?? [] as $serialNumber) {
                        SerialNumber::create([
                            'purchase_order_id'         => $request->post('purchaseOrderId'),
                            'purchase_order_detail_id'  => $parent['id'],
                            'product_id'                => $product->id,
                            'product_parent_id'         => $productParent->id,
                            'product_parent_detail_id'  => $productParentDetail->id,
                            'serial_number'             => $serialNumber['serialNumber'],
                            'qty'                       => 1
                        ]);
                    }

                    PurchaseOrderDetail::where('id', $parent['id'])->increment('qty_quality_control', $parent['qty']);

                    $qtyProductParent += $parent['qty'];
                    $dataSalesDoc[] = $purcOrderDetail->sales_doc;
                }
                ProductParent::where('id', $productParent->id)->update(['qty' => $qtyProductParent]);

                // Insert Child
                foreach ($qualityControl['child'] as $child) {
                    $product = Product::where('material', $child['sku'])->first();
                    $purcOrderDetail = PurchaseOrderDetail::find($child['id']);
                    $productChild = ProductChild::create([
                        'product_parent_id'         => $productParent->id,
                        'product_id'                => $product->id,
                        'purchase_order_id'         => $request->post('purchaseOrderId'),
                        'qty'                       => $child['qty']
                    ]);

                    $productChildDetail = ProductChildDetail::create([
                        'product_child_id'          => $productChild->id,
                        'product_id'                => $product->id,
                        'purchase_order_detail_id'  => $child['id'],
                        'sales_doc'                 => $purcOrderDetail->sales_doc,
                        'qty'                       => $child['qty']
                    ]);

                    foreach ($child['sn'] ?? [] as $serialNumber) {
                        SerialNumber::create([
                            'purchase_order_id'         => $request->post('purchaseOrderId'),
                            'purchase_order_detail_id'  => $child['id'],
                            'product_id'                => $product->id,
                            'product_parent_id'         => $productParent->id,
                            'product_child_id'          => $productChild->id,
                            'product_child_detail_id'   => $productChildDetail->id,
                            'serial_number'             => $serialNumber['serialNumber'],
                            'qty'                       => 1
                        ]);
                    }

                    PurchaseOrderDetail::where('id', $child['id'])->increment('qty_quality_control', $child['qty']);

                    $dataSalesDoc[] = $purcOrderDetail->sales_doc;
                }

                // Jika Item langsung dioutbound tanpa di masukan ke gudang/disimpan
                if ($qualityControl['putAwayStep'] == 0) {
                    $product = Product::where('material', $qualityControl['sku'])->first();
                    $inventoryParent = InventoryParent::create([
                        'product_id'        => $product->id,
                        'purchase_order_id' => $request->post('purchaseOrderId'),
                        'storage_id'        => 1,
                        'pa_number'         => 'PA-'.date('ymdHis').rand(111, 999),
                        'stock'             => $qtyProductParent,
                        'sales_docs'        => json_encode($dataSalesDoc),
                    ]);

                    foreach ($qualityControl['parent'] as $parent) {
                        $product = Product::where('material', $parent['sku'])->first();
                        $purcOrderDetail = PurchaseOrderDetail::find($parent['id']);
                        InventoryParentDetail::create([
                            'product_id'                => $product->id,
                            'inventory_parent_id'       => $inventoryParent->id,
                            'purchase_order_detail_id'  => $parent['id'],
                            'sales_doc'                 => $purcOrderDetail->sales_doc,
                            'qty'                       => $parent['qty']
                        ]);

                        foreach ($parent['sn'] ?? [] as $serialNumber) {
                            SerialNumber::create([
                                'purchase_order_id'         => $request->post('purchaseOrderId'),
                                'purchase_order_detail_id'  => $parent['id'],
                                'serial_number'             => $serialNumber['serialNumber'],
                                'qty'                       => 1
                            ]);
                        }

                        // Insert Inventory & Inventory Detail
                        $checkInventory = Inventory::where('sales_doc', $purcOrderDetail->sales_doc)
                            ->where('purc_doc', $purchaseOrder->purc_doc)
                            ->first();
                        if ($checkInventory == null) {
                            Inventory::create([
                                'purc_doc'  => $purchaseOrder->purc_doc,
                                'sales_doc' => $purcOrderDetail->sales_doc,
                                'stock'     => $parent['qty']
                            ]);
                        } else {
                            Inventory::where('id', $checkInventory->id)->increment('stock', $parent['qty']);
                        }

                        InventoryDetail::create([
                            'purchase_order_detail_id'  => $parent['id'],
                            'storage_id'                => 1,
                            'stock'                     => $parent['qty']
                        ]);
                    }

                    foreach ($qualityControl['child'] as $child) {
                        $product = Product::where('material', $child['sku'])->first();
                        $purcOrderDetail = PurchaseOrderDetail::find($child['id']);
                        $inventoryChild = InventoryChild::create([
                            'inventory_parent_id'       => $inventoryParent->id,
                            'product_id'                => $product->id,
                            'purchase_order_id'         => $request->post('purchaseOrderId'),
                            'stock'                     => $child['qty']
                        ]);

                        InventoryChildDetail::create([
                            'inventory_child_id'        => $inventoryChild->id,
                            'product_id'                => $product->id,
                            'purchase_order_detail_id'  => $child['id'],
                            'sales_doc'                 => $purcOrderDetail->sales_doc,
                            'qty'                       => $child['qty']
                        ]);

                        foreach ($child['sn'] ?? [] as $serialNumber) {
                            SerialNumber::create([
                                'purchase_order_id'         => $request->post('purchaseOrderId'),
                                'purchase_order_detail_id'  => $child['id'],
                                'product_id'                => $product->id,
                                'serial_number'             => $serialNumber['serialNumber'],
                                'qty'                       => 1
                            ]);
                        }

                        // Insert Inventory & Inventory Detail
                        $checkInventory = Inventory::where('sales_doc', $purcOrderDetail->sales_doc)
                            ->where('purc_doc', $purchaseOrder->purc_doc)
                            ->first();
                        if ($checkInventory == null) {
                            Inventory::create([
                                'purc_doc'  => $purchaseOrder->purc_doc,
                                'sales_doc' => $purcOrderDetail->sales_doc,
                                'stock'     => $child['qty']
                            ]);
                        } else {
                            Inventory::where('id', $checkInventory->id)->increment('stock', $child['qty']);
                        }

                        InventoryDetail::create([
                            'purchase_order_detail_id'  => $child['id'],
                            'storage_id'                => 1,
                            'stock'                     => $child['qty']
                        ]);
                    }
                }
            }

            // Check Purchase Order Detail
            $purchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $request->post('purchaseOrderId'))->get();
            foreach ($purchaseOrderDetail as $detail) {
                if ($detail->qty_quality_control == $detail->po_item_qty) {
                    PurchaseOrderDetail::where('id', $detail->id)->update(['status' => 'done']);
                } else {
                    PurchaseOrderDetail::where('id', $detail->id)->update(['status' => 'qc']);
                }
            }

            // Check Purchase Order
            $checkStatusQC = PurchaseOrderDetail::where('purchase_order_id', $request->post('purchaseOrderId'))
                ->whereIn('status', ['qc', 'new'])
                ->count();
            if ($checkStatusQC == 0) {
                PurchaseOrder::where('id', $request->post('purchaseOrderId'))->update(['status' => 'close']);
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

    public function putAway(Request $request): View
    {
        $putAway = ProductParent::with([
            'purchaseOrder' => function ($purchaseOrder) {
                $purchaseOrder->select([
                    'id', 'purc_doc'
                ]);
            },
            'productParentDetail' => function ($productParentDetail) {
                $productParentDetail->select([
                    'id', 'product_parent_id', 'sales_doc'
                ]);
            },
            'product' => function ($product) {
                $product->select([
                    'id', 'material', 'po_item_desc'
                ]);
            }
            ])
            ->latest()
            ->paginate(10);

        $title = 'Put Away';
        return view('inbound.put-away.index', compact('title', 'putAway'));
    }

    public function putAwayDetail(Request $request)
    {
        $products = InventoryParent::with([
            'inventoryParentDetail',
            'inventoryParentDetail.product',
            'inventoryParentDetail.purchaseOrderDetail',
            'storage',
            'inventoryChild',
            'inventoryChild.inventoryChildDetail',
            'inventoryChild.inventoryChildDetail.product',
            'inventoryChild.inventoryChildDetail.purchaseOrderDetail',
        ])->where('pa_number', $request->query('pa-number'))->get();

        $title = 'Put Away';
        return view('inbound.put-away.detail', compact('title', 'products'));
    }

    public function putAwayProcess(Request $request): View
    {
        $products = ProductParent::with([
            'product' => function ($product) {
                $product->select([
                    'id', 'material', 'po_item_desc', 'prod_hierarchy_desc'
                ]);
            },
            'productParentDetail',
            'productParentDetail.purchaseOrderDetail' => function ($purchaseOrderDetail) {
                $purchaseOrderDetail->select([
                    'id', 'item'
                ]);
            },
            'productChild',
            'productChild.product' => function ($productChildProduct) {
                $productChildProduct->select([
                    'id', 'material', 'po_item_desc', 'prod_hierarchy_desc'
                ]);
            },
            'productChild.productChildDetail',
            'productChild.productChildDetail.purchaseOrderDetail' => function ($purchaseOrderDetail) {
                $purchaseOrderDetail->select([
                    'id', 'item'
                ]);
            },
            ])
            ->where('id', $request->query('id'))
            ->first();

        $storageRaw = Storage::where('raw', '!=', '-')->where('area', null)->where('rak', null)->where('bin', null)->get();

        $title = 'Put Away';
        return view('inbound.put-away.process', compact('title', 'products', 'storageRaw'));
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

    public function putAwaySetLocation(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $inventory = Inventory::create([
                'purchase_order_id' => '',
                'purc_doc'          => '',
                'sales_doc'         => ''
            ]);



            DB::commit();
            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            return response()->json([
                'status' => false,
            ]);
        }
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

            $productParent = ProductParent::find($request->post('productParentId'));
            $purchaseOrder = PurchaseOrder::where('id', $productParent->purchase_order_id)->first();

            ProductParent::where('id', $productParent->id)->update([
                'storage_id'    => $request->post('bin'),
                'pa_number'     => $putAwayNumber,
            ]);

            foreach ($listBox as $box) {
                $invParentStock = 0;
                $invParentSalesDocs = [];

                // Parent
                $inventoryParent = InventoryParent::create([
                    'product_id'        => $box['parent'][0]['productId'],
                    'purchase_order_id' => $productParent->purchase_order_id,
                    'storage_id'        => $request->post('bin'),
                    'pa_number'         => $putAwayNumber,
                    'pa_reff_number'    => $putAwayNumber.'-'.$box['boxNumber'],
                    'stock'             => 0,
                    'sales_docs'        => json_encode([]),
                ]);
                foreach ($box['parent'] as $parent) {
                    $inventoryParentDetail = InventoryParentDetail::create([
                        'product_id'                => $parent['productId'],
                        'inventory_parent_id'       => $inventoryParent->id,
                        'purchase_order_detail_id'  => $parent['purchaseOrderDetailId'],
                        'sales_doc'                 => $parent['salesDoc'],
                        'qty'                       => $parent['qtySelect']
                    ]);

                    // Serial Number
                    foreach ($parent['serialNumber'] as $serialNumber) {
                        $findSerialNumber = SerialNumber::where('purchase_order_id', $productParent->purchase_order_id)
                            ->where('purchase_order_detail_id', $parent['purchaseOrderDetailId'])
                            ->where('product_id', $parent['productId'])
                            ->where('serial_number', $serialNumber)
                            ->first();

                        if ($findSerialNumber) {
                            SerialNumber::where('id', $findSerialNumber->id)->update([
                                'inventory_parent_id'        => $inventoryParent->id,
                                'inventory_parent_detail_id' => $inventoryParentDetail->id,
                            ]);
                        } else {
                            SerialNumber::create([
                                'purchase_order_id'          => $productParent->purchase_order_id,
                                'purchase_order_detail_id'   => $parent['purchaseOrderDetailId'],
                                'product_id'                 => $parent['productId'],
                                'inventory_parent_id'        => $inventoryParent->id,
                                'inventory_parent_detail_id' => $inventoryParentDetail->id,
                                'serial_number'              => $serialNumber,
                                'qty'                        => 1
                            ]);
                        }
                    }

                    // Inventory & Inventory Detail
                    $checkInventory = Inventory::where('purc_doc', $purchaseOrder->purc_doc)
                        ->where('sales_doc', $parent['salesDoc'])
                        ->first();
                    if ($checkInventory) {
                        Inventory::where('id', $checkInventory->id)->increment('stock', $parent['qtySelect']);
                    } else {
                        Inventory::create([
                            'purc_doc'  => $purchaseOrder->id,
                            'sales_doc' => $parent['salesDoc'],
                            'stock'     => $parent['qtySelect']
                        ]);
                    }

                    $checkInventoryDetail = InventoryDetail::where('purchase_order_detail_id', $parent['purchaseOrderDetailId'])
                        ->where('storage_id', $request->post('bin'))
                        ->first();
                    if ($checkInventoryDetail) {
                        InventoryDetail::where('id', $checkInventoryDetail->id)->increment('stock', $parent['qtySelect']);
                    } else {
                        InventoryDetail::create([
                            'purchase_order_detail_id'  => $parent['purchaseOrderDetailId'],
                            'storage_id'                => $request->post('bin'),
                            'stock'                     => $parent['qtySelect']
                        ]);
                    }

                    $invParentStock += $parent['qtySelect'];
                    $invParentSalesDocs[] = $parent['salesDoc'];
                }

                // Child
                foreach ($box['child'] as $child) {
                    $inventoryChild = InventoryChild::create([
                        'product_id'            => $child['productId'],
                        'purchase_order_id'     => $productParent->purchase_order_id,
                        'inventory_parent_id'   => $inventoryParent->id,
                        'stock'                 => $child['qtySelect'],
                    ]);

                    $inventoryChildDetail = InventoryChildDetail::create([
                        'product_id'                => $child['productId'],
                        'purchase_order_detail_id'  => $child['purchaseOrderDetailId'],
                        'inventory_child_id'        => $inventoryChild->id,
                        'sales_doc'                 => $child['salesDoc'],
                        'qty'                       => $child['qtySelect']
                    ]);

                    // Serial Number
                    foreach ($child['serialNumber'] as $serialNumber) {
                        $findSerialNumber = SerialNumber::where('purchase_order_id', $productParent->purchase_order_id)
                            ->where('purchase_order_detail_id', $child['purchaseOrderDetailId'])
                            ->where('product_id', $child['productId'])
                            ->where('serial_number', $serialNumber)
                            ->first();

                        if ($findSerialNumber) {
                            SerialNumber::where('id', $findSerialNumber->id)->update([
                                'inventory_child_id'        => $inventoryChild->id,
                                'inventory_child_detail_id' => $inventoryChildDetail->id,
                            ]);
                        } else {
                            SerialNumber::create([
                                'purchase_order_id'          => $productParent->purchase_order_id,
                                'purchase_order_detail_id'   => $child['purchaseOrderDetailId'],
                                'product_id'                 => $child['productId'],
                                'inventory_child_id'         => $inventoryParent->id,
                                'inventory_child_detail_id'  => $inventoryChildDetail->id,
                                'serial_number'              => $serialNumber,
                                'qty'                        => 1
                            ]);
                        }
                    }

                    // Inventory & Inventory Detail
                    $checkInventory = Inventory::where('purc_doc', '')
                        ->where('sales_doc', $child['salesDoc'])
                        ->first();
                    if ($checkInventory) {
                        Inventory::where('id', $checkInventory->id)->increment('stock', $child['qtySelect']);
                    } else {
                        Inventory::create([
                            'purc_doc'  => $purchaseOrder->id,
                            'sales_doc' => $child['salesDoc'],
                            'stock'     => $child['qtySelect']
                        ]);
                    }

                    $checkInventoryDetail = InventoryDetail::where('purchase_order_detail_id', $child['purchaseOrderDetailId'])
                        ->where('storage_id', $request->post('bin'))
                        ->first();
                    if ($checkInventoryDetail) {
                        InventoryDetail::where('id', $checkInventoryDetail->id)->increment('stock', $child['qtySelect']);
                    } else {
                        InventoryDetail::create([
                            'purchase_order_detail_id'  => $child['purchaseOrderDetailId'],
                            'storage_id'                => $request->post('bin'),
                            'stock'                     => $child['qtySelect']
                        ]);
                    }
                }

                InventoryParent::where('id', $inventoryParent->id)->update([
                    'stock'         => $invParentStock,
                    'sales_docs'    => $invParentSalesDocs
                ]);
            }

            DB::commit();
            return response()->json([
                'status'    => true,
                'data'      => $putAwayNumber
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

    public function putAwayStore1(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();

            $qualityControl = QualityControl::where('number', $request->post('number'))->first();

            // Check apakah data sudah ada di inventory
            $inventory = Inventory::where('purchase_order_id', $qualityControl->purchase_order_id)
                ->where('sales_doc', $qualityControl->sales_doc)
                ->where('storage_id', $request->post('bin'))
                ->first();

            $purchaseOrder = PurchaseOrder::where('id', $qualityControl->purchase_order_id)->first();
            if ($inventory == null) {
                $inventory = Inventory::create([
                    'purchase_order_id' => $qualityControl->purchase_order_id,
                    'purc_doc'          => $purchaseOrder->purc_doc,
                    'sales_doc'         => $qualityControl->sales_doc,
                    'qty_item'          => 0,
                    'storage_id'        => $request->post('bin'),
                ]);
            }

            // Insert Parent Item
            $qualityControlDetail = QualityControlDetail::where('id', $request->post('id'))->first();
            $parent = InventoryDetail::create([
                'inventory_id'              => $inventory->id,
                'purchase_order_detail_id'  => $qualityControlDetail->purchase_order_detail_id,
                'quality_control_detail_id' => $qualityControlDetail->id,
                'type'                      => 'parent',
                'purc_doc'                  => $purchaseOrder->purc_doc,
                'sales_doc'                 => $qualityControl->sales_doc,
                'qty'                       => $qualityControlDetail->qty
            ]);

            InventoryHistory::create([
                'inventory_id'              => $inventory->id,
                'inventory_detail_id'       => $parent->id,
                'quality_control_id'        => $qualityControl->id,
                'quality_control_detail_id' => $qualityControlDetail->id,
                'type'                      => 'inbound',
                'qty'                       => $qualityControlDetail->qty
            ]);

            Inventory::where('id', $inventory->id)->increment('qty_item', $qualityControlDetail->qty);

            // Insert Child Item
            $qualityControlItem = QualityControlItem::where('quality_control_detail_id', $qualityControlDetail->id)->get();
            foreach ($qualityControlItem as $item) {
                $inventoryDetail = InventoryDetail::create([
                    'inventory_id'              => $inventory->id,
                    'purchase_order_detail_id'  => $item->purchase_order_detail_id,
                    'quality_control_detail_id' => $item->quality_control_detail_id,
                    'type'                      => 'child',
                    'purc_doc'                  => $purchaseOrder->purc_doc,
                    'sales_doc'                 => $qualityControl->sales_doc,
                    'qty'                       => $item->qty,
                    'parent_id'                 => $parent->id
                ]);

                InventoryHistory::create([
                    'inventory_id'              => $inventory->id,
                    'inventory_detail_id'       => $inventoryDetail->id,
                    'quality_control_id'        => $qualityControl->id,
                    'quality_control_detail_id' => $item->quality_control_detail_id,
                    'type'                      => 'inbound',
                    'qty'                       => $item->qty
                ]);

                Inventory::where('id', $inventory->id)->increment('qty_item', $item->qty);
            }

            QualityControlDetail::where('id', $request->post('id'))->update(['status' => 'put away', 'storage_id' => $request->post('bin')]);
            QualityControl::where('number', $request->post('number'))->update([
                'status' => 'put away'
            ]);

            // Cek apakah semua sudah di Put Away
            $check = QualityControlDetail::where('quality_control_id', $qualityControl->id)
                ->where('status', 'qc')
                ->count();
            if ($check == 0) {
                QualityControl::where('number', $request->post('number'))->update([
                    'status' => 'done'
                ]);
            }

            DB::commit();
            return back()->with('success', 'Set lokasi item berhasil');
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return back()->with('error', 'Set Lokasi Gagal');
        }
    }

    public function qualityControlProcessCcw(Request $request): View
    {
        $purcDocDetail = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))->get();

        $title = 'Quality Control';
        return view('inbound.quality-control.ccw.index', compact('title', 'purcDocDetail'));
    }

    public function qualityControlStoreProcessCcw(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $compare = $request->post('compare');

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

            foreach ($grouped as $dataQC) {
                if (count($dataQC['parent']['salesDoc']) != 0) {
                    // Insert Master Parent
                    $product = Product::where('material', $dataQC['parent']['itemName'])->first();
                    $productParent = ProductParent::create([
                        'product_id'        => $product->id,
                        'purchase_order_id' => $request->post('purchaseOrderId'),
                        'qty'               => $dataQC['parent']['qty'],
                        'storage_id'        => $dataQC['parent']['putAwayStep'] == 0 ? 0 : null
                    ]);
                    foreach ($dataQC['parent']['salesDoc'] as $parentItem) {
                        $purcOrderDetail = PurchaseOrderDetail::find($parentItem['id']);
                        ProductParentDetail::create([
                            'product_parent_id'         => $productParent->id,
                            'product_id'                => $purcOrderDetail->product_id,
                            'purchase_order_detail_id'  => $purcOrderDetail->id,
                            'sales_doc'                 => $purcOrderDetail->sales_doc,
                            'qty'                       => $parentItem['qty']
                        ]);
                        PurchaseOrderDetail::where('id', $purcOrderDetail->id)->increment('qty_quality_control', $parentItem['qty']);
                    }
                    foreach ($dataQC['parent']['serialNumber'] as $serialNumber) {
                        SerialNumber::create([
                            'purchase_order_id'     => $request->post('purchaseOrderId'),
                            'product_id'            => $product->id,
                            'serial_number'         => $serialNumber,
                        ]);
                    }

                    // Insert Child dari Parent
                    foreach ($dataQC['child'] as $childItem) {
                        $product = Product::where('material', $childItem['itemName'])->first();
                        $productChild = ProductChild::create([
                            'product_parent_id' => $productParent->id,
                            'product_id'        => $product->id,
                            'purchase_order_id' => $request->post('purchaseOrderId'),
                            'qty'               => $childItem['qty']
                        ]);
                        foreach ($childItem['salesDoc'] as $childItemDetail) {
                            $purcOrderDetail = PurchaseOrderDetail::find($childItemDetail['id']);
                            ProductChildDetail::create([
                                'product_child_id'          => $productChild->id,
                                'product_id'                => $purcOrderDetail->product_id,
                                'purchase_order_detail_id'  => $purcOrderDetail->id,
                                'sales_doc'                 => $purcOrderDetail->sales_doc,
                                'qty'                       => $childItemDetail['qty']
                            ]);
                            PurchaseOrderDetail::where('id', $purcOrderDetail->id)->increment('qty_quality_control', $childItemDetail['qty']);
                        }
                        foreach ($childItem['serialNumber'] as $serialNumber) {
                            SerialNumber::create([
                                'purchase_order_id'     => $request->post('purchaseOrderId'),
                                'product_id'            => $product->id,
                                'serial_number'         => $serialNumber,
                            ]);
                        }
                    }
                }
            }

            // Check Purchase Order Detail
            $purchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $request->post('purchaseOrderId'))->get();
            foreach ($purchaseOrderDetail as $detail) {
                if ($detail->qty_quality_control == $detail->po_item_qty) {
                    PurchaseOrderDetail::where('id', $detail->id)->update(['status' => 'done']);
                } else {
                    PurchaseOrderDetail::where('id', $detail->id)->update(['status' => 'qc']);
                }
            }

            // Check Purchase Order
            $checkStatusQC = PurchaseOrderDetail::where('purchase_order_id', $request->post('purchaseOrderId'))
                ->whereIn('status', ['qc', 'new'])
                ->count();
            if ($checkStatusQC == 0) {
                PurchaseOrder::where('id', $request->post('purchaseOrderId'))->update(['status' => 'close']);
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

    public function compareSapCcw(Request $request)
    {

    }
}
















































