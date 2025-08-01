<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryPackage extends Model
{
    protected $table = 'inventory_package';
    protected $fillable = [
        'purchase_order_id',
        'storage_id',
        'number',
        'reff_number',
        'qty_item',
        'qty',
        'sales_docs',
        'product_package_id',
        'created_by'
    ];

    public function inventoryPackageItem(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InventoryPackageItem::class, 'inventory_package_id', 'id');
    }

    public function purchaseOrder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function storage(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
