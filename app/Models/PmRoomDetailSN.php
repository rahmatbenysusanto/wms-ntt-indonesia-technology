<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmRoomDetailSN extends Model
{
    protected $table = 'pm_room_detail_sn';
    protected $fillable = [
        'pm_room_detail_id',
        'serial_number',
    ];
}
