<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustments extends Model
{
    use HasFactory , SoftDeletes;

    public function stock_adjust_item()
    {
        return $this->hasMany(StockAdjustedItems::class, 'adjusted_id', 'id');
    }

    public function purchase_info()
    {
        return $this->hasOne(PurchaseOrders::class, 'id', 'purchased_id');
    }
}
