<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    protected $table = 'inventory_history';
    protected $fillable = [
        'inventory_id',
        'inventory_detail_id',
        'quality_control_id',
        'quality_control_detail_id',
        'quality_control_item_id',
        'outbound_id',
        'outbound_detail_id',
        'type',
        'qty'
    ];
}
