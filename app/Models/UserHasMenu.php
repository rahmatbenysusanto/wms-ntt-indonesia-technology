<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHasMenu extends Model
{
    protected $table = 'user_has_menu';
    protected $fillable = ['user_id', 'menu_id'];
}
