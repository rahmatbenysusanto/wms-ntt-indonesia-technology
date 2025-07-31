<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralRoomDetailSN extends Model
{
    protected $table = 'general_room_detail_sn';
    protected $fillable = [
        'general_room_detail_id',
        'serial_number',
    ];
}
