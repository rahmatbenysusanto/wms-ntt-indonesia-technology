<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\InventoryParentDetail;
use App\Models\PurchaseOrderDetail;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $inventory = DB::table('inventory_detail')
            ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
            ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
            ->leftJoin('storage', 'inventory_detail.storage_id', '=', 'storage.id')
            ->where('inventory_detail.stock', '!=', 0);

        if ($request->query('purcDoc') != null) {
            $inventory = $inventory->where('purchase_order.purc_doc', $request->query('purcDoc'));
        }

        if ($request->query('salesDoc') != null) {
            $inventory = $inventory->where('purchase_order_detail.sales_doc', $request->query('salesDoc'));
        }

        if ($request->query('material') != null) {
            $inventory = $inventory->where('purchase_order_detail.material', 'LIKE', '%'.$request->query('material').'%');
        }

        $inventory = $inventory->select([
                'inventory_detail.id',
                'inventory_detail.stock',
                'inventory_detail.created_at',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.item',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                'purchase_order.purc_doc',
                'storage.raw',
                'storage.area',
                'storage.rak',
                'storage.bin',
            ])
            ->latest('inventory_detail.created_at')
            ->paginate(10)
            ->appends([
                'purcDoc' => $request->query('purcDoc'),
                'salesDoc' => $request->query('salesDoc'),
                'material' => $request->query('material'),
            ]);

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
        $cycleCount = InventoryHistory::leftJoin('inventory_detail', 'inventory_detail.id', '=', 'inventory_history.inventory_detail_id')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_detail.purchase_order_detail_id')
            ->whereBetween('inventory_history.created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
            ->select([
                'inventory_detail.purc_doc',
                'inventory_detail.sales_doc',
                'inventory_history.qty',
                'inventory_history.type',
                'inventory_history.created_at',
                'purchase_order_detail.item',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc'
            ])
            ->paginate(10);

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
        $title = 'Transfer Location';
        return view('inventory.transfer-location.create', compact('title'));
    }
}
