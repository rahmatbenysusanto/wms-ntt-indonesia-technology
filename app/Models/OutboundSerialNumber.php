<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundSerialNumber extends Model
{
    protected $table = 'outbound_serial_number';
    protected $fillable = ['outbound_detail_id', 'serial_number_id'];
}
