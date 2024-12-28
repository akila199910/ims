<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\ProductWarehouse;
use App\Models\Warehouses;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class WareHouseRepository
{
    public function warehouse_list($request)
    {
        $warehouses = Warehouses::where('business_id', $request->business_id);

        return $warehouses;
    }

    public function create($request)
    {

        $ware_house =new Warehouses();
        $ware_house->name = $request->name;
        $ware_house->address = $request->address;
        $ware_house->contact  = $request->contact;
        $ware_house->status = $request->status == true ? 1 : 0;
        $ware_house->business_id = $request->business_id;
        $ware_house->save();

        $ref_no = refno_generate(16, 2, $ware_house->id);
        $ware_house->ref_no = $ref_no;
        $ware_house->update();
        return [
            'id' => $ware_house->id,
            'name' => $ware_house->name,
            'address' => $ware_house->address,
            'contact' => $ware_house->contact
        ];

   }

   public function update($request)
   {

        $ware_house = Warehouses::find($request->id);

        $ware_house->name = $request->name;
        $ware_house->address = $request->address;
        $ware_house->contact  = $request->contact;
        $ware_house->business_id= $request->business_id;
        $ware_house->status = $request->status==true ? 1 : 0;
        $ware_house->update();

        return [
            'status' => true,
            'message' => 'Selected Warehouse Updated Successfully!'
        ];

   }

   public function delete($request){

        $ware_house = Warehouses::find($request->id);

        if (!$ware_house) {
            return [
                'status' => false,
                'message' => 'Warehouse Not Found'
            ];
        }

        $ware_house->delete();

        return [
            'status' => true,
            'message' => 'Selected Warehouse Deleted Successfully!'
        ];
    }


   public function get_details($request)
    {
        $ware_house = Warehouses::find($request->id);

        $data = [
            'name' => $ware_house->name,
            'address' => $ware_house->address,
            'contact' => $ware_house->contact,
            'status' => $ware_house->status,
            'status_name' => $ware_house->status == 1 ? 'Active' : 'Inactive',
        ];

        return $data;
    }

    public function get_details_product($request)
    {
        $products = ProductWarehouse::find($request->id);

        $data = [
            'product' => $products->product_info->name,
            'qty' => $products->qty
        ];

        return $data;
    }

    public function warehouse_product_list($request)
    {
        $warehouse_product = ProductWarehouse::with(['product_info'])->whereIn('product_id',$request->products_id)->where('warehouse_id', $request->warehouse_id);

        return $warehouse_product;
    }
}
