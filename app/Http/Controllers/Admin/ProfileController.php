<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Business;
use App\Models\UserProfile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BusinessUsers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Repositories\BusinessUserRepository;

class ProfileController extends Controller
{
    public function profile(){
        $user = Auth::user();

        return view('admin.profile.index', [
            'user' => $user
        ]);
    }
    public function profileUpdate(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|regex:/^[a-z A-Z 0-9]+$/u|max:191',
                'last_name' => 'required|regex:/^[a-z A-Z 0-9]+$/u|max:191',
                'email' => 'required|max:191|unique:users,email,' . Auth::user()->id. ',id,deleted_at,NULL',
                'contact' => 'required|digits:10|unique:users,contact,' . Auth::user()->id. ',id,deleted_at,NULL',
                'image'=>'nullable|mimes:jpeg,png,jpg,svg',

            ]
        );

        if ($validator->fails()) {
            return response()->json(['status'=>'val_error',  'errors'=>$validator->errors()]);
        }

        $user = User::find(Auth::user()->id);

        if (isset($request->image) && $request->image->getClientOriginalName()) {
            $file = $request->file('image')->store(
                'user', 's3'
            );
        } else {
            if (!$user->UserProfile->profile)
                $file = '';
            else
                $file = $user->UserProfile->profile;
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = $request->first_name . ' ' . $request->last_name;
        $user->email = $request->email;
        $user->contact = $request->contact;
        $user->update();

        $userProfile = UserProfile::where('user_id', Auth::user()->id)->first();
        $userProfile->profile = $file;
        $userProfile->update();

        return response()->json([
            'status'=>true,
            'message'=>'Profile Updated successfully!',
            'route' => route('admin.profile.index')
        ]);
    }
    public function passwordUpdate(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'password_confirmation' => ['required'],
            'old_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed','regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'],
        ],
        [
            'password.regex' => 'Password must contain at least one number, both uppercase and lowercase letters and special character.',
        ]
        );

        if ($validator->fails()) {
            return response()->json(['status'=>'val_error',  'errors'=>$validator->errors()]);
        }

        $user_id = Auth::user()->id;
        $user = User::findOrFail($user_id);
        if (!Hash::check($request->input('old_password'), $user->password)) {

            return response()->json(['status'=>'old_error', 'message'=>'Old Password not match']);
        } else {
            if (Hash::check($request->input('password'), $user->password)) {

                return response()->json(['status'=>'error_password', 'message'=>'You can not use the same old password again']);
            } else {
                $user->password = Hash::make($request->input('password'));
                $user->update();

                return response()->json([
                    'status'=>true,
                    'message'=>'Your password updated successfully!',
                    'route' => route('admin.profile.index')
                ]);
            }
        }
    }

}
