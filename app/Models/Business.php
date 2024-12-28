<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ref_no',
        'name',
        'email',
        'contact',
        'address',
        'status',

    ];

    public function BusinessUsers(){
        return $this->hasMany(BusinessUsers::class, 'business_id','id');
    }
}
