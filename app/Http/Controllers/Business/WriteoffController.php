<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductWarehouse;
use App\Models\PurchaseOrderItem;
use App\Models\Warehouses;
use App\Models\Writeoff;
use App\Repositories\WriteoffRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class WriteoffController extends Controller
{
    //
    private $business_id;
    private $writeoff_repo;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->writeoff_repo = new WriteoffRepository();

    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_WriteOff');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        if ($request->json) {
            $request->merge([
                'business_id' => $this->business_id,
            ]);

            $writeoff = $this->writeoff_repo->writeoff_list($request);

            $data =  Datatables::of($writeoff)
                ->addIndexColumn()
                ->addColumn('product', function ($item) {
                    $product = 'N/A';
                    if (isset($item->Product_info) && !empty($item->Product_info))
                        $product = Str::limit($item->Product_info->name,30);

                    return $product;
                })
                ->addColumn('retail_price', function ($item) {
                    $retail_price = 'N/A';
                    if (isset($item->Product_info) && !empty($item->Product_info))
                        $retail_price = $item->Product_info->retail_price;

                    return $retail_price;
                })
                ->addColumn('warehouse', function ($item) {
                    $warehouse = 'N/A';
                    if (isset($item->WareHouse_info) && !empty($item->WareHouse_info))
                        $warehouse = Str::limit($item->WareHouse_info->name,30);

                    return $warehouse;
                })
                ->addColumn('qty', function ($item) {
                    return ($item->qty) ? $item->qty : 'N/A';
                })
                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = route('business.writeoff.update.form', $item->ref_no);
                    $view_url = route('business.writeoff.view_details', $item->ref_no);

                    $actions = '';
                    $actions = action_btns($actions, $user, 'WriteOff', $edit_route, $item->id, '',$view_url);

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                        '</div></div>';

                    return $action;
                })

                ->rawColumns(['action', 'warehouse','product','qty','retail_price'])
                ->make(true);

            return $data;
        }

        return view('business.writeoff.index');
    }

    public function create_form(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_WriteOff');

        if ($check_premission == false) {
            return abort(404);
        }

        $product_warehouse = ProductWarehouse::find($request->id);


        $products = Products::Where('business_id',$this->business_id)->where('status',1)->get();

        $warehouse = Warehouses::Where('business_id',$this->business_id)->where('status',1)->get();

        return view('business.writeoff.create',[
            'products'=> $products,
            'warehouse' => $warehouse,
            'product_warehouse' => $product_warehouse
        ]);
    }

    public function create(Request $request)
    {
        $av_qty = 0;
        if(isset($request->av_qty) && !empty($request->av_qty))
        $av_qty = $request->av_qty;

        $validator = Validator::make(
            $request->all(),
            [
                'product' => 'required',
                'warehouse' => 'required',
                'qty' => 'required|numeric|min:1|max:'.$av_qty,
                'reason' => 'required|max:190'

            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $business_id = $this->business_id;

        $request->merge([
            'business_id' => $business_id,
        ]);


        $data = $this->writeoff_repo->create($request);

        $data['status'] = true;
        $data['message'] = 'New Write Off Created Successfully!';
        $data['route'] = route('business.writeoff');

        return response()->json($data);
    }

    public function update_form($id)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_WriteOff');

        if ($check_premission == false) {
            return abort(404);
        }

        $writeoff = Writeoff::where(['ref_no' => $id])->first();

        if (!$writeoff) {
            return abort(404);
        }

        $product_ids = ProductWarehouse::where('warehouse_id',$writeoff->warehouse_id)->where('qty','>', 0)->pluck('product_id')->toArray();
        $product_ids = array_merge($product_ids, [$writeoff->product_id]);

        $products = Products::whereIn('id',$product_ids)->where('status',1)->get();

        $warehouse = Warehouses::where('status',1)->Where('business_id',$this->business_id)->get();

        //Write off product info
        $product_warehouse = ProductWarehouse::where('warehouse_id',$writeoff->warehouse_id)->where('product_id',$writeoff->product_id)->first();
        $writeoff_qty = $writeoff->qty;
        $av_qty = 0;
        if ($product_warehouse) {
            $av_qty = $product_warehouse->qty;
        }

        $av_qty = $av_qty + $writeoff_qty;

        return view('business.writeoff.update',[
            'warehouse' => $warehouse,
            'products' => $products,
            'writeoff' => $writeoff,
            'av_qty' => $av_qty
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $av_qty = 0;

        if(isset($request->av_qty) && !empty($request->av_qty))
        $av_qty = $request->av_qty;

        $validator = Validator::make(
            $request->all(),
            [
                'product' => 'required',
                'warehouse' => 'required',
                'qty' => 'required|numeric|min:1|max:'.$av_qty,
                'reason' => 'required|max:190'


            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);

        $data = $this->writeoff_repo->update($request);

        $data['route'] = route('business.writeoff');

        return response()->json($data);
    }


    public function delete(Request $request)
    {
        $data = $this->writeoff_repo->delete($request);

        $data['route'] = route('business.writeoff');

        return response()->json($data);
    }


    public function item_filter(Request $request)
    {
        $product_ids = ProductWarehouse::where('warehouse_id',$request->warehouse_id)->where('qty','>', 0)->pluck('product_id')->toArray();

        $products = Products::whereIn('id',$product_ids)->get();


        return view('business.writeoff.item_data',[
            'products' => $products,

        ]);
    }

    public function get_details(Request $request)
    {

        $productId = $request->id;

        $products = ProductWarehouse::where('product_id', $productId)->where('warehouse_id',$request->warehouse_id)->first();

        return response()->json(['product_warehouses' => $products]);
    }

    public function view_details(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_WriteOff');

        if ($check_premission == false) {
            return abort(404);
        }
        // End

        $writeoff = Writeoff::Where(['ref_no' => $ref_no, 'business_id' => $this->business_id])->first();


        if (!$writeoff) {
            return abort(404);
        }

        return view('business.writeoff.view_details', [
            'writeoff' =>  $writeoff
        ]);

    }


}
