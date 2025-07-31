<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmRoomDetail extends Model
{
    protected $table = 'pm_room_detail';
    protected $fillable = [
        'pm_room_id',
        'product_id',
        'outbound_detail_id',
        'qty'
    ];
}
