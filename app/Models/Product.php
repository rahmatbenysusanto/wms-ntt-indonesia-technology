<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $fillable = [
        'master',
        'product_master_id',
        'material',
        'po_item_desc',
        'prod_hierarchy_desc',
    ];
}
