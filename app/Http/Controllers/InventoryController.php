<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\InventoryPackageItem;
use App\Models\InventoryParent;
use App\Models\InventoryParentDetail;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\SerialNumber;
use App\Models\Storage;
use App\Models\TransferLocation;
use App\Models\TransferLocationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $inventory = [];

        $title = 'Inventory';
        return view('inventory.index', compact('title', 'inventory'));
    }

    public function detail(Request $request): View
    {
        $inventory = InventoryDetail::with('storage', 'purchaseOrderDetail')->where('id', $request->query('id'))->first();
        $detail = [];
        $inventoryParent = DB::table('inventory_parent')
            ->leftJoin('inventory_parent_detail', 'inventory_parent.id', '=', 'inventory_parent_detail.inventory_parent_id')
            ->where('inventory_parent_detail.purchase_order_detail_id', $inventory->purchase_order_detail_id)
            ->where('inventory_parent_detail.sales_doc', $request->query('sales-doc'))
            ->select([
                'inventory_parent.id',
                'inventory_parent.pa_number',
                'inventory_parent.pa_reff_number',
                'inventory_parent_detail.id AS detail_id',
                'inventory_parent_detail.qty'
            ])->get();
        foreach ($inventoryParent as $parent) {
            $serialNumber = SerialNumber::where('inventory_parent_id', $parent->id)
                ->where('inventory_parent_detail_id', $parent->detail_id)
                ->select([
                    'serial_number',
                    'qty'
                ])
                ->get();

            $detail[] = [
                'pa_number'         => $parent->pa_number,
                'pa_reff_number'    => $parent->pa_reff_number,
                'type'              => 'parent',
                'qty'               => $parent->qty,
                'serial_number'     => $serialNumber
            ];
        }

        $inventoryChild = DB::table('inventory_child')
            ->leftJoin('inventory_child_detail', 'inventory_child.id', '=', 'inventory_child_detail.inventory_child_id')
            ->leftJoin('inventory_parent', 'inventory_parent.id', '=', 'inventory_child.inventory_parent_id')
            ->where('inventory_child_detail.purchase_order_detail_id', $inventory->purchase_order_detail_id)
            ->where('inventory_child_detail.sales_doc', $request->query('sales-doc'))
            ->select([
                'inventory_child.id',
                'inventory_parent.pa_number',
                'inventory_parent.pa_reff_number',
                'inventory_child_detail.id AS detail_id',
                'inventory_child_detail.qty'
            ])->get();
        foreach ($inventoryChild as $child) {
            $serialNumber = SerialNumber::where('inventory_child_id', $child->id)
                ->where('inventory_child_detail_id', $child->detail_id)
                ->select([
                    'serial_number',
                    'qty'
                ])
                ->get();

            $detail[] = [
                'pa_number'         => $child->pa_number,
                'pa_reff_number'    => $child->pa_reff_number,
                'type'              => 'child',
                'qty'               => $child->qty,
                'serial_number'     => $serialNumber
            ];
        }

        $title = 'Inventory';
        return view('inventory.detail', compact('title', 'inventory', 'detail'));
    }

    public function cycleCount(): View
    {
        $cycleCount = DB::table('inventory_history')
            ->leftJoin('inventory_parent', 'inventory_parent.id', '=', 'inventory_history.inventory_parent_id')
            ->leftJoin('inventory_child', 'inventory_child.id', '=', 'inventory_history.inventory_child_id')
            ->whereBetween('inventory_history.created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
            ->select([
                'inventory_history.purc_doc',
                'inventory_history.sales_doc',
                'inventory_history.type',
                'inventory_history.qty',
                'inventory_history.created_at',
                'inventory_parent.product_id AS parent_product_id',
                'inventory_child.product_id AS child_product_id',
            ])
            ->paginate(10);

        foreach ($cycleCount as $count) {
            if ($count->parent_product_id != null) {
                $product = Product::find($count->parent_product_id);
            } else {
                $product = Product::find($count->child_product_id);
            }

            $count->material = $product->material;
            $count->po_item_desc = $product->po_item_desc;
            $count->prod_hierarchy_desc = $product->prod_hierarchy_desc;
        }

        $title = 'Cycle Count';
        return view('inventory.cycle-count', compact('title', 'cycleCount'));
    }

    public function transferLocation(): View
    {
        $title = 'Transfer Location';
        return view('inventory.transfer-location.index', compact('title'));
    }

    public function transferLocationCreate(): View
    {
        $products = InventoryParent::where('storage_id', '!=', 1)
            ->where('stock', '!=', 0)
            ->get();

        $storageRaw = Storage::whereNull('area')->whereNull('rak')->whereNull('bin')->whereNot('id', 1)->get();

        $title = 'Transfer Location';
        return view('inventory.transfer-location.create', compact('title', 'products', 'storageRaw'));
    }

    public function transferLocationFindNumber(Request $request): \Illuminate\Http\JsonResponse
    {
        $products = [];

        $checkPaNumber = InventoryParent::where('pa_reff_number', $request->query('paNumber'))->first();
        if (!$checkPaNumber) {
            return response()->json([
                'success' => false,
            ]);
        }

        $parent = DB::table('inventory_parent_detail')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_parent_detail.purchase_order_detail_id')
            ->where('inventory_parent_detail.inventory_parent_id', $checkPaNumber->id)
            ->select([
                'inventory_parent_detail.id',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                'inventory_parent_detail.qty',
                'purchase_order_detail.id AS purchase_order_detail_id'
            ])
            ->get();

        foreach ($parent as $item) {
            $products[] = [
                'id'            => $item->id,
                'sales_doc'     => $item->sales_doc,
                'material'      => $item->material,
                'po_item_desc'  => $item->po_item_desc,
                'prod_hierarchy'=> $item->prod_hierarchy_desc,
                'qty'           => $item->qty,
                'type'          => 'parent',
                'purchase_order_detail_id' => $item->purchase_order_detail_id,
            ];
        }

        $child = DB::table('inventory_child')
            ->leftJoin('inventory_child_detail', 'inventory_child.id', '=', 'inventory_child_detail.inventory_child_id')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_child_detail.purchase_order_detail_id')
            ->where('inventory_child.inventory_parent_id', $checkPaNumber->id)
            ->select([
                'inventory_child_detail.id',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                'inventory_child_detail.qty',
                'purchase_order_detail.id AS purchase_order_detail_id'
            ])
            ->get();
        foreach ($child as $item) {
            $products[] = [
                'id'            => $item->id,
                'sales_doc'     => $item->sales_doc,
                'material'      => $item->material,
                'po_item_desc'  => $item->po_item_desc,
                'prod_hierarchy'=> $item->prod_hierarchy_desc,
                'qty'           => $item->qty,
                'type'          => 'child',
                'purchase_order_detail_id' => $item->purchase_order_detail_id,
            ];
        }

        return response()->json([
            'status'    => true,
            'data'      => $products
        ]);
    }

    public function transferLocationStore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $inventoryParent = InventoryParent::where('pa_reff_number', $request->post('paNumber'))->first();
            $purchaseOrder = PurchaseOrder::where('id', $inventoryParent->purchase_order_id)->first();

            $transferLocation = TransferLocation::create([
                'number'                => 'TRS-',
                'inventory_parent_id'   => $inventoryParent->id,
                'purc_doc'              => $purchaseOrder->purc_doc,
                'old_location'          => $inventoryParent->storage_id,
                'new_location'          => $request->post('storageId'),
                'created_by'            => Auth::id()
            ]);

            foreach ($request->post('products') as $product) {
                TransferLocationDetail::create([
                    'transfer_location_id'          => $transferLocation->id,
                    'type'                          => $product['type'],
                    'inventory_parent_detail_id'    => $product['type'] == 'parent' ? $product['id'] : null,
                    'inventory_child_detail_id'     => $product['type'] == 'child' ? $product['id'] : null,
                    'qty'                           => $product['qty'],
                ]);

                // Old Location
                InventoryDetail::where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                    ->where('storage_id', $inventoryParent->storage_id)
                    ->decrement('stock', $product['qty']);

                // New Location
                $check = InventoryDetail::where('purchase_order_detail_id', $product['purchase_order_detail_id'])
                    ->where('storage_id', $request->post('storageId'))
                    ->first();
                if ($check == null) {
                    InventoryDetail::create([
                        'purchase_order_detail_id'  => $product['purchase_order_detail_id'],
                        'storage_id'                => $request->post('storageId'),
                        'stock'                     => $product['qty'],
                    ]);
                } else {
                    InventoryDetail::where('id'. $check->id)->increment('stock', $product['qty']);
                }
            }

            InventoryParent::where('id', $inventoryParent->id)->update([
                'storage_id' => $request->post('storageId')
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
}
