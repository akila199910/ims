<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalHistory extends Model
{
    use HasFactory, SoftDeletes;

    public function pur_order_Info()
    {
        return $this->hasOne(PurchaseOrders::class, 'id', 'order_id');
    }

    public function order_user_info()
    {
        return $this->hasOne(User::class, 'id', 'order_by');
    }

    public function modify_user_info()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function pur_return_Info()
    {
        return $this->hasOne(PurchaseReturn::class, 'id', 'return_id');
    }

}
