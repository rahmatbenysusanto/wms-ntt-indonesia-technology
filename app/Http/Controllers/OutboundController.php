<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Outbound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OutboundController extends Controller
{
    public function index(Request $request): View
    {
        $outbound = Outbound::latest()->paginate(10);

        $title = 'Outbound';
        return view('outbound.index', compact('title', 'outbound'));
    }

    public function create(): View
    {
        $inventory = Inventory::whereNot('qty_item', 0)
            ->select([
                'sales_doc',
                DB::raw('MAX(purc_doc) AS purc_doc'),
            ])
            ->groupBy('sales_doc')
            ->get();

        $title = 'Outbound';
        return view('outbound.create', compact('title', 'inventory'));
    }

    public function getItemBySalesDoc(Request $request): \Illuminate\Http\JsonResponse
    {
        $products = Inventory::with('storage')
            ->where('sales_doc', $request->get('salesDoc'))
            ->where('qty_item', '!=', 0)
            ->get();

        foreach ($products as $product) {
            $listParent = InventoryDetail::with('purchaseOrderDetail')->where('inventory_id', $product->id)->where('type', 'parent')->get();
            foreach ($listParent as $parent) {
                $parent->child = InventoryDetail::with('purchaseOrderDetail')
                    ->where('parent_id', $parent->id)
                    ->where('type', 'child')
                    ->get();
            }

            $product->products = $listParent;
        }

        return response()->json([
            'data' => $products
        ]);
    }
}
