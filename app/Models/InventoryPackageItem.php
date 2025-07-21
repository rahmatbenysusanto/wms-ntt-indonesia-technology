<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryPackageItem extends Model
{
    protected $table = 'inventory_package_item';
    protected $fillable = [
        'inventory_package_id',
        'product_id',
        'purchase_order_detail_id',
        'is_parent',
        'direct_outbound',
        'qty'
    ];

    public function purchaseOrderDetail(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PurchaseOrderDetail::class);
    }
}
