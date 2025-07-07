<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryChildDetail extends Model
{
    protected $table = 'inventory_child_detail';
    protected $fillable = ['product_id', 'purchase_order_detail_id', 'inventory_child_id', 'sales_doc', 'qty'];
}
