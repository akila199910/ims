<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasePayements extends Model
{
    use HasFactory, SoftDeletes;


    public function payment_type_info()
    {
        return $this->hasOne(PaymentType::class, 'id' ,'pay_type_id');
    }

    public function purchase_info()
    {
        return $this->hasOne(PurchaseOrders::class, 'id', 'purchased_id');
    }
}
