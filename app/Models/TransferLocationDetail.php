<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferLocationDetail extends Model
{
    protected $table = 'transfer_location_detail';
    protected $fillable = [
        'transfer_location_id',
        'type',
        'inventory_parent_detail_id',
        'inventory_child_detail_id',
        'qty'
    ];
}
