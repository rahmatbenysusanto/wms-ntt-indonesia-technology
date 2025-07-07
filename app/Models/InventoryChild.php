<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryChild extends Model
{
    protected $table = 'inventory_child';
    protected $fillable = ['product_id', 'purchase_order_id', 'inventory_parent_id', 'stock'];
}
