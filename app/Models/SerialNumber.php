<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerialNumber extends Model
{
    protected $table = 'serial_number';
    protected $fillable = [
        'purchase_order_id',
        'purchase_order_detail_id',
        'product_id',
        'product_parent_id',
        'product_parent_detail_id',
        'product_child_id',
        'product_child_detail_id',
        'child_parent_id',
        'child_parent_detail_id',
        'child_child_id',
        'child_child_detail_id',
        'serial_number',
        'qty'
    ];
}
