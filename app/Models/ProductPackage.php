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

    public function productPackageItem(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductPackageItem::class);
    }

    public function purchaseOrder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
