<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $table = 'storage';
    protected $fillable = ['type', 'raw', 'area', 'rak', 'bin', 'deleted_at'];
}
