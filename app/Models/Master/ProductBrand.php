<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBrand extends Model
{
    use SoftDeletes; 

    protected $table = 'product_brands';

    public $primaryKey = 'id';

    public $keyType = 'string';

    public $incrementing = false;

    protected $guarded = ['created_at', 'updated_at', 'deleted_at'];
}
