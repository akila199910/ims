<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class AdminUserRepository{

    public function create($request)
    {

        //Create admin
        $new_adminuser = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name.' '.$request->last_name,
            'email' => $request->email,
            'contact' => $request->contact,
            'status' => $request->status == true ? 1 : 0,
            'password' => Hash::make($request->password),
        ]);

        $ref_no = refno_generate(16, 2, $new_adminuser->id);

        $new_adminuser->update([
            'ref_no' => $ref_no,
        ]);
            $profile = new UserProfile();
            $profile->user_id = $new_adminuser->id;
            $profile->profile = 'user/user.png';
            $profile->save();

        $permissions = Permission::pluck('name')->toArray();
        $new_adminuser->assignRole('admin');
        $new_adminuser->givePermissionTo($permissions);

        $data["title"] = 'Admin Creation | '.env('APP_NAME');
        $data["email"] = $new_adminuser->email;
        $data["name"] = $new_adminuser->name;
        $data["user"] = $new_adminuser;
        $data["view"] = 'mail.welcome.user';

        mailNotification($data);

        $data = [
            'id' => $new_adminuser->id,
            'ref_no' => $new_adminuser->ref_no,
            'first_name' => $new_adminuser->first_name,
            'last_name' => $new_adminuser->last_name,
            'name' => $new_adminuser->name,
            'email' => $new_adminuser->email,
            'contact' => $new_adminuser->contact,
            'status'=>$new_adminuser->status,
            'password'=>$new_adminuser->password,
        ];

        return [
            'status' => true,
            'message' => 'New Admin Created Successfully!',
            'data' => $data
        ];
    }

    public function update($request)
    {
        $edit_admin = User::find($request->id);

        if (!$edit_admin) {
            return [
                'status' => false,
                'message' => 'Client Not Found'
            ];
        }

        $edit_admin->update([
            'ref_no' => $request->input('ref_no', $edit_admin->ref_no), // Use current ref_no if not provided
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name.' '.$request->last_name,
            'email' => $request->email,
            'contact' => $request->contact,
            'status' => $request->status == true ? 1 : 0
        ]);

        return [
            'status' => true,
            'message' => 'Selected Admin Updated Successfully!'
        ];
    }

    public function delete($request)
    {
        $admin = User::find($request->id);

        if (!$admin) {
            return [
                'status' => false,
                'message' => 'Admin Not Found'
            ];
        }

        $admin->delete();

        return [
            'status' => true,
            'message' => 'Selected Admin Deleted Successfully!'
        ];
    }
}
