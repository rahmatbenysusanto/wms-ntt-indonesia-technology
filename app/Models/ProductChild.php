<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductChild extends Model
{
    protected $table = 'product_child';
    protected $fillable = ['product_parent_id', 'product_id', 'purchase_order_id', 'qty'];

    public function productChildDetail(): HasMany
    {
        return $this->hasMany(ProductChildDetail::class, 'product_child_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
