<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $table = 'inventory_item';
    protected $fillable = [
        'purc_doc',
        'sales_doc',
        'product_id',
        'stock',
        'storage_id',
    ];
}
