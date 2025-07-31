<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpareRoomDetailSN extends Model
{
    protected $table = 'spare_room_detail_sn';
    protected $fillable = [
        'spare_room_detail_id',
        'serial_number',
    ];
}
