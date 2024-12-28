<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchased_id',
        'product_id',
        'unit_price',
        'qty',
        'available_qty',
        'received_qty',
        'total_amount'
    ];

    public function product_info()
    {
        return $this->hasOne(Products::class, 'id','product_id');
    }

    public function purchase_info()
    {
        return $this->hasOne(PurchaseOrders::class, 'id','purchased_id');
    }

    public function pdf_product_info()
    {
        return $this->hasOne(Products::class, 'id','product_id')->withTrashed();
    }
}
