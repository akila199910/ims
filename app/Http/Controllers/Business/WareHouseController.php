<?php

namespace App\Http\Controllers\Business;

use App\Models\Products;
use App\Models\Warehouses;
use Illuminate\Http\Request;
use App\Models\ProductWarehouse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\WareHouseRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class WareHouseController extends Controller
{
    //
    private $warehouse_repo;
    private $business_id;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->warehouse_repo = new WareHouseRepository();
    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Warehouse');

        if ($check_premission == false) {
            return abort(404);
        }

        if ($request->json) {

            $request->merge([
                'business_id' => $this->business_id
            ]);

            // Getting warehouses list
            $warehouses = $this->warehouse_repo->warehouse_list($request);

            $data =  Datatables::of($warehouses)
                ->addIndexColumn()

                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="badge badge-soft-danger badge-border">Inactive</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-success badge-borders">Active</span>';
                    }
                })

                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = route('business.warehouse.update.form', $item->ref_no);
                    $view_route = route('business.warehouse.view.form', $item->ref_no);

                    $actions = '';
                    $actions = action_btns($actions, $user, 'Warehouse', $edit_route, $item->id, $view_route,'');

                    $action = '<div class="dropdown dropdown-action">
                    <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-ellipsis-v"></i>
                    </a>
                <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                        '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);

            return $data;
        }
        return view('business.warehouse.index');
    }

    public function create_form()
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_Warehouse');

        if ($check_premission == false) {
            return abort(404);
        }

        return view('business.warehouse.create');
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-zA-Z0-9\s,\/_\-\'".\$]+$/u|max:190|unique:warehouses,name,NULL,id,deleted_at,NULL,business_id,' . $this->business_id,
                'address' => 'required|max:190',
                'contact' => 'required|digits:10|unique:warehouses,contact,NULL,id,deleted_at,NULL,business_id,' . $this->business_id,

            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $business_id = $this->business_id;

        $request->merge([
            'business_id' => $business_id,
        ]);
        $data = $this->warehouse_repo->create($request);

        $data['status'] = true;
        $data['message'] = 'New Warehouse Created Successfully!';
        $data['route'] = route('business.warehouse');

        return response()->json($data);
    }

    public function update_form($id)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_Warehouse');

        if ($check_premission == false) {
            return abort(404);
        }

        $ware_houses = Warehouses::where(['ref_no' => $id])->first();

        if (!$ware_houses) {
            return abort(404);
        }

        return view('business.warehouse.update', [
            'ware_houses' => $ware_houses
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-zA-Z0-9\s,\/_\-\'".\$]+$/u|max:190|unique:warehouses,name,' . $id . ',id,deleted_at,NULL,business_id,' . $this->business_id,
                'address' => 'required|max:190',
                'contact' => 'required|digits:10|unique:warehouses,contact,' . $id . ',id,deleted_at,NULL,business_id,' . $this->business_id,


            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);

        $data = $this->warehouse_repo->update($request);

        $data['route'] = route('business.warehouse');

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $data = $this->warehouse_repo->delete($request);

        $data['route'] = route('business.warehouse');

        return response()->json($data);
    }

    public function  view_form(Request $request, $ref_no)
    {
        // Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Warehouse');

        if ($check_premission == false) {
            return abort(404);
        }
        // End

        $ware_houses = Warehouses::where(['ref_no' => $ref_no, 'business_id' => $this->business_id])->first();

        if (!$ware_houses) {
            return abort(404);
        }

        return view('business.warehouse.view_details', [
            'ware_houses' => $ware_houses
        ]);
    }

    public function get_products(Request $request)
    {
        $products_id = Products::where('business_id',$this->business_id)->pluck('id')->toArray();
            $request->merge([
                'products_id' => $products_id
            ]);
        $products = $this->warehouse_repo->warehouse_product_list($request);

        $data =  Datatables::of($products)
            ->addIndexColumn()
            ->addColumn('image', function ($item) {
                $url = config('awsurl.url') . ($item->product_info->image);


                if ($item->product_info->image == '' || $item->product_info->image == 0) {
                    return '<img src="'.asset('layout_style/img/icons/product_100.png').'" border="0" height="50px" class="stylist-image" align="center" />';
                }
                return '<img src="' . $url . '" border="0" height="50" class="stylist-image" align="center" />';
            })

            ->editColumn('name', function ($item) {
                $name = 'N/A';

                if($item->product_info)
                    $name = Str::limit($item->product_info->name,30);

                return $name;
            })

            ->editColumn('category', function ($item) {
                $category = 'N/A';

                if($item->product_info && $item->product_info->category_info)
                    $category = Str::limit($item->product_info->category_info->name,30);

                return $category;
            })

            ->editColumn('sub_category', function ($item) {
                $sub_category = 'N/A';

                if($item->product_info && $item->product_info->sub_category_info)
                    $sub_category = Str::limit($item->product_info->sub_category_info->name,30);

                return $sub_category;
            })

            ->editColumn('unit', function ($item) {
                $unit = 'N/A';

                if($item->product_info && $item->product_info->unit_info)
                    $unit = Str::limit($item->product_info->unit_info->name,30);

                return $unit;
            })

            ->addColumn('action', function ($item) {
                $action = '';


                $edit_btn = '<button style="background-color: #2072AF; border: 1px solid #2072AF;" class="btn btn-sm btn-dark edit-warehouse" data-id="' . $item->id . '">
                     <i class="fa-solid fa-pen-to-square"></i>
                 </button> &nbsp;';

                if ($item->qty <= 0) {
                    $delete_btn = '<button class="btn btn-sm btn-danger" onclick="deleteConfirmation(' . $item->id . ')">
                                    <i class="fa fa-trash"></i>
                                </button>';
                    $action .= $delete_btn;
                } else {
                    $action .= $edit_btn;
                }
                return $action;
            })
            ->rawColumns(['action', 'status', 'category', 'sub_category','unit', 'image', 'name'])
            ->make(true);

        return $data;
    }

    public function products_delete(Request $request)
    {
        ProductWarehouse::destroy($request->id);

        return response()->json(['status'=>true, 'message' => 'Selected Product deleted successfully!']);
    }

    public function get_details(Request $request)
    {
        $data = $this->warehouse_repo->get_details($request);

        return response()->json(['status' => true,  'data' => $data]);
    }

    public function get_details_product(Request $request)
    {
        $data = $this->warehouse_repo->get_details_product($request);

        return response()->json(['status' => true,  'data' => $data]);
    }


    public function update_product(Request $request)
    {
        $product = ProductWarehouse::find($request->product_id);  // Find the warehouse by ID

        if ($product) {

            $validator = Validator::make(
                $request->all(),
                [
                    'qty' => 'required|min:1|numeric|max:'.$product->qty,
                ]
            );

            if ($validator->fails()) {
                return response()->json(['status' => false,  'message' => $validator->errors()]);
            }

            $qty = $product->qty - $request->qty;


            $product->qty = $qty > 0 ? $qty : 0;
            $product->update();

            return response()->json(['status' => true, 'message' => 'Selected Warehouse QTY value adjusted successfully!']);
        }
    }


}
