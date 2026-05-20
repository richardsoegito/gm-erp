<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    protected $table = 'product_sizes';
    protected $guarded = ['created_at', 'updated_at'];
}
