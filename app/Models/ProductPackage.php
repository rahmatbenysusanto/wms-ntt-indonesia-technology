<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPackage extends Model
{
    protected $table = 'product_package';
    protected $fillable = [
        'purchase_order_id',
        'qty_item',
        'qty',
        'status',
        'created_by'
    ];

    public function purchaseOrder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
