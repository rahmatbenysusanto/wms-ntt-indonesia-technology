<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryChild extends Model
{
    protected $table = 'inventory_child';
    protected $fillable = ['product_id', 'purchase_order_id', 'inventory_parent_id', 'stock'];

    public function inventoryChildDetail(): BelongsTo
    {
        return $this->belongsTo(InventoryChildDetail::class, 'id', 'inventory_child_id');
    }
}
