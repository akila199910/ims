<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\BusinessUsers;
use App\Models\Products;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);

        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        view()->composer(
            'layouts.business',
            function ($view) {

                $segment = Request::segment(1);
                $segment2 = Request::segment(2);
                $businesses = Business::all();
                $lowStocks = ProductWarehouse::all();


                $user = Auth::user();
                if ($user->hasRole('business_user') ) {
                    $user_business_id =  BusinessUsers::where('user_id', $user->id)->pluck('business_id')->toArray();

                    $businesses = Business::whereIn('id',$user_business_id)->get();
                }


                $business_id = session()->get('_business_id');
                $product_ids = Products::where('business_id', $business_id)->where('status', 1)->pluck('id')->toArray();
                  // Fetch low stock items and count them
                $lowStocks = ProductWarehouse::with(['product_info', 'warehouse_info'])->whereIn('product_id', $product_ids)->whereColumn('qty', '<=', 'qty_alert')->take(5)->get();

                $view->with([
                    'businesses' => $businesses,
                    'lowStocks' => $lowStocks,
                ]);
            }
        );


    }
}
