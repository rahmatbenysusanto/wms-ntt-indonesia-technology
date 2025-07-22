<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundDetailSN extends Model
{
    protected $table = 'outbound_detail_sn';
    protected $fillable = [
        'outbound_detail_id',
        'inventory_package_item_id',
        'serial_number'
    ];
}
