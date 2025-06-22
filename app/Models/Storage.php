<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $table = 'storage';
    protected $fillable = ['raw', 'area', 'level', 'deleted_at'];
}
