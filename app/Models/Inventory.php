<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $fillable = [
        'purchase_order_id',
        'stock',
        'type'
    ];
}
