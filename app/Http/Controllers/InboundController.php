<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
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
                    $this->storePurchaseOrderDetail($checkPO, $item);
                    PurchaseOrder::find($checkPO->id)->increment('sales_docs_qty');
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

                $salesDocsQty = (clone $query)->select('sales_doc')->groupBy('sales_doc')->get()->count();
                $materialQty = (clone $query)->select('product_id')->groupBy('product_id')->get()->count();
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
            $products[] = [
                'id'        => $item->id,
                'sku'       => $item->material,
                'name'      => $item->po_item_desc,
                'type'      => $item->prod_hierarchy_desc,
                'qty'       => $item->po_item_qty,
                'item'      => $item->item,
                'qty_qc'    => 0,
            ];
        }

        $title = "Quality Control";
        return view('inbound.quality-control.qc', compact('title', 'request', 'products', 'purchaseOrder'));
    }


}
















































