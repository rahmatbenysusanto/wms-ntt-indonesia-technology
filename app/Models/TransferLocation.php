<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferLocation extends Model
{
    protected $table = 'transfer_location';
    protected $fillable = [
        'number',
        'inventory_parent_id',
        'purc_doc',
        'old_location',
        'new_location',
        'created_by'
    ];
}
