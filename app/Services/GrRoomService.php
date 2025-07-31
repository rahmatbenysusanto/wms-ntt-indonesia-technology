<?php

namespace App\Services;

use App\Models\GeneralRoom;
use App\Models\GeneralRoomDetail;
use App\Models\GeneralRoomDetailSN;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryItem;
use App\Models\InventoryPackage;
use App\Models\InventoryPackageItem;
use App\Models\OutboundDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Support\Facades\Auth;

class GrRoomService
{
    public function storeInventoryGR($request): void
    {
        $purchaseOrder = PurchaseOrder::find($request->products[0]['purchaseOrderId']);

        $checkInventory = Inventory::where('purchase_order_id', '')->where('type', 'inv')->first();
        if ($checkInventory == null) {
            $inventory = Inventory::create([
                'purchase_order_id' => $purchaseOrder->id,
                'stock'             => '',
                'type'              => 'inv',
            ]);
            $inventoryId = $inventory->id;
        } else {
            Inventory::where('id', $checkInventory->id)->increment('stock', '');
            $inventoryId = $checkInventory->id;
        }

        foreach ($request->products as $product) {
            if ($product['disable'] == 0) {

                $inventoryPackageItem = InventoryPackageItem::find($product['inventoryPackageItemId']);
                $purchaseOrderDetail = PurchaseOrderDetail::find($inventoryPackageItem->purchase_order_detail_id);

                InventoryDetail::create([
                    'inventory_id'              => $inventoryId,
                    'purchase_order_detail_id'  => $purchaseOrderDetail->id,
                    'storage_id'                => 2,
                    'inventory_package_item_id' => $product['inventoryPackageItemId'],
                    'sales_doc'                 => $purchaseOrderDetail->sales_doc,
                    'qty'                       => $product['qtySelect']
                ]);

                $checkInventoryItem = InventoryItem::where('purc_doc', $purchaseOrder->purc_doc)
                    ->where('sales_doc', $purchaseOrderDetail->sales_doc)
                    ->where('product_id', $inventoryPackageItem->product_id)
                    ->where('storage_id', 2)
                    ->first();
                if ($checkInventoryItem == null) {
                    InventoryItem::create([
                        'purc_doc'      => $purchaseOrder->purc_doc,
                        'sales_doc'     => $purchaseOrderDetail->sales_doc,
                        'product_id'    => $inventoryPackageItem->product_id,
                        'storage_id'    => 2,
                        'stock'         => $product['qtySelect'],
                        'type'          => 'gr'
                    ]);
                } else {
                    InventoryItem::where('id', $checkInventoryItem->id)->increment('stock', $product['qtySelect']);
                }
            }
        }
    }

    public function store($request, $outbound, $outboundDetailId, $serialNumber): void
    {
        $outboundDetail = OutboundDetail::find($outboundDetailId);
        $inventoryPackageItem = InventoryPackageItem::find($outboundDetail->inventory_package_item_id);

        $check = GeneralRoom::where('outbound_id', $outbound->id)->first();
        if ($check == null) {
            $generalRoom = GeneralRoom::create([
                'outbound_id'       => $outbound->id,
                'outbound_date'     => $request->outboundDate,
                'number'            => 'GR-' . date('ymdHis') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'qty_item'          => 1,
                'qty'               => $outboundDetail->qty,
                'status'            => 'new',
                'delivery_dest'     => $request->deliveryDest,
                'type'              => 'outbound',
                'created_by'        => Auth::id(),
            ]);
            $generalRoomId = $generalRoom->id;
        } else {
            GeneralRoom::find($check->id)->increment('qty_item', 1);
            GeneralRoom::find($check->id)->increment('qty', $outboundDetail->qty);
            $generalRoomId = $check->id;
        }

        $generalRoomDetail = GeneralRoomDetail::create([
            'general_room_id'       => $generalRoomId,
            'product_id'            => $inventoryPackageItem->product_id,
            'outbound_detail_id'    => $outboundDetailId,
            'qty'                   => $outboundDetail->qty,
        ]);

        foreach ($serialNumber as $sn) {
            GeneralRoomDetailSN::create([
                'general_room_detail_id' => $generalRoomDetail->id,
                'serial_number'          => $sn['serialNumber'],
            ]);
        }
    }
}
