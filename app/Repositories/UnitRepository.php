<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Units;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class UnitRepository
{

    public function unit_list($request)
    {
        $units = Units::where('business_id', $request->business_id);

        return $units;
    }


    public function create($request)
    {

        $unit =new Units();
        $unit->name = $request->name;
        $unit->status = $request->status == true ? 1 : 0;
        $unit->business_id = $request->business_id;
        $unit->save();

        $ref_no = refno_generate(16, 2, $unit->id);
        $unit->ref_no = $ref_no;
        $unit->update();
        return [
            'id' => $unit->id,
            'name' => $unit->name,
        ];

   }

   public function update($request)
   {

        $unit=Units::find($request->id);

        $unit->name = $request->name;
        $unit->business_id= $request->business_id;
        $unit->status = $request->status==true ? 1 : 0;
        $unit->update();

        return [
            'status' => true,
            'message' => 'Selected Unit Updated Successfully!'
        ];

   }

   public function delete($request){

        $unit = Units::find($request->id);

        if (!$unit) {
            return [
                'status' => false,
                'message' => 'Location Not Found'
            ];
        }

        $unit->delete();

        return [
            'status' => true,
            'message' => 'Selected Unit Deleted Successfully!'
        ];
    }


   public function get_details($request)
    {
        $unit = Units::find($request->id);

        $data = [
            'name' => $unit->name,
            'status' => $unit->status,
            'status_name' => $unit->status == 1 ? 'Active' : 'Inactive',
        ];

        return $data;
    }
}
