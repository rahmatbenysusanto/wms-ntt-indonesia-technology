<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductParentDetail extends Model
{
    protected $table = 'product_parent_detail';
    protected $fillable = ['product_parent_id', 'product_id', 'purchase_order_detail_id', 'sales_doc', 'qty'];
}
