<?php

namespace App\Http\Controllers\Business;

use App\Models\User;
use App\Models\Business;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;




class ProfileController extends Controller
{
    //
    private $business_id;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });
    }

    public function profile(Request $request)
    {
        $business = Business::find($this->business_id);
        $user = Auth::user();

        return view('business.profile.index', [
            'business' => $business,
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
            'route' => route('business.profile')
        ]);
    }


    public function passwordUpdate(Request $request)
{
    $validator = Validator::make(
        $request->all(),
        [
            'old_password' => ['required', 'string'],
            'password' => ['required','string','min:8','confirmed','regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/'],
            'password_confirmation' => ['required', 'same:password'],
        ],
        [
            'old_password.required' => 'The old password is required.',
            'password.regex' => 'Password must contain at least one number, both uppercase and lowercase letters, and a special character.',
            'password_confirmation.same' => 'The password confirmation does not match.',
        ]
    );

    $errors = $validator->errors();

    $user = Auth::user();
    if (!Hash::check($request->input('old_password'), $user->password)) {
        $errors->add('old_password', 'The old password does not match.');
    }

    if ($request->filled('password') && Hash::check($request->input('password'), $user->password)) {
        $errors->add('password', 'You cannot use the same old password again.');
    }

    if ($errors->isNotEmpty()) {
        return response()->json([
            'status' => 'val_error',
            'errors' => $errors,
        ]);
    }

    $user->password = Hash::make($request->input('password'));
    $user->update();

    return response()->json([
        'status' => true,
        'message' => 'Your password was updated successfully!',
        'route' => route('business.profile'),
    ]);
}

}
