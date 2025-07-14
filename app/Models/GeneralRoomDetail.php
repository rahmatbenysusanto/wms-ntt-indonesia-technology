<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralRoomDetail extends Model
{
    protected $table = 'general_room_detail';
    protected $fillable = [
        'general_room_id',
        'product_id',
        'inventory_parent_id',
        'inventory_parent_detail_id',
        'inventory_child_id',
        'inventory_child_detail_id',
        'qty',
        'serial_number'
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
