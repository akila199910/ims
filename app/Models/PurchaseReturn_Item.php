<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturn_Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'return_id',
        'product_id',
        'qty',
        'order_item_id',
        'unit_price',
        'total_amount'
    ];

    public function Purchase_Item_info()
    {
        return $this->hasOne(PurchaseOrderItem::class, 'id', 'order_item_id');
    }

    public function Product_info()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    public function purchase_info()
    {
        return $this->hasOne(PurchaseOrders::class, 'id', 'purchased_id');
    }




}
