<?php

namespace App\Services;

use App\Models\InventoryPackageItem;
use App\Models\OutboundDetail;
use App\Models\SpareRoom;
use App\Models\SpareRoomDetail;
use App\Models\SpareRoomDetailSN;
use Illuminate\Support\Facades\Auth;

class SpareRoomService
{
    public function store($request, $outbound, $outboundDetailId, $serialNumber): void
    {
        $outboundDetail = OutboundDetail::find($outboundDetailId);
        $inventoryPackageItem = InventoryPackageItem::find($outboundDetail->inventory_package_item_id);

        $check = SpareRoom::where('outbound_id', $outbound->id)->first();
        if ($check == null) {
            $spareRoom = SpareRoom::create([
                'outbound_id'       => $outbound->id,
                'outbound_date'     => $request->outboundDate,
                'number'            => 'SPR-' . date('ymdHis') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'qty_item'          => 1,
                'qty'               => $outboundDetail->qty,
                'status'            => 'new',
                'delivery_dest'     => $request->deliveryDest,
                'type'              => 'outbound',
                'created_by'        => Auth::id(),
            ]);
            $spareRoomId = $spareRoom->id;
        } else {
            SpareRoom::find($check->id)->increment('qty_item', 1);
            SpareRoom::find($check->id)->increment('qty', $outboundDetail->qty);
            $spareRoomId = $check->id;
        }

        $spareRoomDetail = SpareRoomDetail::create([
            'spare_room_id'         => $spareRoomId,
            'product_id'            => $inventoryPackageItem->product_id,
            'outbound_detail_id'    => $outboundDetailId,
            'qty'                   => $outboundDetail->qty,
        ]);

        foreach ($serialNumber as $sn) {
            SpareRoomDetailSN::create([
                'spare_room_detail_id'   => $spareRoomDetail->id,
                'serial_number'          => $sn['serialNumber'],
            ]);
        }
    }
}
