<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserBusiness;
use Illuminate\Support\Facades\Hash;



class UserManageRepository

{
    public function create_users($request,$role)
    {
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = $request->first_name.' '. $request->last_name;
        $user->email = $request->email;
        $user->contact = $request->contact;
        $user->status = $request->status == true ? 1 : 0;
        $user->password = Hash::make($request->password);
        $user->save();

        //Generate Reference Number
        $ref_no = refno_generate(16, 2, $user->id);
        $user->ref_no = $ref_no;
        $user->update();

        //Storig User Profile
        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $profile->profile = 'user/user.png';
        $profile->save();

        //Storing User Business
        $user_business = new UserBusiness();
        $user_business->business_id = $request->business_id;
        $user_business->user_id = $user->id;
        $user_business->save();

        //Assigning Roles
        $user->assignRole($role);

        //Check permission available or not
        if (isset($request->permissions) && !empty($request->permissions)) {
            $user->givePermissionTo($request->permissions);
        }

        $data["title"] = 'User Creation | '.env('APP_NAME');
        $data["email"] = $user->email;
        $data["name"] = $user->name;
        $data["user"] = $user;
        $data["view"] = 'mail.welcome.user';

        mailNotification($data);

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->name,
            'contact' => $user->contact,
            'email' => $user->email,
            'profile' => config('constants.aws_url') . $user->UserProfile->profile
        ];
    }

    public function update_users($request)
    {
        $user = User::find($request->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = $request->first_name.' '. $request->last_name;
        $user->email = $request->email;
        $user->contact = $request->contact;
        $user->status = $request->status == true ? 1 : 0;
        $user->update();


        //Check permission available or not
        if (isset($request->permissions) && !empty($request->permissions)) {
            $user->syncPermissions($request->permissions);
        }

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->name,
            'contact' => $user->contact,
            'email' => $user->email,
            'profile' => config('constants.aws_url') . $user->UserProfile->profile
        ];
    }

    public function delete_user($request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'User Not Found'
            ];
        }

        $user->delete();

        return [
            'status' => true,
            'message' => 'Selected User Deleted Successfully!'
        ];
    }

}
