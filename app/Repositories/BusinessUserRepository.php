<?php

namespace App\Repositories;

use App\Models\BusinessUsers;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class BusinessUserRepository{

    public function create($request)
    {

        //Create Business user
        $new_business_user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name.' '.$request->last_name,
            'email' => $request->email,
            'contact' => $request->contact,
            'status' => $request->status == true ? 1 : 0,
            'password' => Hash::make($request->password),
        ]);

        $profile = new UserProfile();
        $profile->user_id = $new_business_user->id;
        $profile->profile = 'user/user.png';
        $profile->save();
        $new_business_user->assignRole('business_user');
        $permissions = Permission::pluck('name')->toArray();
        $new_business_user->givePermissionTo($permissions);

        $user_ref = refno_generate(16, 2, $new_business_user->id);
        $new_business_user->update([
            'ref_no' => $user_ref
        ]);
        $selectedCompanies = $request->input('companies');
        foreach ($selectedCompanies as $company){
            BusinessUsers::Create([
                'user_id'=>$new_business_user->id,
                'business_id' => $company,
            ]);

        }
        $data["title"] = 'Business User Creation | '.env('APP_NAME');
        $data["email"] = $new_business_user->email;
        $data["name"] = $new_business_user->name;
        $data["user"] = $new_business_user;
        $data["view"] = 'mail.welcome.user';

        mailNotification($data);

        $data = [
            'id' => $new_business_user->id,
            'ref_no' => $new_business_user->ref_no,
            'first_name' => $new_business_user->first_name,
            'last_name' => $new_business_user->last_name,
            'name' => $new_business_user->name,
            'email' => $new_business_user->email,
            'contact' => $new_business_user->contact,
            'status'=>$new_business_user->status,
            'password'=>$new_business_user->password,
        ];

        return [
            'status' => true,
            'message' => 'New Business User Created Successfully!',
            'data' => $data
        ];
    }

    public function update($request)
    {
        $edit_business_user = User::find($request->id);

        if (!$edit_business_user) {
            return [
                'status' => false,
                'message' => 'Business User Not Found'
            ];
        }
        $edit_business_user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name.' '.$request->last_name,
            'email' => $request->email,
            'contact' => $request->contact,
            'status' => $request->status == true ? 1 : 0,
            // 'password' => Hash::make($password),
        ]);
        BusinessUsers::where('user_id', $edit_business_user->id)->delete();
        foreach ($request->input('companies') as $company) {
            BusinessUsers::create([
                'user_id' => $edit_business_user->id,
                'business_id' => $company,
            ]);
        }

        return [
            'status' => true,
            'message' => 'Selected Business User Updated Successfully!'
        ];
    }

    public function delete($request)
    {
        $business_user = User::find($request->id);

        if (!$business_user) {
            return [
                'status' => false,
                'message' => 'Business User Not Found'
            ];
        }

        $business_user->delete();

        return [
            'status' => true,
            'message' => 'Selected Business User Deleted Successfully!'
        ];
    }
}
