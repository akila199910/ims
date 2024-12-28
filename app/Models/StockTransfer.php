<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransfer extends Model
{
    use HasFactory, SoftDeletes;

    public function from_warehouse()
    {
        return $this->hasOne(Warehouses::class, 'id', 'warehouse_from');
    }

    public function to_warehouse()
    {
        return $this->hasOne(Warehouses::class, 'id', 'warehouse_to');
    }

    public function creator_info()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function editor_info()
    {
        return $this->hasOne(User::class, 'id', 'edit_by');
    }

    public function product_info()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
