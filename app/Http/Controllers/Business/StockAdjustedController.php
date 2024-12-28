<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductWarehouse;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrders;
use App\Models\StockAdjustedItems;
use App\Models\StockAdjustments;
use App\Models\Warehouses;
use App\Repositories\StockAdjustedRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StockAdjustedController extends Controller
{
    //
    private $stock_ad_repo;
    private $business_id;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->stock_ad_repo = new StockAdjustedRepository();
    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_StockAdjustment');

        if ($check_premission == false) {
            return abort(404);
        }

        if ($request->json) {

            $request->merge([
                'business_id' => $this->business_id
            ]);

            // Getting Category list
            $stock_adjust = $this->stock_ad_repo->stockAdjust_list($request);

            $data =  Datatables::of($stock_adjust)
                ->addIndexColumn()
                ->editColumn('purchased', function($item){
                    $purchase_ref = 'N/A';
                    if($item->purchase_info)
                        $purchase_ref = $item->purchase_info->invoice_id;

                    return $purchase_ref;
                })
                ->editColumn('created_date', function($item){
                    return date('Y-m-d', strtotime($item->created_at));
                })
                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = route('business.stock_adjusted.update.form', $item->ref_no);
                    $view_url = route('business.stock_adjusted.view_details', $item->ref_no);

                    $actions = '';
                    $actions = action_btns($actions, $user, 'StockAdjustment', $edit_route, $item->id,'',$view_url);

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                    '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'purchased','created_date'])
                ->make(true);

            return $data;
        }

        return view('business.stock_adjust.index');
    }

    public function create_form(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_StockAdjustment');

        if ($check_premission == false) {
            return abort(404);
        }

        $purchases =  PurchaseOrders::Where('status',6)->Where('business_id',$this->business_id)->get();

        return view('business.stock_adjust.create',[
            'purchases' => $purchases
        ]);
    }

    public function Stock_item_filter(Request $request)
    {
        $request->merge([
            'not_qty' => true
        ]);

        $data = $this->stock_ad_repo->get_purchase_items($request);

        return view('business.stock_adjust.content.create',[
            'products' => $data
        ]);
    }

    public function get_ware_house(Request $request)
    {
       $purchase_item = PurchaseOrderItem::where('purchased_id',$request->order_id)->where('product_id',$request->product_id)->first();

        $ware_house = [];
        $available_qty = 0;
        $exist_qty = 0;
        if ($purchase_item) {
            $available_qty = $purchase_item->available_qty;

            $ware_house_ids = ProductWarehouse::where('product_id',$request->product_id)->pluck('warehouse_id')->toArray();
            $ware_house = Warehouses::whereIn('id',$ware_house_ids)->get();

            if (isset($request->product_array) && !empty($request->product_array)) {
                foreach ($request->product_array as $key => $value) {
                    if ($request->product_id == $value['product_id']) {
                        $exist_qty += $value['qty'];
                    }
                }
            }

            $available_qty = $available_qty - $exist_qty;
        }

        $data = [
            'qty' => $available_qty > 0 ? $available_qty : 0,
            'ware_house' => $ware_house,
            'order_item_id' => $purchase_item->id
        ];

        return response()->json(['status'=>true, 'data'=>$data]);
    }

    public function add_update_item(Request $request)
    {
        $av_qty = isset($request->av_qty) && !empty($request->av_qty) ? $request->av_qty : 0;

        $validator = Validator::make(
            $request->all(),
            [
                'product' => 'required',
                'warehouse' => 'required',
                'qty' => 'required|numeric|min:1|max:'.$av_qty,
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $prod_warehouse = $request->product.'_'.$request->warehouse;
        $message = [];
        if(isset($request->product_warehouse) && !empty($request->product_warehouse))
        {
            if (in_array($prod_warehouse, $request->product_warehouse)) {
                $message['product'] = ['The product and warehouse already has been taken.'];
            }
        }

        if (isset($message) && !empty($message)) {
            return response()->json(['status' => false,  'message' => $message]);
        }

        $product = Products::find($request->product);
        $warehouse = Warehouses::find($request->warehouse);

        $data = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'warehouse_id' => $warehouse->id,
            'warehouse_name' => $warehouse->name,
            'qty' => $request->qty,
            'product_warehouse_ids' => $product->id.'_'.$warehouse->id,
            'order_item_id' => $request->order_item_id
        ];

        return response()->json(['status'=>true, 'data'=>$data]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'purchase_ref' => 'required',
                'product_ids' => 'required'
            ],
            [
                'product_ids.required' => 'Fill atleast one product to list'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $business_id = $this->business_id;

        $request->merge([
            'business_id' => $business_id,
            'adjusted_date' => date('Y-m-d')
        ]);

        $data = $this->stock_ad_repo->create($request);

        $data['route'] = route('business.stock_adjusted');

        return response()->json($data);
    }

    public function update_form(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_StockAdjustment');

        if ($check_premission == false) {
            return abort(404);
        }

        $stock_adjust = StockAdjustments::where('ref_no',$ref_no)->where('business_id',$this->business_id)->first();

        if (!$stock_adjust) {
            return abort(404);
        }

        $request->merge([
            'not_qty' => true,
            'order_id' => $stock_adjust->purchased_id
        ]);

        $data = $this->stock_ad_repo->get_purchase_items($request);

        return view('business.stock_adjust.update',[
            'stock_adjust' => $stock_adjust,
            'products' => $data
        ]);
    }

    public function product_list(Request $request)
    {
        $request->merge([
            'not_qty' => true
        ]);

        $data = $this->stock_ad_repo->get_purchase_items($request);

        return response()->json(['status' => true, 'product' => $data]);
    }

    public function get_item_list(Request $request)
    {
        $adjust_items = StockAdjustedItems::with(['Product_info','warehouse_info'])->where('adjusted_id',$request->adjust_id);

        $data =  Datatables::of($adjust_items)
            ->addIndexColumn()
            ->setRowId(function ($row) {
                return "row_".$row->id; // Set the row ID attribute
            })
            ->editColumn('product_name', function ($item) {
                $product_name = 'N/A';
                if (isset($item->Product_info) && !empty($item->Product_info))
                    $product_name = Str::limit($item->Product_info->name,30);

                return $product_name;
            })
            ->editColumn('warehouse_name', function ($item) {
                $warehouse_name = 'N/A';
                if (isset($item->warehouse_info) && !empty($item->warehouse_info))
                    $warehouse_name = Str::limit($item->warehouse_info->name,30);

                return $warehouse_name;
            })
            ->editColumn('qty', function ($item) {
                return $item->qty;
            })
            ->addColumn('action', function ($item) {
                $actions = '';

                // if ($item->purchase_info->status == 1) {
                //     $actions .= '<button type="button" class="btn btn-sm btn-outline-primary _update_button" id="'.$item->id.'"><i class="fa fa-check"></i></button> ';
                // }

                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="delete_confirmation('.$item->id.')"><i class="fa fa-minus"></i></button>';

                return $actions;
            })
            ->rawColumns(['action', 'qty', 'product_name', 'warehouse_name'])
            ->make(true);

        return $data;
    }

    public function add_item(Request $request)
    {
        $av_qty = isset($request->av_qty) && !empty($request->av_qty) ? $request->av_qty : 0;

        $validator = Validator::make(
            $request->all(),
            [
                'product' => 'required|unique:stock_adjusted_items,product_id,NULL,id,deleted_at,NULL,warehouse_id,'.$request->warehouse.',order_item_id,'.$request->order_item_id,
                'warehouse' => 'required',
                'qty' => 'required|numeric|min:1|max:'.$av_qty,
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'product_id' => $request->product,
            'warehouse_id' => $request->warehouse
        ]);

        $data = $this->stock_ad_repo->add_stock_item($request);

        return response()->json(['status' => true, 'message' => 'New adjust item added successfully!']);
    }

    public function view_details(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_StockAdjustment');

        if ($check_premission == false) {
            return abort(404);
        }
        // End

        $adjust_items = StockAdjustments::with(['purchase_info','stock_adjust_item'])->Where(['ref_no' => $ref_no, 'business_id' => $this->business_id])->first();


        if (!$adjust_items) {
            return abort(404);
        }

        return view('business.stock_adjust.view_details', [
            'adjust_items' =>  $adjust_items
        ]);
    }


    public function delete_item(Request $request)
    {
        $adjust_item = StockAdjustedItems::find($request->id);

        if ($adjust_item) {
            $request->merge([
                'adjust_id' => $request->id
            ]);
            $data = $this->stock_ad_repo->delete_stock_item($request);
        }

        return response()->json(['status' => true, 'message' => 'Select adjust item delete successfully!']);
    }

    public function delete(Request $request)
    {
        $stock_adjust = StockAdjustments::find($request->id);

        if ($stock_adjust) {
            $data = $this->stock_ad_repo->delete_stock_adjust($request);
        }

        return response()->json(['status' => true, 'message' => 'Select adjust item delete successfully!']);
    }
}
