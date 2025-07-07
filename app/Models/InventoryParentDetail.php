<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryParentDetail extends Model
{
    protected $table = 'inventory_parent_detail';
    protected $fillable = ['product_id', 'inventory_parent_id', 'purchase_order_detail_id', 'sales_doc', 'qty'];
}
