<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryDetail extends Model
{
    protected $table = 'inventory_detail';
    protected $fillable = [
        'inventory_id',
        'purchase_order_detail_id',
        'storage_id',
        'inventory_package_item_id',
        'sales_doc',
        'qty'
    ];

    public function purchaseOrderDetail(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderDetail::class, 'purchase_order_detail_id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id');
    }
}
