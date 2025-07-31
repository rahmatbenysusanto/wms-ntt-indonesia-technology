<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GeneralRoom;
use App\Models\GeneralRoomDetail;
use App\Models\InventoryDetail;
use App\Models\InventoryItem;
use App\Models\InventoryPackage;
use App\Models\InventoryParent;
use App\Models\Outbound;
use App\Models\OutboundDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GeneralRoomController extends Controller
{
    public function index(): View
    {
        $generalRoom = DB::table('inventory_item')
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

        $title = "General Room";
        return view('general-room.index', compact('title', 'generalRoom'));
    }

    public function detail(Request $request): View
    {
        $generalRoom = GeneralRoom::where('id', $request->query('id'))->first();
        $outbound = Outbound::where('id', $generalRoom->outbound_id)->first();
        $generalRoomDetail = GeneralRoomDetail::with('product')->where('general_room_id', $generalRoom->id)->get();

        $title = "General Room";
        return view('general-room.detail', compact('title', 'generalRoom', 'generalRoomDetail', 'outbound'));
    }

    public function outboundAll(Request $request): \Illuminate\Http\JsonResponse
    {
        GeneralRoom::where('id', $request->post('id'))->update(['status' => 'outbound']);

        return response()->json([
            'status' => true
        ]);
    }

    public function outbound(): View
    {
        $generalRoom = Outbound::with('customer')->where('type', 'general room')->latest()->paginate(10);

        $title = "General Room Outbound";
        return view('general-room.outbound.index', compact('title', 'generalRoom'));
    }

    public function create(): View
    {
        $products = InventoryItem::with('product')
            ->where('type', 'gr')
            ->where('stock', '!=', 0)
            ->latest()
            ->get();

        $customer = Customer::all();

        $title = "General Room Outbound";
        return view('general-room.outbound.create', compact('title', 'customer', 'products'));
    }
}

































