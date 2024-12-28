<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\UserBusiness;
use Illuminate\Http\Request;
use App\Models\BusinessUsers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
     /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => false,  'errors' => $validator->errors()]);
        }

        $user = User::where('email', $input['email'])->first();

        if (!$user) {
            // return redirect()->route('login')
            //     ->with('error_login','These credentials do not match our records.');

            return response()->json(['status' => false, 'errors' => [
                'email' => 'The email address do not match our records'
            ]]);
        }

        if ($user->status == 0) {
            // return redirect()->route('login')->with('error_login','Your account has been deactivated. Please contact the support team');

            return response()->json(['status' => false, 'errors' => [
                'email' => 'Sorry! Your account was deactivated. Please contact the support team.'
            ]]);
        }

        if (auth()->attempt(array('email' => $input['email'], 'password' => $input['password'], 'status' => 1))) {

            if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin'))
            {
                $route = route('admin.business');
                session()->put('_user_role', 'admin');

                return response()->json(['status' => true, 'message' => 'Success', 'route' => $route]);

             }
             elseif(auth()->user()->hasRole('user') || auth()->user()->hasRole('business_user')){

                $user = Auth()->user();
                if($user->hasRole('business_user')){
                    $business_id = BusinessUsers::Where('user_id',$user->id)->first();
                }
                if($user->hasRole('user')){
                    $business_id = UserBusiness::Where('user_id',$user->id)->first();
                }
                
                $route = route('business.dashboard');
                session()->put('_business_id', $business_id->business_id);
                return response()->json(['status' => true, 'message' => 'Success', 'route' => $route]);

            }
            // elseif(auth()->user()->hasRole('user') || auth()->user()->hasRole('business_user')){

            //     $user = Auth()->user();
            //     $business_id = BusinessUsers::Where('user_id',$user->id)->first()||UserBusiness::Where('user_id',$user->id)->first();
            //     $route = route('business.dashboard');
            //     session()->put('_business_id', $business_id);
            //     return response()->json(['status' => true, 'message' => 'Success', 'route' => $route]);

            // }
            else {
                Auth::logout();
                $route = route('login');

                return response()->json(['status' => true, 'message' => 'Success', 'route' => $route]);
            }
        } else {

            return response()->json(['status' => false, 'errors' => [
                'password' => 'Invalid Password.'
            ]]);
        }
    }
}
