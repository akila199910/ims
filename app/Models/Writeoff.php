<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Writeoff extends Model
{
    use HasFactory, SoftDeletes;

    public function Product_info()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    public function WareHouse_info()
    {
        return $this->hasOne(Warehouses::class, 'id', 'warehouse_id');
    }
}
