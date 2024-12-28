<?php

namespace App\Repositories;

use App\Models\Product_Supplier;
use App\Models\Products;
use App\Models\ProductWarehouse;

class ProductRepository

{
    public function product_list($request)
    {
        $query = Products::with(['category_info','sub_category_info','unit_info']);
                if(isset($request->business_id) && !empty($request->business_id))
                    $query = $query->where('business_id',$request->business_id);

                if(isset($request->category) && !empty($request->category))
                    $query = $query->where('category_id',$request->category);

                if(isset($request->sub_category) && !empty($request->sub_category))
                    $query = $query->where('subcategory_id',$request->sub_category);

                if(isset($request->unit) && !empty($request->unit))
                    $query = $query->where('unit_id',$request->unit);

        $products = $query;

        return $products;
    }

    public function create_product($request)
    {
       $file = '';
        if (isset($request->image) && $request->image->getClientOriginalName()) {
            // $file = file_upload($request->image, 'products');
            $file = resize_file_upload($request->image, 'products', 500, 500);
        }

        $product = new Products();
        $product->business_id = $request->business_id;
        $product->name = $request->product_name;
        $product->retail_price = $request->retail_price;
        $product->category_id = $request->category;
        $product->subcategory_id = $request->sub_category;
        $product->unit_id = $request->units;
        $product->sort_description = $request->sort_description;
        $product->description = $request->full_description;
        $product->image = $file;
        $product->status = $request->status == true ? 1 : 0;
        $product->save();

        $product_id = $product->id;
        $formatted_product_id = auto_increment_id($product_id);
        $product->product_id = $formatted_product_id;
        $product->update();

        // Generate Reference Number for supplier
        $ref_no = refno_generate(16, 2, $product->id);
        $product->ref_no = $ref_no;
        $product->update();

        if (isset($request->warehouses) && !empty($request->warehouses)) {

            foreach ($request->warehouses as $key => $value) {
                $product_warehouse = new ProductWarehouse();
                $product_warehouse->product_id = $product->id;
                $product_warehouse->warehouse_id = $value;
                $product_warehouse->qty = 0;
                $product_warehouse->qty_alert = $request->alert_qty;
                $product_warehouse->status = $request->status == true ? 1 : 0;
                $product_warehouse->save();
            }

        }

        if (isset($request->vendors) && !empty($request->vendors)) {

            foreach ($request->vendors as $key => $value) {
                Product_Supplier::updateOrCreate([
                    'product_id' => $product->id,
                    'supplier_id' => $value,
                    'status' => 1
                ]);
            }

        }

        return [
            'status' => true,
            'message' => 'New Product created successfully!'
        ];
    }

    public function product_info($request)
    {
        $status = false;
        $data = [];

        $product = Products::with(['ware_houses', 'category_info','sub_category_info','unit_info','supplier'])->where('business_id',$request->business_id)->where('ref_no', $request->product_id)->first();

        if ($product) {
            $status = true;

            $ware_houses = [];
            $ware_house_ids = [];
            $qty_alert = 0;

            if (isset($product->ware_houses) && !empty($product->ware_houses)) {

                foreach($product->ware_houses as $warehouse)
                {
                    $ware_house_ids[] = $warehouse->warehouse_id;
                    $qty_alert = $warehouse->qty_alert;

                    $ware_houses[] = [
                        'id' => $warehouse->warehouse_info->id,
                        'warehouse_id' => $warehouse->warehouse_info->ref_no,
                        'name' => $warehouse->warehouse_info->name
                    ];
                }
            }

            $suppliers = [];
            $supplier_ids = [];

            if (isset($product->supplier) && !empty($product->supplier)) {

                foreach($product->supplier as $supplier)
                {
                    $supplier_ids[] = $supplier->supplier_id;

                    $suppliers[] = [
                        'id' => $supplier->supplier_Info->id,
                        'supplier_id' => $supplier->supplier_Info->ref_no,
                        'name' => $supplier->supplier_Info->name
                    ];
                }
            }


            $data = [
                'id' => $product->id,
                'product_id' => $product->ref_no,
                'product_no' => $product->product_id,
                'name' => $product->name,
                'retail_price' => $product->retail_price,
                'category_id' => $product->category_id,
                'category_name' => isset($product->category_info) ? $product->category_info->name : null,
                'subcategory_id' => $product->subcategory_id,
                'subcategory_name' => isset($product->sub_category_info) ? $product->sub_category_info->name : null,
                'unit_id' => $product->unit_id,
                'unit_name' => $product->unit_info->name,
                'qty_alert' => $qty_alert,
                'status' => $product->status,
                'status_name' => $product->status == 1 ? 'Active' : 'Inactive',
                'sort_description' => $product->sort_description,
                'description' => $product->description,
                'image' => $product->image == '' || $product->image == 0 ? asset('layout_style/img/icons/product_100.png') : config('aws_url.url').$product->image,
                'ware_houses' => $ware_houses,
                'ware_house_ids' => $ware_house_ids,
                'supplier' => $suppliers,
                'supplier_ids' => $supplier_ids,

            ];
        }

        return [
            'status' => $status,
            'data' => $data
        ];
    }

    public function update_product($request)
    {
        $product = Products::find($request->id);

        $file = '';
        if (isset($request->image) && $request->image->getClientOriginalName()) {
            // $file = file_upload($request->image, 'products');
            $file = resize_file_upload($request->image, 'products', 500, 500);
        }
        else
        {
            if (!$product->image)
                $file = '';
            else
                $file = $product->image;
        }

        $product->name = $request->product_name;
        $product->retail_price = $request->retail_price;
        $product->category_id = $request->category;
        $product->subcategory_id = $request->sub_category;
        $product->unit_id = $request->units;
        $product->sort_description = $request->sort_description;
        $product->description = $request->full_description;
        $product->image = $file;
        $product->status = $request->status == true ? 1 : 0;
        $product->update();

        if (isset($request->warehouses) && !empty($request->warehouses)) {

            foreach ($request->warehouses as $key => $value) {
                ProductWarehouse::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'warehouse_id' => $value
                    ],
                    [
                        'qty_alert' => $request->alert_qty,
                        'status' => $request->status == true ? 1 : 0
                    ]
                );
            }

            $product->ware_houses()->where('product_id',$product->id)->whereNotIn('warehouse_id',$request->warehouses)->delete();
        }

        if (isset($request->vendors) && !empty($request->vendors)) {

            foreach ($request->vendors as $key => $value) {
                Product_Supplier::updateOrCreate([
                    'product_id' => $product->id,
                    'supplier_id' => $value,
                    'status' => 1
                ]);
            }

            $product->supplier()->where('product_id',$product->id)->whereNotIn('supplier_id',$request->vendors)->delete();

        }

        return [
            'status' => true,
            'message' => 'Selected Product Updated Successfully!'
        ];
    }

    public function delete_product($request)
    {
        $product = Products::find($request->id);

        // Delete the product and belongs joins
        $product->ware_houses()->delete();
        $product->delete();

        return true;
    }

    public function warehouse_list($request)
    {
        $warehouse_list = ProductWarehouse::with(['warehouse_info'])->where('product_id', $request->product_id);

        return $warehouse_list;
    }

    public function get_details($request)
    {
        $ware_houses = ProductWarehouse::find($request->id);

        $data = [
            'warehouse' => $ware_houses->warehouse_info->name,
            'qty' => $ware_houses->qty
        ];

        return $data;
    }
}
