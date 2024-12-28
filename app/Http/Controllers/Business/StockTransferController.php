<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductWarehouse;
use App\Models\StockTransfer;
use App\Models\Warehouses;
use App\Repositories\StockTransfersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class StockTransferController extends Controller
{
    //
    private $stock_transfer_repo;
    private $business_id;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->stock_transfer_repo = new StockTransfersRepository();
    }


    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_StockTransfer');

        if ($check_premission == false) {
            return abort(404);
        }

        if ($request->json) {

            $request->merge([
                'business_id' => $this->business_id
            ]);

            // Getting Transfer list
            $stock_transfer = $this->stock_transfer_repo->transfer_list($request);

            $data =  Datatables::of($stock_transfer)
                ->addIndexColumn()
                ->addColumn('image', function ($item) {

                    // $url = $item->product_info ? config('awsurl.url').($item->product_info->image) : '';
                    if (!isset($item->product_info) ||  $item->product_info->image == '' || $item->product_info->image == 0) {
                        return '<img src="'.asset('layout_style/img/icons/product_100.png').'"  border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                    }

                    $url = config('awsurl.url').($item->product_info->image);
                    return '<img src="' . $url . '"  border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                })
                ->editColumn('product_name', function ($item) {
                    return ($item->product_info) ? Str::limit($item->product_info->name,30) : 'N/A';
                })
                ->editColumn('warehouse_from', function ($item) {
                    return ($item->from_warehouse) ? Str::limit($item->from_warehouse->name,30) : 'N/A';
                })
                ->editColumn('warehouse_to', function ($item) {
                    return ($item->to_warehouse) ? Str::limit($item->to_warehouse->name,30) : 'N/A';
                })
                ->editColumn('created_by', function ($item) {
                    return ($item->creator_info) ? Str::limit($item->creator_info->name,30) : 'N/A';
                })
                ->editColumn('edit_by', function ($item) {
                    return ($item->editor_info) ? Str::limit($item->editor_info->name,30) : 'N/A';
                })
                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = route('business.stock_transfer.update.form', $item->ref_no);
                    $view_url = route('business.stock_transfer.view_details', $item->ref_no);

                    $actions = '';
                    $actions = action_btns_model($actions, $user, 'StockTransfer', $edit_route, $item->id,'', $view_url);

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                    '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'product_name','image', 'warehouse_from', 'warehouse_to','created_by','edit_by'])
                ->make(true);

            return $data;
        }

        return view('business.stock_transfer.index');
    }

    public function create_form()
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_StockTransfer');

        if ($check_premission == false) {
            return abort(404);
        }

        $ware_house = Warehouses::where('status',1)->where('business_id',$this->business_id)->get();

        return view('business.stock_transfer.create',[
            'ware_house' => $ware_house
        ]);
    }

    public function get_warehouse(Request $request)
    {
        $ware_house = Warehouses::where('status',1)->where('id','!=',$request->from_id)->where('business_id',$this->business_id)->get();

        return response()->json(['status'=>true, 'data'=>$ware_house]);
    }

    public function get_trasnfer_item(Request $request)
    {
        $data = $this->stock_transfer_repo->available_products($request);

        return view('business.stock_transfer.create_content',[
            'products' => $data
        ]);
    }

    public function add_update_item(Request $request)
    {
        $av_qty = (isset($request->av_qty) && !empty($request->av_qty)) ? $request->av_qty : 0;
        $validator = Validator::make(
            $request->all(),
            [
                'product' => 'required',
                'qty' => 'required|numeric|min:1|max:'.$av_qty

            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        if (isset($request->product_array) && !empty($request->product_array)) {
            if (in_array($request->product, $request->product_array)) {
                return response()->json(['status' => false,  'message' => ['product' => 'The product has been already taken.']]);
            }
        }

        $product = Products::find($request->product);

        $data = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'qty' => $request->qty
        ];

        return response()->json(['status' => true, 'data'=>$data]);
    }

    public function get_product(Request $request)
    {
        $product = Products::find($request->product_id);
        $product_qty = ProductWarehouse::where('product_id',$request->product_id)->where('warehouse_id',$request->warehouse_from)->first();

        $data = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'qty' => $product_qty->qty
        ];

        return response()->json(['status' => true, 'data'=>$data]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'warehouse_from' => 'required',
                'warehouse_to' => 'required',
                'product_ids' => 'required'
            ],
            [
                'product_ids.required' => 'Fill atleast one product details.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'transfer_date' => date('Y-m-d'),
            'created_by' => Auth::user()->id,
            'edit_by' => Auth::user()->id,
            'business_id' => $this->business_id
        ]);

        $data = $this->stock_transfer_repo->create_transfer($request);

        $data['route'] = route('business.stock_transfer');

        return response()->json($data);
    }

    public function update_form(Request $request)
    {
        $stock_transfer = StockTransfer::find($request->id);

        $product = Products::find($stock_transfer->product_id);
        $product_qty = ProductWarehouse::where('product_id',$stock_transfer->product_id)->where('warehouse_id',$stock_transfer->warehouse_from)->first();
        $transfered_qty = $stock_transfer->qty;
        $av_qty = 0;
        if ($product_qty) {
            $av_qty = $product_qty->qty;
        }

        $av_qty = $av_qty + $transfered_qty;

        return view('business.stock_transfer.update',[
            'product' => $product,
            'stock_transfer' => $stock_transfer,
            'av_qty' => $av_qty
        ]);
    }

    public function update(Request $request)
    {
        $av_qty = (isset($request->av_qty) && !empty($request->av_qty)) ? $request->av_qty : 0;
        $validator = Validator::make(
            $request->all(),
            [
                'qty' => 'required|numeric|min:1|max:'.$av_qty
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'edit_by' => Auth::user()->id
        ]);

        $data = $this->stock_transfer_repo->update_transfer($request);

        $data['route'] = route('business.stock_transfer');

        return response()->json($data);
    }

    public function view_details(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_StockTransfer');

        if ($check_premission == false) {
            return abort(404);
        }
        // End

        $stock_transfer = StockTransfer::with(['from_warehouse', 'to_warehouse', 'creator_info', 'editor_info','product_info'])->Where(['ref_no' => $ref_no, 'business_id' => $this->business_id])->first();

        if (!$stock_transfer) {
            return abort(404);
        }

        return view('business.stock_transfer.view_details', [
            'stock_transfer' =>  $stock_transfer
        ]);
    }

    public function delete(Request $request)
    {
        StockTransfer::destroy($request->id);

        return response()->json(['status'=>true, 'message' => 'Selected Stock transfer deleted successfully!']);
    }
}
