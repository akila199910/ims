<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Storage;

class SubcategoryRepository
{

    public function sub_category_list($request)
    {
        $sub_categories = SubCategory::with(['CategoryInfo'])->where('business_id', $request->business_id);

        return $sub_categories;
    }


    public function create($request)
    {
        $file = '';
        if (isset($request->image) && $request->image->getClientOriginalName()) {
            // $file = file_upload($request->image, 'categories');
            $file = resize_file_upload($request->image, 'categories', 500, 500);
        }

        $sub_category=new SubCategory();
        $sub_category->name = $request->name;
        $sub_category->category_id = $request->category;
        $sub_category->image = $file;
        $sub_category->status = $request->status == true ? 1 : 0;
        $sub_category->business_id = $request->business_id;
        $sub_category->save();

        $ref_no = refno_generate(16, 2, $sub_category->id);
        $sub_category->ref_no = $ref_no;
        $sub_category->update();
        return [
            'id' => $sub_category->id,
            'name' => $sub_category->name,
            'image' => $file,
            'category_id' => $sub_category->category
        ];

   }

   public function update($request)
   {

        $sub_category=SubCategory::find($request->id);

        $file = '';
        if (isset($request->image) && $request->image->getClientOriginalName()) {
            // $file = file_upload($request->image, 'categories');
            $file = resize_file_upload($request->image, 'categories', 500, 500);
        }
        else
        {
            if (!$sub_category->image)
                $file = '';
            else
                $file = $sub_category->image;
        }

        $sub_category->name = $request->name;
        $sub_category->image = $file ;
        $sub_category->category_id = $request->category;
        $sub_category->business_id= $request->business_id;
        $sub_category->status = $request->status==true ? 1 : 0;
        $sub_category->update();

        return [
            'status' => true,
            'message' => 'Selected Sub Category Updated Successfully!'
        ];

   }

   public function delete($request){

        $sub_category = SubCategory::find($request->id);

        if (!$sub_category) {
            return [
                'status' => false,
                'message' => 'Sub Category Not Found'
            ];
        }

        $sub_category->delete();

        return [
            'status' => true,
            'message' => 'Selected Sub Category Deleted Successfully!'
        ];
    }


   public function get_details($request)
    {
        $sub_category = SubCategory::find($request->id);

        $data = [
            'name' => $sub_category->name,
            'category_id' => $sub_category->CategoryInfo->name,
            'status' => $sub_category->status,
            'status_name' => $sub_category->status == 1 ? 'Active' : 'Inactive',
        ];

        return $data;
    }
}
