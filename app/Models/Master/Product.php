<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\ProductImage;
use App\Models\Master\ProductSize;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\ProductBrand;
use App\Models\Master\ProductCategories;
use App\Models\Master\ProductUnit;
use App\Models\Master\ProductVariant;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = ['created_at', 'updated_at', 'deleted_at'];

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategories::class, 'category_id');
    }

    public function largeUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'large_unit_id');
    }
    
    public function smallUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'small_unit_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
