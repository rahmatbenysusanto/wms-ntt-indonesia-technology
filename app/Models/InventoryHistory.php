<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryHistory extends Model
{
    protected $table = 'inventory_history';
    protected $fillable = [
        'purchase_order_id',
        'purchase_order_detail_id',
        'outbound_id',
        'inventory_package_item_id',
        'qty',
        'type',
        'created_by',
        'note',
        'serial_number'
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function purchaseOrderDetail(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderDetail::class, 'purchase_order_detail_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function outbound(): BelongsTo
    {
        return $this->belongsTo(Outbound::class, 'outbound_id');
    }

    public function inventoryPackageItem(): BelongsTo
    {
        return $this->belongsTo(InventoryPackageItem::class, 'inventory_package_item_id');
    }
}
