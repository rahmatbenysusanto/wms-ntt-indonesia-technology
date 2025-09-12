<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $fillable = ['name', 'type'];

    public function userHasMenu(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserHasMenu::class);
    }
}
