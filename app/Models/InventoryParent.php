<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryParent extends Model
{
    protected $table = 'inventory_parent';
    protected $fillable = ['product_id', 'purchase_order_id', 'storage_id', 'pa_number', 'pa_reff_number', 'stock', 'sales_docs'];
}
