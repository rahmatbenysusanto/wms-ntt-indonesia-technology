<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\Product;
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
    public function purchaseOrder(): View
    {
        $purchaseOrder = PurchaseOrder::with('vendor', 'customer', 'user')->latest()->paginate(10);

        $title = "Purchase Order";
        return view('inbound.purchase-order.index', compact('title', 'purchaseOrder'));
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

    public function qualityControl(): View
    {
        $purchaseOrder = PurchaseOrder::with('vendor', 'customer', 'user')->latest()
            ->whereIn('status', ['open', 'process'])
            ->paginate(10);

        $title = "Quality Control";
        return view('inbound.quality-control.index', compact('title', 'purchaseOrder'));
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
        $purchaseOrder = PurchaseOrder::find($request->query('po'));
        $data = PurchaseOrderDetail::where('purchase_order_id', $request->query('po'))
            ->where('sales_doc', $request->query('sales-doc'))
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
                    'qty_qc_done' => $item->qty_quality_control
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

            $qualityControl = QualityControl::create([
                'number'            => 'QC-' . date('ymd') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'purchase_order_id' => $request->post('purchaseOrderId'),
                'sales_doc'         => $request->post('salesDoc'),
                'qty_parent'        => 0,
                'status'            => 'qc',
                'created_by'        => 1
            ]);

            $qty_parent = 0;
            foreach ($request->post('qualityControl') as $item) {
                $detail = QualityControlDetail::create([
                    'quality_control_id'        => $qualityControl->id,
                    'purchase_order_detail_id'  => $item['id'],
                    'qty'                       => $item['qty'],
                    'status'                    => 'qc'
                ]);

                // Input Serial Number
                foreach ($item['sn'] ?? [] as $sn) {
                    SerialNumber::create([
                        'purchase_order_id'         => $request->post('purchaseOrderId'),
                        'purchase_order_detail_id'  => $item['id'],
                        'serial_number'             => $sn
                    ]);
                }

                $qty_parent += 1;

                PurchaseOrderDetail::find($item['id'])->increment('qty_quality_control', $item['qty']);
                $checkPoDetail = PurchaseOrderDetail::find($item['id']);
                if ($checkPoDetail->po_item_qty === $checkPoDetail->qty_quality_control) {
                    PurchaseOrderDetail::find($item['id'])->update(['status' => 'qc']);
                }

                foreach ($item['child'] as $child) {
                    QualityControlItem::create([
                        'quality_control_detail_id' => $detail->id,
                        'purchase_order_detail_id'  => $child['id'],
                        'qty'                       => $child['qty']
                    ]);

                    // Input Serial Number
                    foreach ($child['sn'] as $sn) {
                        SerialNumber::create([
                            'purchase_order_id'         => $request->post('purchaseOrderId'),
                            'purchase_order_detail_id'  => $item['id'],
                            'serial_number'             => $sn
                        ]);
                    }

                    PurchaseOrderDetail::find($child['id'])->increment('qty_quality_control', $child['qty']);
                    $checkPoDetail = PurchaseOrderDetail::find($child['id']);
                    if ($checkPoDetail->po_item_qty === $checkPoDetail->qty_quality_control) {
                        PurchaseOrderDetail::find($child['id'])->update(['status' => 'qc']);
                    }
                }
            }

            QualityControl::find($qualityControl->id)->update(['qty_parent' => $qty_parent]);

            $checkQC = PurchaseOrderDetail::where('purchase_order_id', $request->post('purchaseOrderId'))
                ->where('status', 'new')
                ->count();

            if ($checkQC == 0) {
                PurchaseOrder::find($request->post('purchaseOrderId'))->update(['status' => 'done']);
            } else {
                PurchaseOrder::find($request->post('purchaseOrderId'))->update(['status' => 'process']);
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

    public function putAway(): View
    {
        $putAway = QualityControl::with('purchaseOrder', 'user')->latest()->paginate(10);

        $title = 'Put Away';
        return view('inbound.put-away.index', compact('title', 'putAway'));
    }

    public function putAwayDetail(): View
    {
        $title = 'Put Away';
        return view('inbound.put-away.detail', compact('title'));
    }

    public function putAwayProcess(Request $request): View
    {
        $qualityControl = QualityControl::where('number', $request->query('number'))->first();
        $productParent = QualityControlDetail::where('quality_control_id', $qualityControl->id)->get();
        $storageRaw = Storage::where('area', null)->where('rak', null)->where('bin', null)->get();

        $products = [];
        foreach ($productParent as $parent) {
            $poDetail = PurchaseOrderDetail::find($parent->purchase_order_detail_id);
            $productChild = QualityControlItem::where('quality_control_detail_id', $parent->id)->get();
            $child = [];
            foreach ($productChild as $item) {
                $detail = PurchaseOrderDetail::find($item->purchase_order_detail_id);
                $child[] = [
                    'sku'   => $detail->material,
                    'name'  => $detail->po_item_desc,
                    'type'  => $detail->prod_hierarchy_desc,
                    'qty'   => $item->qty,
                    'item'  => $detail->item,
                ];
            }

            $location = null;
            if ($parent->storage_id != null) {
                $storage = Storage::find($parent->storage_id);
                $location = $storage->raw.' - '.$storage->area.' - '.$storage->rak.' - '.$storage->bin;
            }

            $products[] = [
                'id'                        => $parent->id,
                'quality_control_id'        => $qualityControl->id,
                'purchase_order_detail_id'  => $parent->purchase_order_detail_id,
                'qty'                       => $parent->qty,
                'sku'                       => $poDetail->material,
                'name'                      => $poDetail->po_item_desc,
                'type'                      => $poDetail->prod_hierarchy_desc,
                'item'                      => $poDetail->item,
                'child'                     => $child,
                'location'                  => $location
            ];
        }

        $title = 'Put Away';
        return view('inbound.put-away.process', compact('title', 'products', 'storageRaw'));
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

    public function putAwayStore(Request $request): \Illuminate\Http\RedirectResponse
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
}
















































