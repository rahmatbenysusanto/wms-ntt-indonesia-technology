<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingOutboundDetail extends Model
{
    protected $table = 'pending_outbound_details';
    protected $fillable = [
        'pending_outbound_id',
        'purchase_order_detail_id',
        'inventory_package_item_id',
        'product_id',
        'sales_doc',
        'material',
        'item',
        'po_item_desc',
        'qty',
    ];

    public function pendingOutbound(): BelongsTo
    {
        return $this->belongsTo(PendingOutbound::class, 'pending_outbound_id', 'id');
    }

    public function purchaseOrderDetail(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderDetail::class, 'purchase_order_detail_id', 'id');
    }

    public function inventoryPackageItem(): BelongsTo
    {
        return $this->belongsTo(InventoryPackageItem::class, 'inventory_package_item_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
