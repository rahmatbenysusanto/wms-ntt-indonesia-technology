<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPackageItem extends Model
{
    protected $table = 'product_package_item';
    protected $fillable = [
        'product_package_id',
        'product_id',
        'purchase_order_detail_id',
        'is_parent',
        'direct_outbound',
        'qty'
    ];
}
