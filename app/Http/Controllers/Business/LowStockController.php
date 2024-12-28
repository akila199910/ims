<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Exports\LowStockExport;
use App\Models\ProductWarehouse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\ExportRepository;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class LowStockController extends Controller
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

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_LowStock');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        if ($request->json) {

            $product_ids = Products::where('business_id', $this->business_id)->where('status', 1)->pluck('id')->toArray();

            $lowStocks = ProductWarehouse::with(['product_info', 'warehouse_info'])->whereIn('product_id', $product_ids)->whereColumn('qty', '<=', 'qty_alert');

            $data = DataTables::of($lowStocks)
                ->addIndexColumn()

                ->addColumn('product', function ($item) {
                    return $item->product_info ?  Str::limit($item->product_info->name ,30) : 'N/A';
                })
                ->addColumn('warehouse', function ($item) {
                    return $item->warehouse_info ?  Str::limit($item->warehouse_info->name ,30) : 'N/A';
                })
                ->addColumn('image', function ($item) {

                    // $url = $item->product_info ? config('awsurl.url').($item->product_info->image) : '';
                    if (!isset($item->product_info) ||  $item->product_info->image == '' || $item->product_info->image == 0) {
                        return '<img src="'.asset('layout_style/img/icons/product_100.png').'"  border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                    }

                    $url = config('awsurl.url').($item->product_info->image);
                    return '<img src="' . $url . '"  border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                })
                ->rawColumns(['image', 'product', 'warehouse', 'qty'])

                ->make(true);

            return $data;
        }

        return view('business.lowstock.index');
    }

    public function lowStock_export(Request $request)
    {
       $request->merge([
        'business_id' => $this->business_id
       ]);

        $data = (new ExportRepository)->lowStockExport($request);

        $file_name = 'lowStock' . date('_YmdHis') . '.xlsx';

        $business = Business::find($this->business_id);

        return Excel::download(new LowStockExport($data, count($data), $business), $file_name);
    }
}
