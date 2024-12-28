<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    public function payment_information()
    {
        return $this->hasOne(SupplierPaymentInfo::class, 'supplier_id', 'id');
    }

    public function supplier_contacts()
    {
        return $this->hasMany(SupplierContact::class, 'supplier_id', 'id');
    }

   
}
