<?php

namespace App\Http\Controllers\Business;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserPasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    //
    public function index()
    {
        return view('auth.passwordreset.index');
    }

    public function emailcheck(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => false,  'errors' => $validator->errors()]);
        }

        $user = User::where('email',$request->email)->first();
        if ($user) {
            if ($user->status == 0) {
                return response()->json(['status' => false,  'errors' => ['email' => 'Sorry! You account was deactivated. Please contact the support team']]);
            } else {

                $links = new UserPasswordReset();
                $links->user_id = $user->id;
                $links->save();

                $data["title"] = "Forgot Password Verification Link | ".env('APP_NAME');
                $data["email"] = $user->email;
                $data["name"] = $user->name;
                // $data["comapny_logo"] = config('aws_url.url').$company->image;
                // $data['company'] = $company;
                $data["view"] = "mail.forgot";

                mailNotification($data);

                return response()->json(['status' => true,  'message' => 'Password Reset link sent your mail address. Please check and click the Reset Password Button.']);
            }
        } else {
            return response()->json(['status' => false,  'errors' => ['email'=>'This Mail is not found in our server']]);
        }
    }

    public function forget_password_verify($id)
    {
        $email = Crypt::decrypt($id);

        $user = User::where('email', $email)->first();

        if ($user) {
            $links = UserPasswordReset::where('user_id', $user->id)->first();
            if ($links) {
                return redirect('/new_password/' . Crypt::encrypt($user->id));
            } else {
                return redirect()->route('login')->with('error', 'Reset Link Expired');
            }
        } else {
            return redirect()->route('login')->with('error', 'User Not Found');
        }
    }

    public function new_password($id)
    {
        $id = Crypt::decrypt($id);

        $user = User::find($id);

        return view('auth.passwordreset.new_password', ['user' => $user]);
    }

    public function password_create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'],
                'password_confirmation' => 'required'
            ],
            [
                'password.regex' => 'Password must contain at least one number, both uppercase and lowercase letters and special character.',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => false,  'errors' => $validator->errors()]);
        }

        $user = User::where('email', $request->email)->first();
        UserPasswordReset::where('user_id', $user->id)->delete();

        $user->password = Hash::make($request->password);
        $user->update();

        //Logged the user
        Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => '1']);

        return response()->json(['status' => true,  'message' => 'Password Reset Successfully!']);
    }
}
