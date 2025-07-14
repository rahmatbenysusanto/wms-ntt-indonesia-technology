<?php

namespace App\Http\Controllers;

use App\Models\GeneralRoom;
use App\Models\OutboundDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GeneralRoomController extends Controller
{
    public function index(): View
    {
        $generalRoom = DB::table('general_room')
            ->leftJoin('outbound', 'outbound.id', '=', 'general_room.outbound_id')
            ->select([
                'general_room.id',
                'general_room.number',
                'general_room.qty_item',
                'general_room.status',
                'outbound.id As outbound_id',
                'outbound.purc_doc',
                'outbound.sales_doc'
            ])
            ->latest('general_room.created_at')
            ->paginate(10);

        foreach ($generalRoom as $item) {
            $outboundDetail = OutboundDetail::where('outbound_id', $item->outbound_id)
                ->leftJoin('product', 'product.id', '=', 'outbound_detail.product_id')
                ->where('inventory_parent_id', '!=', null)
                ->select([
                    'product.material',
                    'product.po_item_desc'
                ])
                ->first();

            $item->material = $outboundDetail->material;
            $item->po_item_desc = $outboundDetail->po_item_desc;
        }

        $title = "General Room";
        return view('general-room.index', compact('title', 'generalRoom'));
    }
}
