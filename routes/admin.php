<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| This router contains super admin and admin routers.
|
*/

Route::prefix('admin')->middleware(['auth'])->group(function () {

    // //Business
    Route::get('/business', [App\Http\Controllers\Admin\BusinessController::class, 'index'])->name('admin.business');
    Route::get('/business/create', [App\Http\Controllers\Admin\BusinessController::class, 'create_form'])->name('admin.business.create.form');
    Route::post('/business/create', [App\Http\Controllers\Admin\BusinessController::class, 'create'])->name('admin.business.create');
    Route::get('/business/update/{id}', [App\Http\Controllers\Admin\BusinessController::class, 'update_form'])->name('admin.business.update.form');
    Route::post('/business/update', [App\Http\Controllers\Admin\BusinessController::class, 'update'])->name('admin.business.update');
    Route::post('/business/move_to_dashboard', [App\Http\Controllers\Admin\BusinessController::class, 'move_dashboard'])->name('admin.business.move_dashboard');
    Route::get('/business/view/{ref_no}', [App\Http\Controllers\Admin\BusinessController::class, 'view_details'])->name('admin.business.view_details');
    Route::post('/business/delete', [App\Http\Controllers\Admin\BusinessController::class, 'delete'])->name('admin.business.delete');


    //Admin users create
    Route::middleware(['super_admin'])->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminUsersController::class, 'index'])->name('admin.admin-users');
        Route::get('/create', [App\Http\Controllers\Admin\AdminUsersController::class, 'create_form'])->name('admin.admin-users.create.form');
        Route::post('/create', [App\Http\Controllers\Admin\AdminUsersController::class, 'create'])->name('admin.admin-users.create');
        Route::get('/update/{id}', [App\Http\Controllers\Admin\AdminUsersController::class, 'update_form'])->name('admin.admin-users.update.form');
        Route::post('/update', [App\Http\Controllers\Admin\AdminUsersController::class, 'update'])->name('admin.admin-users.update');
        Route::post('/delete', [App\Http\Controllers\Admin\AdminUsersController::class, 'delete'])->name('admin.admin-users.delete');
        Route::post('/business/delete', [App\Http\Controllers\Admin\BusinessController::class, 'delete'])->name('admin.business.delete');
        Route::get('/view/{ref_no}', [App\Http\Controllers\Admin\AdminUsersController::class, 'view_details'])->name('admin.admin-user.view_details');

    });
});




Route::prefix('business_user')->middleware(['auth'])->group(function () {

    //Business users create
    Route::get('/', [App\Http\Controllers\Admin\BusinessUserController::class, 'index'])->name('admin.business-users');
    Route::get('/create', [App\Http\Controllers\Admin\BusinessUserController::class, 'create_form'])->name('admin.business-users.create.form');
    Route::post('/create', [App\Http\Controllers\Admin\BusinessUserController::class, 'create'])->name('admin.business-users.create');
    Route::get('/update/{id}', [App\Http\Controllers\Admin\BusinessUserController::class, 'update_form'])->name('admin.business-users.update.form');
    Route::post('/update', [App\Http\Controllers\Admin\BusinessUserController::class, 'update'])->name('admin.business-users.update');
    Route::post('/delete', [App\Http\Controllers\Admin\BusinessUserController::class, 'delete'])->name('admin.business-users.delete');
    Route::get('/view/{ref_no}', [App\Http\Controllers\Admin\BusinessUserController::class, 'view_details'])->name('admin.business-users.view_details');

});



