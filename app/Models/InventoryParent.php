<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryParent extends Model
{
    protected $table = 'inventory_parent';
    protected $fillable = ['product_id', 'purchase_order_id', 'storage_id', 'pa_number', 'pa_reff_number', 'stock', 'sales_docs'];

    public function inventoryParentDetail(): HasMany
    {
        return $this->hasMany(InventoryParentDetail::class, 'inventory_parent_id', 'id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function inventoryChild(): HasMany
    {
        return $this->hasMany(InventoryChild::class, 'inventory_parent_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }
}
