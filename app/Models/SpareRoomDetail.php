<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpareRoomDetail extends Model
{
    protected $table = 'spare_room_detail';
    protected $fillable = [
        'spare_room_id',
        'product_id',
        'outbound_detail_id',
        'qty'
    ];
}
