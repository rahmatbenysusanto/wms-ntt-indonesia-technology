<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralRoom extends Model
{
    protected $table = 'general_room';
    protected $fillable = [
        'outbound_id',
        'number',
        'qty',
        'qty_item',
        'status'
    ];
}
