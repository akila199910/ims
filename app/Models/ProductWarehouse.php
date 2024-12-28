<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductWarehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'qty',
        'qty_alert',
        'status'
    ];

    public function product_info()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    public function warehouse_info()
    {
        return $this->hasOne(Warehouses::class, 'id', 'warehouse_id');
    }
}
