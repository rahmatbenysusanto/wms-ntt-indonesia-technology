<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductChild extends Model
{
    protected $table = 'product_child';
    protected $fillable = ['product_parent_id', 'product_id', 'purchase_order_id', 'qty'];
}
