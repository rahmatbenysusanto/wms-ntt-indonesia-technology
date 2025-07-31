<?php

namespace App\Services;

use App\Models\InventoryPackageItem;
use App\Models\OutboundDetail;
use App\Models\PmRoom;
use App\Models\PmRoomDetail;
use App\Models\PmRoomDetailSN;
use Illuminate\Support\Facades\Auth;

class PmRoomService
{
    public function store($request, $outbound, $outboundDetailId, $serialNumber): void
    {
        $outboundDetail = OutboundDetail::find($outboundDetailId);
        $inventoryPackageItem = InventoryPackageItem::find($outboundDetail->inventory_package_item_id);

        $check = PmRoom::where('outbound_id', $outbound->id)->first();
        if ($check == null) {
            $pmRoom = PmRoom::create([
                'outbound_id'       => $outbound->id,
                'outbound_date'     => $request->outboundDate,
                'number'            => 'PMR-' . date('ymdHis') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'qty_item'          => 1,
                'qty'               => $outboundDetail->qty,
                'status'            => 'new',
                'delivery_dest'     => $request->deliveryDest,
                'type'              => 'outbound',
                'created_by'        => Auth::id(),
            ]);
            $pmRoomId = $pmRoom->id;
        } else {
            PmRoom::find($check->id)->increment('qty_item', 1);
            PmRoom::find($check->id)->increment('qty', $outboundDetail->qty);
            $pmRoomId = $check->id;
        }

        $pmRoomDetail = PmRoomDetail::create([
            'pm_room_id'            => $pmRoomId,
            'product_id'            => $inventoryPackageItem->product_id,
            'outbound_detail_id'    => $outboundDetailId,
            'qty'                   => $outboundDetail->qty,
        ]);

        foreach ($serialNumber as $sn) {
            PmRoomDetailSN::create([
                'pm_room_detail_id'      => $pmRoomDetail->id,
                'serial_number'          => $sn['serialNumber'],
            ]);
        }
    }
}
