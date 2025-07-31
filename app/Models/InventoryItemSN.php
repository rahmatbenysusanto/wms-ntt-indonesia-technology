<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItemSN extends Model
{
    protected $table = "inventory_item_sn";
    protected $fillable = [
        'inventory_item_id',
        'serial_number',
    ];
}
