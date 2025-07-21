<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryPackageItemSN extends Model
{
    protected $table = 'inventory_package_item_sn';
    protected $fillable = [
        'inventory_package_item_id',
        'serial_number',
        'qty'
    ];
}
