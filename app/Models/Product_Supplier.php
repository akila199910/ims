<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product_Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_suppliers';

    protected $fillable = [
        'product_id',
        'supplier_id',
        'status'
    ];

    public function product_info()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    public function supplier_Info()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

}
