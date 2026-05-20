<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $guarded = ['created_at', 'updated_at'];
}
