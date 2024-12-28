<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ref_no',
        'supplier_id',
        'business_id',
        'name',
        'contact',
        'status'
    ];
}
