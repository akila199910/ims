<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturn extends Model
{
    use HasFactory, SoftDeletes;


    public function pur_return_item()
    {
        return $this->hasMany(PurchaseReturn_Item::class, 'return_id', 'id');
    }

    public function purchase_info()
    {
        return $this->hasOne(PurchaseOrders::class, 'id', 'purchased_id');
    }

    public function pur_orderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchased_id', 'purchased_id');
    }

    public function business_info()
    {
        return $this->hasOne(Business::class, 'id','business_id');
    }


}
