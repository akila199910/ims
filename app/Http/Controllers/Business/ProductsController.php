<?php

namespace App\Http\Controllers\Business;

use App\Models\Units;
use App\Models\Category;
use App\Models\Warehouses;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductWarehouse;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ProductRepository;
use App\Repositories\WareHouseRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ProductsController extends Controller
{
    private $business_id;
    private $product_repo;


    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->product_repo = new ProductRepository();

    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Product');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        if ($request->json) {
            $request->merge([
                'business_id' => $this->business_id
            ]);

            $products = $this->product_repo->product_list($request);

            $data =  Datatables::of($products)
                ->addIndexColumn()
                ->addColumn('image', function ($item) {
                    $url = config('awsurl.url') . ($item->image);

                    if ($item->image == '' || $item->image == 0) {
                        return '<img src="layout_style/img/icons/product_100.png"  border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                    }
                    return '<img src="' . $url . '"  border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                })
                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="badge badge-soft-danger badge-border">Inactive</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-success badge-borders">Active</span>';
                    }
                })
                ->addColumn('category', function ($item) {
                    $category = 'N/A';
                    if (isset($item->category_info) && !empty($item->category_info)) {
                        $category = Str::limit($item->category_info->name, 30);
                    }

                    return $category;
                })
                ->addColumn('sub_category', function ($item) {
                    $sub_category = 'N/A';
                    if (isset($item->sub_category_info) && !empty($item->sub_category_info))
                        $sub_category = Str::limit($item->sub_category_info->name,30);

                    return $sub_category;
                })
                ->addColumn('unit', function ($item) {
                    $unit = 'N/A';
                    if (isset($item->unit_info) && !empty($item->unit_info))
                        $unit = Str::limit($item->unit_info->name,30);

                    return $unit;
                })
                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = route('business.products.update.form', $item->ref_no);
                    $view_route = route('business.products.view.form', $item->ref_no);

                    $actions = '';
                    $actions = action_btns($actions, $user, 'Product', $edit_route, $item->id, $view_route,'');

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                        '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'status', 'category', 'sub_category', 'unit', 'image'])
                ->make(true);

            return $data;
        }

        return view('business.products.index');
    }

    public function  view_form(Request $request, $ref_no)
    {
        // Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Product');

        if ($check_premission == false) {
            return abort(404);
        }
        // End

        $products = Products::where(['ref_no' => $ref_no, 'business_id' => $this->business_id])->first();

        if (!$products) {
            return abort(404);
        }

        $ware_houses = Warehouses::Where('status',1)->Where('business_id',$this->business_id)->get();

        return view('business.products.view_details', [
            'products' => $products,
            'ware_houses' => $ware_houses
        ]);
    }

    public function get_wareHouse(Request $request)
    {
        $wareHouses = $this->product_repo->warehouse_list($request);

        $data =  Datatables::of($wareHouses)
            ->addIndexColumn()

            ->editColumn('warehouse', function ($item) {
                $name = 'N/A';

                if($item->warehouse_info)
                    $name = Str::limit($item->warehouse_info->name,30);

                return $name;
            })
            ->addColumn('action', function ($item) {

                $edit_btn = '<button style="background-color: #2072AF;border: 1px solid #2072AF;" class="btn btn-md btn-dark  edit-warehouse" data-id="' . $item->id . '"><i class="fa-solid fa-pen-to-square m-r-5"></i></button>';

                return $edit_btn;
            })
            ->rawColumns(['action', 'warehouse'])
            ->make(true);

        return $data;
    }

    public function create_form(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_Product');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $category = Category::where('business_id', $this->business_id)->where('status', 1)->get();
        $sub_category = SubCategory::where('business_id', $this->business_id)->where('status', 1)->get();
        $units = Units::where('business_id', $this->business_id)->where('status', 1)->get();
        $warehouse = Warehouses::where('business_id', $this->business_id)->where('status', 1)->get();
        $supplier = Supplier::where('business_id', $this->business_id)->where('status', 1)->get();


        return view('business.products.create', [
            'category' => $category,
            'sub_category' => $sub_category,
            'units' => $units,
            'warehouse' => $warehouse,
            'supplier' => $supplier
        ]);
    }

    public function get_subcategory(Request $request)
    {
        $sub_category = SubCategory::where('business_id', $this->business_id)->where('category_id', $request->category)->where('status', 1)->get();

        return response()->json(['data' => $sub_category]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'product_name' => 'required|regex:/^[a-zA-Z0-9. ]+$/u|max:191|unique:products,name,NULL,id,deleted_at,NULL,business_id,' . $this->business_id,
                'units' => 'required',
                'category' => 'required',
                'sub_category' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'sort_description' => 'required|min:20|max:190',
                'full_description' => 'required|min:20',
                'warehouses' => 'required',
                'alert_qty' => 'required|min:0|numeric',
                'vendors' => 'required',
                'retail_price' => 'required|between:0,999999999.99',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);

        // Create Product
        $data = $this->product_repo->create_product($request);

        $data['route'] = route('business.products');

        return response()->json($data);
    }

    public function update_form(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_Product');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $request->merge([
            'product_id' => $ref_no,
            'business_id' => $this->business_id
        ]);

        $data = $this->product_repo->product_info($request);

        if ($data['status'] == false) {
            return abort(404);
        }

        // Getting categories
        $actv_category_ids = Category::where('business_id', $this->business_id)->where('status', 1)->pluck('id')->toArray();
        $product_category_id = [$data['data']['category_id']];
        $category_ids = array_merge($actv_category_ids, $product_category_id);
        $category_ids = array_unique($category_ids);
        $category = Category::where('business_id', $this->business_id)->whereIn('id', $category_ids)->get();

        // Getting subcategories
        $act_subcategory_ids = SubCategory::where('business_id', $this->business_id)->where('category_id',$product_category_id)->where('status', 1)->pluck('id')->toArray();
        $product_sub_category_id = [$data['data']['subcategory_id']];
        $subcategory_ids = array_merge($act_subcategory_ids, $product_sub_category_id);
        $subcategory_ids = array_unique($subcategory_ids);
        $sub_category = SubCategory::where('business_id', $this->business_id)->whereIn('id', $subcategory_ids)->get();

        // Getting units
        $act_unit_ids = Units::where('business_id', $this->business_id)->where('status', 1)->pluck('id')->toArray();
        $product_unit_id = [$data['data']['unit_id']];
        $unit_ids = array_merge($act_unit_ids, $product_unit_id);
        $unit_ids = array_unique($unit_ids);
        $units = Units::where('business_id', $this->business_id)->whereIn('id', $unit_ids)->get();

        $act_warehouse_ids = Warehouses::where('business_id', $this->business_id)->where('status', 1)->pluck('id')->toArray();
        $product_warehouse_ids = $data['data']['ware_house_ids'];
        $warehouse_ids = array_merge($act_warehouse_ids, $product_warehouse_ids);
        $warehouse_ids = array_unique($warehouse_ids);
        $warehouse = Warehouses::where('business_id', $this->business_id)->whereIn('id', $warehouse_ids)->get();

        $act_supplier_ids = Supplier::where('business_id', $this->business_id)->where('status', 1)->pluck('id')->toArray();
        $product_supplier_ids = $data['data']['supplier_ids'];
        $supplier_ids = array_merge($act_supplier_ids, $product_supplier_ids);
        $supplier_ids = array_unique($supplier_ids);
        $supplier = Supplier::where('business_id', $this->business_id)->whereIn('id', $supplier_ids)->get();

        return view('business.products.update',[
            'category' => $category,
            'sub_category' => $sub_category,
            'units' => $units,
            'warehouse' => $warehouse,
            'supplier' => $supplier,
            'product' => $data['data']
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $validator = Validator::make(
            $request->all(),
            [
                'product_name' => 'required|regex:/^[a-zA-Z0-9. ]+$/u|max:191|unique:products,name,'.$id.',id,deleted_at,NULL,business_id,' . $this->business_id,
                'units' => 'required',
                'category' => 'required',
                'sub_category' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'sort_description' => 'required|min:20|max:190',
                'full_description' => 'required|min:20',
                'warehouses' => 'required',
                'alert_qty' => 'required|min:0|numeric',
                'vendors' => 'required',
                'retail_price' => 'required|between:0,999999999.99',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);

        // Update Product
        $data = $this->product_repo->update_product($request);

        $data['route'] = route('business.products');

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $delete = $this->product_repo->delete_product($request);

        return response()->json(['status'=>true, 'message'=> 'Selected Product deleted successfully!']);
    }

    public function get_details(Request $request)
    {
        $data = $this->product_repo->get_details($request);

        return response()->json(['status' => true,  'data' => $data]);
    }

    public function getDetails($id)
    {
        $warehouse = ProductWarehouse::find($id);  // Find the warehouse by ID

        if ($warehouse) {
            return response()->json(['data' => $warehouse]);
        }
    }


    public function update_wareHouse(Request $request)
{
    $warehouse = ProductWarehouse::find($request->warehouse_id);  // Find the warehouse by ID

    if ($warehouse) {

        $validator = Validator::make(
            $request->all(),
            [
                'qty' => 'required|min:1|numeric|max:'.$warehouse->qty,
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $qty = $warehouse->qty - $request->qty;


        $warehouse->qty = $qty > 0 ? $qty : 0;
        $warehouse->update();

        return response()->json(['status' => true, 'message' => 'Selected Product QTY value adjusted successfully!']);
    }
}

}
