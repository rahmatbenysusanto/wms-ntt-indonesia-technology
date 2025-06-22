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
        'serial_number'
    ];
}
