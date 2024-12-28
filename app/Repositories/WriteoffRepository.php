<?php

namespace App\Repositories;

use App\Models\Products;
use App\Models\ProductWarehouse;
use App\Models\Writeoff;
use App\Models\PurchaseOrderItem;
use App\Models\Warehouses;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class WriteoffRepository
{
    public function writeoff_list($request)
    {
        $query = Writeoff::where('business_id', $request->business_id);
            if (isset($request->product_id) && !empty($request->product_id))
                $query = $query->where('product_id', $request->product_id);

            if (isset($request->warehouse_id) && !empty($request->warehouse_id))
                $query = $query->where('warehouse_id', $request->warehouse_id);

        $writeoffs = $query;

        return $writeoffs;
    }

    public function create($request)
    {
        $product = Products::find($request->product);

        $retail_price = 0;
        if ($product) {
            $retail_price =  $product->retail_price;
        }

        if($product){
            $product->qty = $product->qty - $request->qty;
            $product->update();
        }

        $writeoff = new Writeoff();
        $writeoff->product_id  = $request->product;
        $writeoff->retail_price = $retail_price;
        $writeoff->warehouse_id  = $request->warehouse;
        $writeoff->qty  = $request->qty;
        $writeoff->reason  = $request->reason;
        $writeoff->business_id = $request->business_id;
        $writeoff->save();

        $ref_no = refno_generate(16, 2, $writeoff->id);
        $writeoff->ref_no = $ref_no;
        $writeoff->update();

        $product_warehouse = ProductWarehouse::where('product_id', $request->product)->where('warehouse_id', $request->warehouse)->first();

        if ($product_warehouse) {
            $product_warehouse->qty = $product_warehouse->qty - $request->qty;
            $product_warehouse->update();
        }


        return [
            'id' => $writeoff->id,
            'product_id' => $writeoff->product,
            'warehouse_id' => $writeoff->warehouse,
            'qty' => $writeoff->qty,
            'reason' => $writeoff->reason,
            'retail_price' => $writeoff->retail_price
        ];
    }

    public function update($request)
    {
       $product = Products::find($request->product);

        $retail_price = 0;
        if ($product) {
            $retail_price =  $product->retail_price;
        }


        $writeoff = Writeoff::find($request->id);

        if($product){
            $product->qty = $product->qty + $writeoff->qty;
            $product->update();

            $product->qty = $product->qty - $request->qty;
            $product->update();
        }

        $writeoff->product_id  = $request->product;
        $writeoff->retail_price = $retail_price;
        $writeoff->warehouse_id  = $request->warehouse;
        $writeoff->qty  = $request->qty;
        $writeoff->reason  = $request->reason;
        $writeoff->business_id = $request->business_id;
        $writeoff->update();

        $product_warehouse = ProductWarehouse::where('product_id', $request->product)->where('warehouse_id', $request->warehouse)->first();

        if ($product_warehouse) {
            $product_warehouse->qty = $request->av_qty;
            $product_warehouse->update();

            $product_warehouse->qty = $product_warehouse->qty - $request->qty;
            $product_warehouse->update();
        }

        return [
            'status' => true,
            'message' => 'Selected Write off Updated Successfully!'
        ];
    }

    public function delete($request)
    {

        $writeoff = Writeoff::find($request->id);

        if (!$writeoff) {
            return [
                'status' => false,
                'message' => 'Write Off Not Found'
            ];
        }


        $product = Products::find($writeoff->product_id);

        if($product){
            $product->qty = $product->qty + $writeoff->qty;
            $product->update();
        }

        $product_warehouse = ProductWarehouse::where('product_id', $writeoff->product_id)
            ->where('warehouse_id', $writeoff->warehouse_id)
            ->first();

        if ($product_warehouse) {

            $product_warehouse->qty = $product_warehouse->qty + $writeoff->qty;
            $product_warehouse->update();
        }

        $writeoff->delete();

        return [
            'status' => true,
            'message' => 'Selected Write Off Deleted Successfully!'
        ];
    }


    public function get_warehouse($request)
    {
        $query = Warehouses::with('product_info');
        if (isset($request->product_info->name) && !empty($request->product_info->name))
            $query = $query->where('product_id', $request->product_info->name);

        if (isset($request->key_word) && !empty($request->key_word))
            $query = $query->where('warehouse', 'LIKE', '%' . $request->key_word . '%');

        if (isset($request->business_id) && !empty($request->business_id))
            $query = $query->where('business_id', $request->business_id);

        if (isset($request->id) && !empty($request->id))
            $query = $query->where('id', $request->id);

        $warehouse = $query->orderBy('name', 'ASC')->get();

        return $warehouse;
    }
}
