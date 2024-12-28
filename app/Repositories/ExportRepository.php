<?php

namespace App\Repositories;

use App\Models\Business;
use App\Models\User;
use App\Models\Products;
use Illuminate\Support\Str;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class ExportRepository{

    public function lowStockExport($request)
    {
        $product_ids = Products::where('business_id', $request->business_id)->where('status', 1)->pluck('id')->toArray();

        $query = ProductWarehouse::with(['product_info', 'warehouse_info'])->whereIn('product_id', $product_ids)->where('qty', '<=', 'qty_alert');

        if (isset($request->product) && !empty($request->product))
            $query = $query->where('product_id', $request->product);

        if (isset($request->warehouse) && !empty($request->warehouse))
            $query = $query->where('warehouse_id', $request->warehouse);

        $lowStock = $query->orderBy('id', 'DESC')->get();

        return $lowStock;
    }
}
