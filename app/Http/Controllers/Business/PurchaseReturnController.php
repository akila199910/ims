<?php

namespace App\Http\Controllers\Business;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\PurchaseOrders;
use App\Models\PurchaseReturn;
use App\Models\ApprovalHistory;
use App\Models\ProductWarehouse;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReturn_Item;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ProductRepository;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\PurchaseReturnRepository;
use Illuminate\Support\Str;
use PDF;



class PurchaseReturnController extends Controller
{
    //
    private $business_id;
    private $pur_order_repo;
    private $pur_return_repo;
    private $product_repo;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->pur_order_repo = new PurchaseOrderRepository();
        $this->pur_return_repo = new PurchaseReturnRepository();
        $this->product_repo = new ProductRepository();
    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_PurchaseReturn');

        if ($check_premission == false) {
            return abort(404);
        }

        if ($request->json) {
            $request->merge([
                'business_id' => $this->business_id
            ]);

            // Getting Category list
            $pur_return = $this->pur_return_repo->pur_return_list($request);

            $data =  Datatables::of($pur_return)
                ->addIndexColumn()
                ->editColumn('purchased', function ($item) {
                    $purchase_ref = 'N/A';
                    if ($item->purchase_info)
                        $purchase_ref = $item->purchase_info->invoice_id;

                    return $purchase_ref;
                })
                ->editColumn('created_date', function ($item) {
                    return date('Y-m-d', strtotime($item->created_at));
                })
                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="custom-badge status-pending badge-border">Pending</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="custom-badge status-approved badge-borders">Approved</span>';
                    }

                    if ($item->status == 2) {
                        return '<span class="custom-badge badge-borders status-onhold">On Hold</span>';
                    }

                    if ($item->status == 3) {
                        return '<span class="custom-badge badge-borders status-cancelled">Cancelled</span>';
                    }

                    if ($item->status == 4) {
                        return '<span class="custom-badge badge-borders status-fullfilled">Full Filled</span>';
                    }

                    if ($item->status == 5) {
                        return '<span class="custom-badge badge-borders status-returned"> Returned</span>';
                    }

                    if ($item->status == 6) {
                        return '<span class="custom-badge badge-borders status-closed">Closed</span>';
                    }
                })
                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = '';
                    $read_aproval_route = route('business.purchase_return.approval_histories', $item->ref_no);
                    $detail_view = route('business.purchase_return.detail.view', $item->ref_no);

                    if ($item->status == 0) {
                        $edit_route = route('business.purchase_return.update.form', $item->ref_no);
                    }

                    $can_delete = false;
                    if ($item->status == 0 || $item->status == 3) {
                        $can_delete = true;
                    }

                    $actions = '';

                    $actions = action_btns_po_return($actions, $user, $edit_route, $can_delete, $detail_view, 'PurchaseReturn', $item, '');

                    $read_approval = user_any_permission_check($user, ['Read_Return_Approval_History']);

                    if ($read_approval) {
                        $actions .= '<a class="dropdown-item" href="' . $read_aproval_route . '"><i class="fa-solid  fa-circle-check  m-r-5"></i>Return History</a>';
                    }


                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                        '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'purchased', 'created_date', 'status'])
                ->make(true);

            return $data;
        }

        return view('business.pur_return.index');
    }

    public function create_form(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_PurchaseReturn');

        if ($check_premission == false) {
            return abort(404);
        }

        $purchases =  PurchaseOrders::Where('status', 6)->Where('business_id', $this->business_id)->get();

        return view('business.pur_return.create', [
            'purchases' => $purchases
        ]);
    }

    public function pur_item_filter(Request $request)
    {
        $request->merge([
            'not_qty' => true
        ]);

        $data = $this->pur_return_repo->get_purchase_items($request);

        return view('business.pur_return.content.create', [
            'products' => $data
        ]);
    }

    // public function get_products(Request $request)
    // {
    //     $purchase_item = PurchaseOrderItem::where('purchased_id', $request->order_id)->where('product_id', $request->product_id)->first();

    //     $available_qty = 0;
    //     $exist_qty = 0;
    //     if ($purchase_item) {
    //         $available_qty = $purchase_item->available_qty;

    //         $product_ids = ProductWarehouse::where('product_id', $request->product_id)->pluck('product_id')->toArray();
    //         $product = Products::whereIn('id', $product_ids)->get();

    //         if (isset($request->product_array) && !empty($request->product_array)) {
    //             foreach ($request->product_array as $key => $value) {
    //                 if ($request->product_id == $value['product_id']) {
    //                     $exist_qty += $value['qty'];
    //                 }
    //             }
    //         }

    //         $available_qty = $available_qty - $exist_qty;
    //     }

    //     $data = [
    //         'qty' => $available_qty > 0 ? $available_qty : 0,
    //         'product' => $product,
    //         'order_item_id' => $purchase_item->id
    //     ];

    //     return view('business.pur_orders.products.create', [
    //         'purchase_item' => $purchase_item
    //     ]);
    // }

    public function purchase_item_validation(Request $request)
    {
        $av_qty = 0;
        if(isset($request->av_qty) && !empty($request->av_qty))
        $av_qty = $request->av_qty;

        $return_id = NULL;
        if(isset($request->id) && !empty($request->id))
        $return_id = $request->id;

        $validation_rule = [
            'product' => 'required|unique:purchase_return__items,product_id,NULL,id,deleted_at,NULL,order_item_id,'.$request->order_item_id.',return_id,'.$return_id,
            'qty' => 'required|numeric|min:1|max:'.$av_qty,
        ];

        $validator = Validator::make(
            $request->all(),
            $validation_rule
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'errors' => $validator->errors()]);
        }

        $message = [];
        if (isset($request->product_ids) && !empty($request->product_ids)) {
            if (in_array($request->product, $request->product_ids)) {
                $message['product'] = ['The product already has been taken.'];
            }
        }

        if (isset($message) && !empty($message)) {
            return response()->json(['status' => false,  'errors' => $message]);
        }

        $purchase_item = PurchaseOrderItem::find($request->order_item_id);

        return response()->json([
            'status' => true,
            'data' => [
                'pur_order_item_id' => $purchase_item->id,
                'product_id' => $purchase_item->product_id,
                'product_code' => $purchase_item->product_info->product_id ? $purchase_item->product_info->product_id : '',
                'product_name' => $purchase_item->product_info->name,
                'description' => $purchase_item->product_info->name. ' - '. $purchase_item->product_info->unit_info->name,
                'qty' => $request->qty,
                'unit_price' => $purchase_item->unit_price,
                'total_amount' => $request->qty *  $purchase_item->unit_price
            ]
        ]);
    }

    public function update_status(Request $request)
    {
        $pur_return = PurchaseReturn::find($request->pur_return_id);

        if ($pur_return) {

            $pur_return->status = $request->status;
            $pur_return->update();

            if($request->status == 5){

                $pur_return_items = PurchaseReturn_Item::where('return_id', $pur_return->id)->get();

                foreach ($pur_return_items as $item) {
                    $product = Products::where('id', $item->product_id)->where('business_id', $this->business_id)->first();
                    if ($product) {
                        $sum_of_product =$product->qty - $item->qty;
                        $product->qty  = $sum_of_product < 0 ? 0 : $sum_of_product;
                        $product->update();
                    }
                }
            }
            // Approval History
            $history = new ApprovalHistory();
            $history->order_id = 0;
            $history->return_id = $pur_return->id;
            $history->user_id = Auth::user()->id;
            $history->status = $request->status;
            $history->save();

            return response()->json(['status' => true, 'message' => 'Selected Purchase Return Status Updated Successfully!', 'route' => route('business.purchase_return')]);
        }

        return response()->json(['status' => false, 'message' => 'Order not found']);
    }


    public function product_list(Request $request)
    {
        $request->merge([
            'not_qty' => true
        ]);

        $data = $this->pur_return_repo->get_purchase_items($request);

        return response()->json(['status' => true, 'product' => $data]);
    }

    public function get_product(Request $request)
    {
        $purchase_item = PurchaseOrderItem::where('purchased_id', $request->order_id)->where('product_id', $request->product_id)->first();

        $available_qty = 0;
        $exist_qty = 0;
        if ($purchase_item) {
            $available_qty = $purchase_item->available_qty;

            $product_ids = ProductWarehouse::where('product_id', $request->product_id)->pluck('product_id')->toArray();
            $product = Products::whereIn('id', $product_ids)->get();

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
            'product' => $product,
            'order_item_id' => $purchase_item->id
        ];

        return response()->json(['status' => true, 'data' => $data]);
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
            'return_date' => date('Y-m-d')
        ]);

        $data = $this->pur_return_repo->create($request);

        $data['route'] = route('business.purchase_return');

        return response()->json($data);
    }


    public function update_form(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_PurchaseReturn');

        if ($check_premission == false) {
            return abort(404);
        }

        //End
        $pur_return = PurchaseReturn::where('ref_no', $ref_no)->where('business_id', $this->business_id)->first();

        $request->merge([
            'return_id' => $pur_return->ref_no,
            'business_id' => $this->business_id,
            'return_date' => date('Y-m-d')
        ]);

        $pur_return = $this->pur_return_repo->purchase_return_info($request);

        if ($pur_return['status'] == false) {
            return abort(404);
        }

        if ($pur_return['data']['status'] != 0) {
            return redirect()->route('business.pur_return');
        }

        $ordered_item_ids = $pur_return['data']['ordered_product_ids'];

        // dd($pur_return['data']);

        $products = Products::where('business_id', $this->business_id)->where('status', 1)->get();

        return view('business.pur_return.update', [
            'pur_return' => $pur_return['data'],
            'products' => $products
        ]);
    }

    public function update(Request $request)
    {
        $order_by = Auth::user()->id;

        $request->merge([
            'business_id' => $this->business_id,
            'modify_by' => $order_by
        ]);

        $pur_return = PurchaseReturn::find($request->id);

        $data = $this->pur_return_repo->update_purchase($request);

        $data['route'] = route('business.purchase_return');

        return response()->json($data);
    }

    public function product_subtotal(Request $request)
    {

        // dd($request->all());
        $pur_return = PurchaseReturn::find($request->return_id);

        $total_amount = 0;

        if ($pur_return) {
            $total_amount = $pur_return->pur_return_item->sum('total_amount');
        }

        return response()->json(['total_amount' => $total_amount]);
    }

    public function get_order_items(Request $request)
    {
        $pur_return = PurchaseReturn::find($request->return_id);
        $return_products = $pur_return->pur_return_item()->pluck('product_id')->toArray();

        $ordered_item_ids = $pur_return->pur_orderItems()->where('available_qty', '>', 0)->pluck('product_id')->toArray();

        $ordered_item_ids = array_diff($ordered_item_ids, $return_products);

        $products = Products::whereIn('id', $ordered_item_ids)->get();

        return view('business.pur_return.content.add_products', [
            'pur_return' => $pur_return,
            'products' => $products
        ]);

    }

    public function get_return_items_list(Request $request)
    {
        $purchase_items = PurchaseReturn_Item::with(['product_info', 'purchase_info'])->where('return_id', $request->return_id);

        $data =  Datatables::of($purchase_items)
            ->addIndexColumn()
            ->setRowId(function ($row) {
                return "row_" . $row->id; // Set the row ID attribute
            })
            ->editColumn('product_id', function ($item) {
                $product_id = 'N/A';
                if (isset($item->product_info) && !empty($item->product_info))
                    $product_id = $item->product_info->product_id;

                return '<input type="hidden" name="product_ids[]"  value="' . $item->product_id . '">' . $product_id;
            })
            ->editColumn('product_name', function ($item) {
                $product_name = 'N/A';
                $unit_name = 'N/A';
                if (isset($item->product_info->unit_info) && !empty($item->product_info->unit_info))
                    $unit_name = Str::limit($item->product_info->unit_info->name,30);

                if (isset($item->product_info) && !empty($item->product_info))
                    $product_name = Str::limit($item->product_info->name,30);

                return $product_name . ' - ' . $unit_name;
            })
            ->addColumn('category_name', function ($item) {
                $category_name = 'N/A';
                if (isset($item->product_info->category_info) && !empty($item->product_info->category_info))
                    $category_name = Str::limit($item->product_info->category_info->name,30);

                return $category_name;
            })
            ->addColumn('sub_category_name', function ($item) {
                $sub_category_name = 'N/A';
                if (isset($item->product_info->sub_category_info) && !empty($item->product_info->sub_category_info))
                    $sub_category_name = Str::limit($item->product_info->sub_category_info->name,30);

                return $sub_category_name;
            })
            ->addColumn('unit_name', function ($item) {
                $unit_name = 'N/A';
                if (isset($item->product_info->unit_info) && !empty($item->product_info->unit_info))
                    $unit_name = Str::limit($item->product_info->unit_info->name,30);

                return $unit_name;
            })
            ->editColumn('unit_price', function ($item) {
                return '<input type="text" name="retail_prices[]" style="width:100px" class="form-control decimal_val retail_price" value="' . $item->unit_price . '">';
            })
            ->editColumn('qty', function ($item) {
                $av_qty = 0;
                if($item->Purchase_Item_info)
                {
                    $av_qty = $item->Purchase_Item_info->available_qty;
                }

                $av_qty = $av_qty + $item->qty;

               return '
               <input type="hidden" name="pur_order_item_ids[]" style="width:75px" class="form-control number_only_val pur_order_item_id" value="' . $item->order_item_id . '">
               <input type="hidden" name="av_qtys[]" style="width:75px" class="form-control number_only_val av_qty" value="' . $av_qty . '">
               <input type="number" name="request_qtys[]" min="1" max="'.$av_qty.'" style="width:75px" class="form-control number_only_val request_qty" value="' . $item->qty . '">';
            })
            ->editColumn('total_amount', function ($item) {
                return '<input type="hidden" name="total_prices[]" style="width:75px" class="form-control decimal_val total_price" value="' . $item->total_amount . '"> <span class="row_total_price">' . $item->total_amount . '</span>';
            })
            ->addColumn('received_qty', function ($item) {
                return '<input type="number" min="0" max="' . $item->qty . '" name="received_qtys[]" style="width:100px" class="form-control number_only_val received_qty" value="' . $item->received_qty . '">';
            })
            ->addColumn('action', function ($item) {
                $actions = '';

                if ($item->status == 0 || $item->status == 4) {
                    // $actions .= '<button type="button" class="btn btn-sm btn-outline-primary _update_button" onclick="update_qty(' . $item->id . ')" id="' . $item->id . '"><i class="fa fa-edit"></i></button> ';
                    $actions .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="delete_confirmation(' . $item->id . ')"><i class="fa fa-minus"></i></button>';
                }

                return $actions;
            })
            ->rawColumns(['action', 'qty', 'product_name', 'category_name', 'sub_category_name', 'unit_name', 'unit_price', 'received_qty', 'product_id', 'total_amount'])
            ->make(true);

        return $data;
    }

    public function detail_view(Request $request, $ref_no)
    {
        $permissons = ['PO_Return_Approval', 'PO_Return_Hold', 'PO_Return_Cancel', 'PO_Return_Fullfillment', 'PO_Return_Received', 'PO_Return_Closed'];
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_any_permission_check($user, $permissons);

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $request->merge([
            'return_id' => $ref_no,
            'business_id' => $this->business_id
        ]);

        $pur_return = $this->pur_return_repo->purchase_return_info($request);

        // dd($pur_return);

        if ($pur_return['status'] == false) {
            return abort(404);
        }

        $view = 'business.pur_return.detail_view';

        // if ($pur_return['data']['status'] == 5 || $pur_return['data']['status'] == 6) {
        //     $view = 'business.pur_return.products.received_products';
        // }

        return view($view, [
            'pur_return' => $pur_return['data'],

        ]);
    }

    public function get_item_list(Request $request)
    {
        $pur_return_items = PurchaseReturn_Item::with(['Product_info'])->where('return_id', $request->return_id);

        $data =  Datatables::of($pur_return_items)
            ->addIndexColumn()
            ->setRowId(function ($row) {
                return "row_" . $row->id; // Set the row ID attribute
            })
            ->editColumn('product_name', function ($item) {
                $product_name = 'N/A';
                if (isset($item->Product_info) && !empty($item->Product_info))
                    $product_name = Str::limit($item->Product_info->name,30);

                return $product_name;
            })
            ->editColumn('qty', function ($item) {
                return $item->qty;
            })
            ->addColumn('action', function ($item) {
                $actions = '';

                // if ($item->purchase_info->status == 1) {
                //     $actions .= '<button type="button" class="btn btn-sm btn-outline-primary _update_button" id="'.$item->id.'"><i class="fa fa-check"></i></button> ';
                // }

                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="delete_confirmation(' . $item->id . ')"><i class="fa fa-minus"></i></button>';

                return $actions;
            })
            ->rawColumns(['action', 'qty', 'product_name'])
            ->make(true);

        return $data;
    }

    public function item_list(Request $request)
    {
        $purchase_items = PurchaseReturn_Item::with(['product_info'])->where('return_id', $request->return_id);

        // dd($purchase_items);
        $data =  Datatables::of($purchase_items)
            ->addIndexColumn()
            ->setRowId(function ($row) {
                return "row_" . $row->id; // Set the row ID attribute
            })
            ->editColumn('product_id', function ($item) {
                $product_id = 'N/A';
                if (isset($item->product_info) && !empty($item->product_info))
                    $product_id = $item->product_info->product_id;

                return '<input type="hidden" name="product_ids[]"  value="' . $item->product_id . '">' . $product_id;
            })
            ->editColumn('product_name', function ($item) {
                $product_name = 'N/A';
                $unit_name = 'N/A';
                if (isset($item->product_info->unit_info) && !empty($item->product_info->unit_info))
                    $unit_name = Str::limit($item->product_info->unit_info->name,30);

                if (isset($item->product_info) && !empty($item->product_info))
                    $product_name = Str::limit($item->product_info->name,30);

                return $product_name . ' - ' . $unit_name;
            })
            ->addColumn('category_name', function ($item) {
                $category_name = 'N/A';
                if (isset($item->product_info->category_info) && !empty($item->product_info->category_info))
                    $category_name = Str::limit($item->product_info->category_info->name,30);

                return $category_name;
            })
            ->addColumn('sub_category_name', function ($item) {
                $sub_category_name = 'N/A';
                if (isset($item->product_info->sub_category_info) && !empty($item->product_info->sub_category_info))
                    $sub_category_name = Str::limit($item->product_info->sub_category_info->name,30);

                return $sub_category_name;
            })
            ->addColumn('unit_name', function ($item) {
                $unit_name = 'N/A';
                if (isset($item->product_info->unit_info) && !empty($item->product_info->unit_info))
                    $unit_name = Str::limit($item->product_info->unit_info->name,30);

                return $unit_name;
            })
            ->addColumn('unit_price', function ($item) {
                return $item->unit_price;
            })
            ->editColumn('qty', function ($item) {
                return $item->qty;
            })
            ->addColumn('received_qty', function ($item) {
                return $item->received_qty;
            })
            ->addColumn('action', function ($item) {
                $actions = '';

                // if ($item->purchase_info->status == 1) {
                //     $actions .= '<button type="button" class="btn btn-sm btn-outline-primary _update_button" id="'.$item->id.'"><i class="fa fa-check"></i></button> ';
                // }

                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="delete_confirmation(' . $item->id . ')"><i class="fa fa-minus"></i></button>';

                return $actions;
            })
            ->rawColumns(['action', 'qty', 'product_name', 'category_name', 'sub_category_name', 'unit_name', 'unit_price', 'received_qty', 'product_id'])
            ->make(true);

        return $data;
    }

    public function add_item(Request $request)
    {
        $av_qty = isset($request->av_qty) && !empty($request->av_qty) ? $request->av_qty : 0;

        $validator = Validator::make(
            $request->all(),
            [
                'product' => 'required|unique:purchase_return__items,product_id,NULL,id,deleted_at,NULL,order_item_id,' . $request->order_item_id,
                'qty' => 'required|numeric|min:1|max:' .$av_qty,
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $purchase_item = PurchaseOrderItem::find($request->order_item_id);

        $request->merge([
            'product_id' => $request->product,
            'return_id' => $request->return_id,
            'order_item_id' => $request->order_item_id,
            'unit_price' => $purchase_item->unit_price,
            'qty' => $request->qty,
            'total_amount' => $request->qty *  $purchase_item->unit_price
        ]);

        // dd($request->all());

        $data = $this->pur_return_repo->add_pur_item($request);

        return response()->json(['status' => true, 'message' => 'New Purchase Item added successfully!']);
    }

    public function delete_item(Request $request)
    {
        $pur_return_item = PurchaseReturn_Item::find($request->id);

        if ($pur_return_item) {
            $request->merge([
                'return_id' => $request->id
            ]);
            $data = $this->pur_return_repo->delete_pur_item($request);
        }

        return response()->json(['status' => true, 'message' => 'Select Purchase Item delete successfully!']);
    }

    public function delete(Request $request)
    {
        $pur_return = PurchaseReturn::find($request->id);

        if ($pur_return) {
            $data = $this->pur_return_repo->delete_pur_return($request);
        }

        return response()->json(['status' => true, 'message' => 'Select Purchase Return delete successfully!']);
    }

    public function available_products(Request $request)
    {
        $pur_return = PurchaseReturn::find($request->return_id);

        $products = [];
        if ($pur_return) {
            $purchased_order_items_id = $pur_return->pur_orderItems->pluck('product_id')->toArray();

            $return_product_ids = $pur_return->pur_return_item->pluck('product_id')->toArray();

            $product_ids = array_diff($purchased_order_items_id,$return_product_ids);

            $products = Products::whereIn('id',$product_ids)->get();
        }

        return response()->json(['data'=>$products]);
    }

    public function approval_histories(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_PurchaseReturn');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $request->merge([
            'return_id' => $ref_no,
            'business_id' => $this->business_id
        ]);

        $pur_return = $this->pur_return_repo->purchase_return_info($request);

        if ($pur_return['status'] == false) {
            return abort(404);
        }

        return view('business.pur_return.approval_history', [
            'pur_return' => $pur_return['data']
        ]);
    }

    public function approval_history_list(Request $request)
    {
        if ($request->json) {

            $approval_histories = ApprovalHistory::with(['pur_return_Info', 'order_user_info', 'modify_user_info'])->where('return_id', $request->return_id);

            $data =  Datatables::of($approval_histories)
                ->addIndexColumn()
                ->editColumn('invoice_id', function ($item) {
                    return $item->pur_return_Info ?  $item->pur_return_Info->invoice_id  : 'N/A';
                })
                ->editColumn('modify_by', function ($item) {
                    return $item->modify_user_info ?  Str::limit($item->modify_user_info->name,30)  : 'N/A';
                })
                ->editColumn('created_at', function ($item) {
                    return date('Y-m-d H:i:s', strtotime($item->created_at));
                })
                ->editColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="custom-badge status-pending badge-border">Pending</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="custom-badge status-approved badge-borders">Approved</span>';
                    }

                    if ($item->status == 2) {
                        return '<span class="custom-badge badge-borders status-onhold">On Hold</span>';
                    }

                    if ($item->status == 3) {
                        return '<span class="custom-badge badge-borders status-cancelled">Cancelled</span>';
                    }

                    if ($item->status == 4) {
                        return '<span class="custom-badge badge-borders status-fullfilled">Full Filled</span>';
                    }

                    if ($item->status == 5) {
                        return '<span class="custom-badge badge-borders status-returned"> Returned</span>';
                    }

                    if ($item->status == 6) {
                        return '<span class="custom-badge badge-borders status-closed">Closed</span>';
                    }
                })
                ->rawColumns(['status', 'invoice_id', 'modify_by', 'created_at'])
                ->make(true);

            return $data;
        }
    }

    // public function view_details(Request $request, $ref_no)
    // {
    //     //Check User Permission
    //     $user = Auth::user();
    //     $check_premission = user_permission_check($user, 'Read_PurchaseReturn');

    //     if ($check_premission == false) {
    //         return abort(404);
    //     }
    //     // End

    //     $pur_return = PurchaseReturn::with(['purchase_info'])->Where(['ref_no' => $ref_no, 'business_id' => $this->business_id])->first();


    //     if (!$pur_return) {
    //         return abort(404);
    //     }

    //     return view('business.pur_return.view_details', [
    //         'pur_return' =>  $pur_return
    //     ]);

    // }

    public function download_pdf($ref_no)
    {
        $pur_return = PurchaseReturn::where('ref_no', $ref_no)->first();

        if (!$pur_return) {
            return abort(404);
        }

        $data = [
            'pur_return' => $pur_return
        ];

        $pdf = PDF::loadView('business.pur_return.download', $data);
        return $pdf->download('pur_return' . date('Ymdhis') . '.pdf');

        // dd($purchase);
    }



}
