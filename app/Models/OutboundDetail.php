<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundDetail extends Model
{
    protected $table = 'outbound_detail';
    protected $fillable = [
        'outbound_id',
        'product_id',
        'inventory_parent_id',
        'inventory_parent_detail_id',
        'inventory_child_id',
        'inventory_child_detail_id',
        'qty',
        'serial_number'
    ];
}
