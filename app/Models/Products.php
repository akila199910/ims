<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use HasFactory, SoftDeletes;

    public function category_info()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function sub_category_info()
    {
        return $this->hasOne(SubCategory::class, 'id', 'subcategory_id');
    }

    public function unit_info()
    {
        return $this->hasOne(Units::class, 'id', 'unit_id');
    }

    public function ware_houses()
    {
        return $this->hasMany(ProductWarehouse::class, 'product_id', 'id');
    }

    public function supplier()
    {
       return $this->hasMany(Product_Supplier::class,  'product_id', 'id');
    }

    public function unit_info_pdf()
    {
        return $this->hasOne(Units::class, 'id', 'unit_id')->withTrashed();
    }
}
