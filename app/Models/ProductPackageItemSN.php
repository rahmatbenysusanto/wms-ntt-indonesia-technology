<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPackageItemSN extends Model
{
    protected $table = 'product_package_item_sn';
    protected $fillable = [
        'product_package_item_id',
        'serial_number',
    ];
}
