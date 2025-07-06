<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $inventory = Inventory::with('storage');

        if ($request->query('purcDoc') != null) {
            $inventory = $inventory->where('purc_doc', $request->query('purcDoc'));
        }

        if ($request->query('salesDoc') != null) {
            $inventory = $inventory->where('sales_doc', $request->query('salesDoc'));
        }

        $inventory = $inventory->latest()->paginate(10);

        foreach ($inventory as $item) {
            $item->child = InventoryDetail::where('inventory_id', $item->id)->where('type', 'child')->count();
            $item->parent = InventoryDetail::where('inventory_id', $item->id)->where('type', 'parent')->count();
        }

        $title = 'Inventory';
        return view('inventory.index', compact('title', 'inventory'));
    }

    public function detail(Request $request): View
    {
        $inventory = Inventory::with('storage')->where('id', $request->query('id'))->first();
        $inventoryDetail = InventoryDetail::where('inventory_id', $request->query('id'))->where('type', 'parent')->get();
        foreach ($inventoryDetail as $item) {
            $item->child = InventoryDetail::with('purchaseOrderDetail')->where('parent_id', $item->id)->get();
            $item->parent = InventoryDetail::with('purchaseOrderDetail')->where('id', $item->id)->first();
        }

        $title = 'Inventory';
        return view('inventory.detail', compact('title', 'inventory', 'inventoryDetail'));
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
