<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundDetail extends Model
{
    protected $table = 'outbound_detail';
    protected $fillable = [
        'outbound_id',
        'product_id',
        'inventory_id',
        'inventory_detail_id',
        'qty'
    ];
}
