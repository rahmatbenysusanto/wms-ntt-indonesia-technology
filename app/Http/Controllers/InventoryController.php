<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $inventory = Inventory::with('storage')->latest()->paginate(10);

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
}
