<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouses extends Model
{
    use HasFactory, SoftDeletes;

    public function product_info()
    {
        return $this->hasOne(Products::class, 'id','product_id');
    }


}
