<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryRepository
{

    public function category_list($request)
    {
        $categories = Category::where('business_id', $request->business_id);

        return $categories;
    }

    public function create($request)
    {

        $file = '';
        if (isset($request->image) && $request->image->getClientOriginalName()) {
            // $file = file_upload($request->image, 'categories');
            $file = resize_file_upload($request->image, 'categories', 500, 500);
        }

        $category=new Category();
        $category->name = $request->name;
        $category->image = $file;
        $category->business_id = $request->business_id;
        $category->status = $request->status == true ? 1 : 0;
        $category->save();

        $ref_no = refno_generate(16, 2, $category->id);
        $category->ref_no = $ref_no;
        $category->update();

        return [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $file
        ];

   }

   public function update($request)
   {

        $category=Category::find($request->id);

        $file = '';
        if (isset($request->image) && $request->image->getClientOriginalName()) {
            // $file = file_upload($request->image, 'categories');
            $file = resize_file_upload($request->image, 'categories', 500, 500);
        }
        else
        {
            if (!$category->image)
                $file = '';
            else
                $file = $category->image;
        }

        $category->name = $request->name;
        $category->image = $file;
        $category->business_id= $request->business_id;
        $category->status = $request->status==true ? 1 : 0;
        $category->update();

        return [
            'status' => true,
            'message' => 'Selected Category Updated Successfully!'
        ];

   }

   public function delete($request){

        $category = Category::find($request->id);

        if (!$category) {
            return [
                'status' => false,
                'message' => 'Location Not Found'
            ];
        }

        $category->delete();

        return [
            'status' => true,
            'message' => 'Selected Category Deleted Successfully!'
        ];
    }


   public function get_details($request)
    {
        $category = Category::find($request->id);

        $data = [
            'name' => $category->name,
            'status' => $category->status,
            'status_name' => $category->status == 1 ? 'Active' : 'Inactive',
        ];

        return $data;
    }
}
