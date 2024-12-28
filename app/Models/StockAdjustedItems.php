<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustedItems extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'adjusted_id',
        'product_id',
        'warehouse_id',
        'order_item_id',
        'qty'
    ];

    public function Purchase_Item_info()
    {
        return $this->hasOne(PurchaseOrderItem::class, 'id', 'order_item_id');
    }

    public function Product_info()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    public function warehouse_info()
    {
        return $this->hasOne(Warehouses::class, 'id', 'warehouse_id');
    }

}
