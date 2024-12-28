<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    if (Auth::check()) {
        if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin')){
            return redirect()->route('admin.business');
        }elseif(auth()->user()->hasRole('business_user') || auth()->user()->hasRole('user')){
            return redirect()->route('business.dashboard');
        }
    }

    return view('auth.login');
});


Route::get('/mail_view', function () {

    return view('mail.welcome.user');
});

Route::get('/404_error', function () {

    return abort(404);
});

Route::get('/error_view', function () {

    return view('errors.error_layout');
});

Auth::routes(['register', false]);

//Forgot Password
Route::get('/forget_password', [App\Http\Controllers\Business\ForgotPasswordController::class, 'index'])->name('buiness.forget_password.index');
Route::post('/forget_password', [App\Http\Controllers\Business\ForgotPasswordController::class, 'emailcheck'])->name('buiness.forget_password.email.check');
Route::get('/forget_password/verify/{id}', [App\Http\Controllers\Business\ForgotPasswordController::class, 'forget_password_verify'])->name('buiness.forget_password.verify');
Route::get('/new_password/{id}', [App\Http\Controllers\Business\ForgotPasswordController::class, 'new_password'])->name('buiness.new_password.view');
Route::post('/new_password', [App\Http\Controllers\Business\ForgotPasswordController::class, 'password_create'])->name('buiness.password_create');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\Business\DashboardController::class, 'index'])->name('dashboard');


//set password
Route::get('/set_password/{id}', [App\Http\Controllers\PasswordController::class, 'password_view'])->name('set_password.view');
Route::post('/set_password', [App\Http\Controllers\PasswordController::class, 'password_update'])->name('set_password.update');

Route::get('/refresh_admin_perm', [App\Http\Controllers\CustomController::class, 'refresh_admin_permissions'])->name('custom.refresh.admin_permission');
Route::get('/get_pdf_view/{id}', [App\Http\Controllers\CustomController::class, 'get_pdf_view'])->name('custom.get.pdf_view');

  //profile
  Route::get('/Profile_setting',[App\Http\Controllers\Admin\ProfileController::class,'profile'])->name('admin.profile.index');
  Route::post('/Profile_setting/profile_update', [App\Http\Controllers\Admin\ProfileController::class, 'profileUpdate'])->name('admin.profile.profile_update');
  Route::post('/Profile_setting/password_update', [App\Http\Controllers\Admin\ProfileController::class, 'passwordUpdate'])->name('admin.profile.password_update');

  // It maybe move to business
  Route::get('/cost_calculator',[App\Http\Controllers\CostCalculatoryController::class,'index'])->name('cost_calculator')->middleware('auth');
  Route::post('/cost_calculation',[App\Http\Controllers\CostCalculatoryController::class,'cost_calculation'])->name('cost_calculator.calculation');
  Route::post('/export_calculation',[App\Http\Controllers\CostCalculatoryController::class,'export_calculation'])->name('cost_calculator.expport.calculation');

  Route::get('/cost_calculator_download',[App\Http\Controllers\CostCalculatoryController::class,'cost_calculator_download'])->name('cost_calculator_download');
