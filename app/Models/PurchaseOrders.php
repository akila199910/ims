<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrders extends Model
{
    use HasFactory, SoftDeletes;


    public function supplier_Info()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function pur_orderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchased_id', 'id');
    }

    public function first_payement_info()
    {
        return $this->hasOne(PurchasePayements::class, 'purchased_id','id')->orderBy('id','ASC');
    }

    public function payment_list()
    {
        return $this->hasMany(PurchasePayements::class, 'purchased_id','id');
    }

    public function order_user_info()
    {
        return $this->hasOne(User::class, 'id', 'order_by');
    }

    public function modify_user_info()
    {
        return $this->hasOne(User::class,'id','modify_by');
    }

    public function approved_user_info()
    {
        return $this->hasOne(User::class,'id','approved_by');
    }

    public function business_info()
    {
        return $this->hasOne(Business::class, 'id','business_id');
    }
}
