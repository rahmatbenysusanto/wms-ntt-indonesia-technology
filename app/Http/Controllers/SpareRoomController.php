<?php

namespace App\Http\Controllers;

use App\Models\SpareRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SpareRoomController extends Controller
{
    public function index(): View
    {
        $spareRoom = DB::table('inventory_item')
            ->leftJoin('product', 'product.id', '=', 'inventory_item.product_id')
            ->leftJoin('purchase_order', 'purchase_order.purc_doc', '=', 'inventory_item.purc_doc')
            ->leftJoin('customer', 'customer.id', '=', 'purchase_order.customer_id')
            ->where('inventory_item.stock', '!=', 0)
            ->where('inventory_item.type', 'gr')
            ->select([
                'inventory_item.*',
                'product.material',
                'product.po_item_desc',
                'product.prod_hierarchy_desc',
                'customer.name AS customer_name',
            ])
            ->latest('inventory_item.created_at')
            ->paginate(10);

        $title = 'Spare Room';
        return view('spare-room.index', compact('title', 'spareRoom'));
    }
}
