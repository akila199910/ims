<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordController extends Controller
{
    //
    public function password_view($id)
    {
        $user = User::where('ref_no',$id)->first();

        if (!$user) {
            return abort(404);
        }

        return view('auth.passwords.new_password',[
            'user' => $user
        ]);
    }

    public function password_update(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => ['required', 'string', 'min:8', 'confirmed','regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'],
                'password_confirmation' => ['required']
            ],
            [
                'password.regex' => 'Password must contain at least one number, both uppercase and lowercase letters and special character.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'errors' => $validator->errors()]);
        }

        $user = User::where('email',$request->email)->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->update();
        }

        $route = route('login');
        return response()->json(['status' => true,  'message' => 'New Password set successfully!', 'route' => $route]);
    }
}
