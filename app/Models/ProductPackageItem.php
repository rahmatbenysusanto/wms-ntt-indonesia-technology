<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPackageItem extends Model
{
    protected $table = 'product_package_item';
    protected $fillable = [
        'product_package_id',
        'product_id',
        'purchase_order_detail_id',
        'is_parent',
        'direct_outbound',
        'qty'
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function purchaseOrderDetail(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PurchaseOrderDetail::class, 'purchase_order_detail_id');
    }

    public function productPackageItemSn(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductPackageItemSn::class, 'product_package_item_id');
    }
}
