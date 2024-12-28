<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ref_no',
        'name',
        'category_id',
        'status',
        'image',
        'file_path',
        'business_id'

    ];

    public function CategoryInfo()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
