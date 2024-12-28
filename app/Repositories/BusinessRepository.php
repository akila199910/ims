<?php

namespace App\Repositories;

use App\Models\Business;
use Illuminate\Support\Facades\Hash;

class BusinessRepository
{
    public function create_business($request)
    {
        //Create business
        $business = new Business();
        $business->name = $request->name;
        $business->email = $request->email;
        $business->contact = $request->contact;
        $business->address = $request->address;
        $business->status = $request->status == true ? 1 : 0;
        $business->save();

        //Generate Reference Number
        $ref_no = refno_generate(16, 2, $business->id);
        $business->ref_no = $ref_no;
        $business->update();

        return[
            'id' => $business->id,
            'name' => $business->name,
            'email' => $business->email,
            'contact' => $business->contact,
            'address' => $business->address,
            'status' => $business->status
        ];
    }

    public function update_business($request)
    {
        //update business
        $business = Business::find($request->id);
        $business->name = $request->name;
        $business->email = $request->email;
        $business->contact = $request->contact;
        $business->address = $request->address;
        $business->status = $request->status == true ? 1 : 0;
        $business->update();

        return[
            'id' => $business->id,
            'name' => $business->name,
            'email' => $business->email,
            'contact' => $business->contact,
            'address' => $business->address,
            'status' => $business->status
        ];
    }

    public function delete_business($request)
    {
        $business = Business::find($request->id);

        if (!$business) {
            return [
                'status' => false,
                'message' => 'Business Not Found'
            ];
        }

        $business->delete();

        return [
            'status' => true,
            'message' => 'Selected Business Deleted Successfully!'
        ];
    }
}
