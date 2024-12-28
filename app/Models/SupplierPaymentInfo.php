<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierPaymentInfo extends Model
{
    use HasFactory,SoftDeletes;

    public function PaymentTermsInfo()
    {
        return $this->hasOne(PaymentTerms::class, 'id' ,'payement_term');
    }
}
