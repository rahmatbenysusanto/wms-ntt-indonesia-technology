<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductParent extends Model
{
    protected $table = 'product_parent';
    protected $fillable = ['product_id', 'purchase_order_id', 'storage_id', 'qty'];
}
