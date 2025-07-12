<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductParent extends Model
{
    protected $table = 'product_parent';
    protected $fillable = ['product_id', 'purchase_order_id', 'storage_id', 'qty', 'pa_number'];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id');
    }

    public function productParentDetail(): HasMany
    {
        return $this->hasMany(ProductParentDetail::class, 'product_parent_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productChild(): HasMany
    {
        return $this->hasMany(ProductChild::class, 'product_parent_id');
    }
}
