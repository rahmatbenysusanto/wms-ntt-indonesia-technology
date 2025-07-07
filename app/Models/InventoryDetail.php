<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryDetail extends Model
{
    protected $table = 'inventory_detail';
    protected $fillable = [
        'purchase_order_detail_id',
        'storage_id',
        'stock'
    ];
}
