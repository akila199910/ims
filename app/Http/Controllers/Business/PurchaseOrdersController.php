<?php

namespace App\Http\Controllers\Business;

use PDF;
use App\Models\Business;
use App\Models\Products;
use App\Models\Supplier;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\payement_type;
use App\Models\PurchaseOrders;
use App\Models\Product_Supplier;
use App\Models\PurchaseOrderItem;
use App\Http\Controllers\Controller;
use App\Models\ApprovalHistory;
use App\Models\SupplierPaymentInfo;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ProductRepository;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PurchaseOrderRepository;
use Illuminate\Support\Str;


class PurchaseOrdersController extends Controller
{
    //
    private $business_id;
    private $pur_orders_repo;
    private $product_repo;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->pur_orders_repo = new PurchaseOrderRepository();
        $this->product_repo = new ProductRepository();
    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_PurchaseOrder');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $suppliers = Supplier::where('business_id', $this->business_id)->get();

        if ($request->json) {
            $request->merge([
                'business_id' => $this->business_id
            ]);

            $pur_orders = $this->pur_orders_repo->purchase_list($request);

            $data =  Datatables::of($pur_orders)
                ->addIndexColumn()
                ->addColumn('supplier', function ($item) {
                    $supplier = 'N/A';
                    if (isset($item->supplier_Info) && !empty($item->supplier_Info))
                        $supplier = Str::limit($item->supplier_Info->name,30);

                    return $supplier;
                })
                ->addColumn('order_by', function ($item) {
                    $order_by = 'N/A';
                    if (isset($item->order_user_info) && !empty($item->order_user_info))
                        $order_by = Str::limit($item->order_user_info->name,30);

                    return $order_by;
                })
                ->addColumn('created_at', function ($item) {
                    return date('Y-m-d', strtotime($item->created_at));
                })
                ->addColumn('modify_by', function ($item) {
                    $modify_by = 'N/A';
                    if (isset($item->modify_user_info) && !empty($item->modify_user_info))
                        $modify_by = Str::limit($item->modify_user_info->name,30);

                    return $modify_by;
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
                        return '<span class="custom-badge badge-borders status-received">Received</span>';
                    }

                    if ($item->status == 6) {
                        return '<span class="custom-badge badge-borders status-closed">Closed</span>';
                    }
                })
                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = '';
                    $read_aproval_route = route('business.purchaseorder.approval_histories', $item->ref_no);
                    $detail_view = route('business.purchaseorder.detail.view', $item->ref_no);

                    if ($item->status == 0) {
                        $edit_route = route('business.purchaseorder.update.form', $item->ref_no);
                    }

                    $can_delete = false;
                    if ($item->status == 0 || $item->status == 3) {
                        $can_delete = true;
                    }

                    $actions = '';

                    $actions = action_btns_po($actions, $user, $edit_route, $can_delete, $detail_view, 'PurchaseOrder', $item);

                    $read_approval = user_any_permission_check($user, ['Read_Approval_History']);

                    if ($read_approval) {
                        $actions .= '<a class="dropdown-item" href="' . $read_aproval_route . '"><i class="fa-solid  fa-circle-check  m-r-5"></i> Approval History</a>';
                    }

                    $paid_amount = $item->payment_list()->sum('paid_amount');

                    $due_amount =  $item->final_amount - $paid_amount;

                    $due_amount = $due_amount > 0 ? $due_amount : 0;

                    $add_payment = user_any_permission_check($user, ['Create_Payement']);

                    if ($add_payment && ($due_amount > 0)) {
                        $pay_add_route = '';
                        $actions .= '<a class="dropdown-item" href="javascript:;" onclick="open_create_model('.$item->id.')"><i class="fa-solid  fas fa-tag  m-r-5"></i> Add Payment</a>';
                    }

                    $read_payment = user_any_permission_check($user, ['Read_Payement']);

                    if ($read_payment) {
                        $pay_history_route = route('business.purchaseorder.payments', $item->ref_no);
                        $actions .= '<a class="dropdown-item" href="' . $pay_history_route . '"><i class="fa-solid  fas fa-tags  m-r-5"></i> Payment History</a>';
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
                ->rawColumns(['action', 'status', 'supplier', 'order_by', 'modify_by', 'created_at'])
                ->make(true);

            return $data;
        }

        return view('business.pur_orders.index', [
            'suppliers' => $suppliers
        ]);
    }

    public function create_form(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_PurchaseOrder');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $suppliers = Supplier::where('business_id', $this->business_id)->where('status', 1)->get();
        $products = Products::where('business_id', $this->business_id)->where('status', 1)->get();

        return view('business.pur_orders.create', [
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    public function get_products(Request $request)
    {
        $product_supplier_id = Product_Supplier::where('supplier_id', $request->supplier_id)->pluck('product_id')->toArray();

        $products = Products::where('business_id', $this->business_id)->whereIn('id', $product_supplier_id)->where('status', 1)->get();

        $supplier = Supplier::find($request->supplier_id);
        $payement_term = $supplier->payment_information->PaymentTermsInfo->payement_term;
        $pay_types = PaymentType::all();

        return view('business.pur_orders.products.create', [
            'products' => $products,
            'supplier' => $supplier,
            'payement_term' => $payement_term,
            'pay_types' => $pay_types
        ]);
    }

    public function purchase_item_validation(Request $request)
    {
        $validation_rule = [
            'qty' => 'required|numeric|min:1',
            'product' => 'required|'
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

        $product = Products::find($request->product);

        $request->merge([
            'product_id' => $product->ref_no,
            'business_id' => $this->business_id
        ]);

        $product_data =  $this->product_repo->product_info($request);
        $product_data['data']['qty'] =  $request->qty;
        $product_data['data']['total_price'] = $product_data['data']['retail_price'] *  $request->qty;

        if (isset($request->id) && !empty($request->id)) {

            $this->pur_orders_repo->add_purchase_Item($request);
        }

        return response()->json([
            'status' => true,
            'data' => $product_data['data']
        ]);
    }

    public function add_purchase_item(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'product' => 'required|unique:purchase_order_items,product_id,NULL,id,deleted_at,NULL,purchased_id,' . $request->order_id,
                'qty' => 'required|numeric|min:1'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'errors' => $validator->errors()]);
        }

        $product = Products::find($request->product);


        $request->merge([
            'purchased_id' => $request->order_id,
            'product_id' => $request->product,
            'unit_price' => $product->retail_price,
            'received_qty' => $request->qty
        ]);

        $this->pur_orders_repo->add_purchase_Item($request);

        return response()->json(['status' => true, 'message' => 'New Order Item Added Successfully!']);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'supplier' => 'required',
                'product_ids' => 'required'
            ],
            [
                'product_ids.required' => 'Select atleast one product'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $business_id = $this->business_id;

        $order_by = Auth::user()->id;

        $modify_by = Auth::user()->id;

        $request->merge([
            'business_id' => $business_id,
            'order_by' => $order_by,
            'modify_by' => $order_by,
            'purchased_date' => date('Y-m-d')
        ]);

        $data = $this->pur_orders_repo->create($request);

        $data['route'] = route('business.purchaseorder');

        return response()->json($data);
    }

    public function get_items(Request $request)
    {
        $id = $request->id;

        $purchase_Item = PurchaseOrderItem::with(['product_info'])->where('purchased_id', $id)->get();

        $data = [];

        foreach ($purchase_Item as $item) {
            $data[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product' => isset($item->product_info) ?  $item->product_info->name : '',
                'qty' => $item->qty,

            ];
        }

        return response()->json(['data' => $data]);
    }

    public function update_form(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_PurchaseOrder');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $request->merge([
            'order_id' => $ref_no,
            'business_id' => $this->business_id
        ]);

        $pur_orders = $this->pur_orders_repo->purchase_order_info($request);

        if ($pur_orders['status'] == false) {
            return abort(404);
        }

        if ($pur_orders['data']['status'] != 0) {
            return redirect()->route('business.purchaseorder');
        }

        $ordered_item_ids = $pur_orders['data']['ordered_product_ids'];

        $act_supplier_id = Supplier::where('business_id', $this->business_id)->where('status', 1)->pluck('id')->toArray();
        $supplier_id = [$pur_orders['data']['supplier_id']];
        $supplier_ids = array_merge($act_supplier_id, $supplier_id);
        $supplier_ids = array_unique($supplier_ids);
        $suppliers = Supplier::where('business_id', $this->business_id)->whereIn('id', $supplier_ids)->get();

        $products = Products::where('business_id', $this->business_id)->where('status', 1)->get();

        $product_supplier_id = Product_Supplier::where('supplier_id', $pur_orders['data']['supplier_id'])->whereNotIn('product_id', $ordered_item_ids)->pluck('product_id')->toArray();

        $products = Products::where('business_id', $this->business_id)->whereIn('id', $product_supplier_id)->where('status', 1)->get();

        // dd( $pur_orders['data']);

        return view('business.pur_orders.update', [
            'purchase' => $pur_orders['data'],
            'products' => $products
        ]);
    }

    public function get_order_items(Request $request)
    {
        $purchase = PurchaseOrders::find($request->order_id);

        $ordered_item_ids = $purchase->pur_orderItems->pluck('product_id')->toArray();

        $product_supplier_id = Product_Supplier::where('supplier_id', $purchase->supplier_id)->whereNotIn('product_id', $ordered_item_ids)->pluck('product_id')->toArray();

        $products = Products::where('business_id', $this->business_id)->whereIn('id', $product_supplier_id)->where('status', 1)->get();

        $pay_types = PaymentType::all();

        return view('business.pur_orders.products.add_products', [
            'purchase' => $purchase,
            'products' => $products,
            'pay_types' => $pay_types
        ]);
    }

    public function get_order_items_list(Request $request)
    {
        $purchase_items = PurchaseOrderItem::with(['product_info', 'purchase_info'])->where('purchased_id', $request->order_id);

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
                if (in_array($item->purchase_info->status, [1, 2, 3, 5])) {
                    return '<input type="hidden" name="retail_prices[]" style="width:100px" class="form-control decimal_val retail_price" value="' . $item->unit_price . '">' . $item->unit_price;
                }
                return '<input type="text" name="retail_prices[]" style="width:100px" class="form-control decimal_val retail_price" value="' . $item->unit_price . '">';
                // return $item->unit_price;
            })
            ->editColumn('qty', function ($item) {
                if (in_array($item->purchase_info->status, [4])) {
                    return '<input type="hidden" name="request_qtys[]" style="width:75px" class="form-control number_only_val request_qty" value="' . $item->qty . '">' . $item->qty;
                }
                return '<input type="text" name="request_qtys[]" style="width:75px" class="form-control number_only_val request_qty" value="' . $item->qty . '">';
                // return $item->purchase_info->status;
            })
            ->editColumn('total_amount', function ($item) {
                return '<input type="hidden" name="total_prices[]" style="width:75px" class="form-control decimal_val total_price" value="' . $item->total_amount . '"> <span class="row_total_price">' . $item->total_amount . '</span>';
                // return $item->qty;
            })
            ->addColumn('received_qty', function ($item) {
                return '<input type="number" min="0" max="' . $item->qty . '" name="received_qtys[]" style="width:100px" class="form-control number_only_val received_qty" value="' . $item->received_qty . '">';
            })
            ->addColumn('action', function ($item) {
                $actions = '';

                if ($item->purchase_info->status == 0 || $item->purchase_info->status == 4) {
                    // $actions .= '<button type="button" class="btn btn-sm btn-outline-primary _update_button" onclick="update_qty(' . $item->id . ')" id="' . $item->id . '"><i class="fa fa-edit"></i></button> ';
                    $actions .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="delete_confirmation(' . $item->id . ')"><i class="fa fa-minus"></i></button>';
                }

                return $actions;
            })
            ->rawColumns(['action', 'qty', 'product_name', 'category_name', 'sub_category_name', 'unit_name', 'unit_price', 'received_qty', 'product_id', 'total_amount'])
            ->make(true);

        return $data;
    }

    public function get_order_items_list_view(Request $request)
    {
        $purchase_items = PurchaseOrderItem::with(['product_info', 'purchase_info'])->where('purchased_id', $request->order_id);

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
                return $item->unit_price;
            })
            ->editColumn('qty', function ($item) {
                return $item->qty;
            })
            ->editColumn('total_amount', function ($item) {
                // return '<input type="hidden" name="total_prices[]" style="width:75px" class="form-control decimal_val total_price" value="'.$item->total_amount.'"> <span class="row_total_price">'.$item->total_amount.'</span>';
                return $item->total_amount;
            })
            ->addColumn('received_qty', function ($item) {
                return '<input type="number" min="0" max="' . $item->qty . '" name="received_qtys[]" style="width:100px" class="form-control number_only_val received_qty" value="' . $item->received_qty . '">';
            })
            ->addColumn('action', function ($item) {
                $actions = '';

                if ($item->purchase_info->status == 0 || $item->purchase_info->status == 4) {
                    // $actions .= '<button type="button" class="btn btn-sm btn-outline-primary _update_button" onclick="update_qty(' . $item->id . ')" id="' . $item->id . '"><i class="fa fa-edit"></i></button> ';
                    $actions .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="delete_confirmation(' . $item->id . ')"><i class="fa fa-minus"></i></button>';
                }

                return $actions;
            })
            ->rawColumns(['action', 'qty', 'product_name', 'category_name', 'sub_category_name', 'unit_name', 'unit_price', 'received_qty', 'product_id', 'total_amount'])
            ->make(true);

        return $data;
    }

    public function delete_item(Request $request)
    {
        PurchaseOrderItem::destroy($request->id);

        return response()->json(['status' => true, 'message' => 'Deleted']);
    }

    public function update(Request $request)
    {
        $order_by = Auth::user()->id;

        $request->merge([
            'business_id' => $this->business_id,
            'modify_by' => $order_by
        ]);

        $purchase_order = PurchaseOrders::find($request->id);


        if ($purchase_order->status == 0 && $purchase_order->supplier_Info->payment_information->PaymentTermsInfo->payement_term == 'Prepay') {
            if ((isset($request->prepay_amount) && !empty($request->prepay_amount)) ||
                (isset($request->paid_date) && !empty($request->paid_date)) ||
                (isset($request->paid_type) && !empty($request->paid_type)) ||
                (isset($request->payment_reference) && !empty($request->payment_reference))
            ) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'prepay_amount' => 'required|numeric|between:1,' . $request->net_total_amount,
                        'paid_date' => 'required|before_or_equal:' . date('Y-m-d'),
                        'paid_type' => 'required',
                        'payment_reference' => 'nullable|max:190|unique:purchase_payements,payment_reference',
                        'scan_document' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,doc,pdf|max:2048',
                    ]
                );

                if ($validator->fails()) {
                    return response()->json(['status' => false,  'message' => $validator->errors()]);
                }
            }
        }


        $data = $this->pur_orders_repo->update_purchase($request);


        $data['route'] = route('business.purchaseorder');

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        PurchaseOrders::destroy($request->id);

        return response()->json(['status' => true, 'message' => 'Selected Purchase deleted successfully!']);
    }

    public function send_mail(Request $request)
    {
        $purchase_order = PurchaseOrders::find($request->id);

        $data["purchase_order"] = $purchase_order;
        $data["supplier"] = $purchase_order->supplier_Info;
        $data["title"] = 'New Purchase Order Request | ' . $purchase_order->business_info->name;
        $data["email"] = $purchase_order->supplier_Info->email;
        $data["name"] = $purchase_order->supplier_Info->name;
        $data["view"] = 'mail.order_mail';

        $pdf = PDF::loadView('business.pur_orders.download', $data);

        mailNotificationAttach($data, $pdf->output());

        return response()->json(['status' => true, 'message' => 'Selected Purchase Order PDF sent successfully!']);
    }

    public function product_subtotal(Request $request)
    {
        $pur_order = PurchaseOrders::find($request->order_id);
        $total_amount = 0;

        if ($pur_order) {
            $total_amount = $pur_order->pur_orderItems->sum('total_amount');
        }

        return response()->json(['total_amount' => $total_amount]);
    }

    public function get_item_info(Request $request)
    {
        $pur_item = PurchaseOrderItem::find($request->item_id);
        $unit_price = $pur_item->unit_price;
        $qty = $pur_item->qty;

        return response()->json(['qty' => $qty, 'item_id' => $request->item_id]);
    }

    public function update_qty(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'edit_qty' => 'required|numeric|min:1'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $pur_item = PurchaseOrderItem::find($request->order_item_id);

        $unit_price = $pur_item->unit_price;
        $qty = $request->edit_qty;
        $total_amount = $qty * $unit_price;

        $pur_item->qty = $qty;
        $pur_item->total_amount = $total_amount;
        $pur_item->update();

        return response()->json(['qty' => $qty, 'item_id' => $request->order_item_id]);
    }

    public function detail_view(Request $request, $ref_no)
    {
        $permissons = ['PO_Approval', 'PO_Hold', 'PO_Cancel', 'PO_Fullfillment', 'PO_Received', 'PO_Closed'];
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_any_permission_check($user, $permissons);

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $request->merge([
            'order_id' => $ref_no,
            'business_id' => $this->business_id
        ]);

        $pur_orders = $this->pur_orders_repo->purchase_order_info($request);


        if ($pur_orders['status'] == false) {
            return abort(404);
        }

        $pay_types = PaymentType::all();

        $view = 'business.pur_orders.detail_view';

        if ($pur_orders['data']['status'] == 5 || $pur_orders['data']['status'] == 6) {
            $view = 'business.pur_orders.products.received_products';
        }

        return view($view, [
            'purchase' => $pur_orders['data'],
            'pay_types' => $pay_types
        ]);
    }

    public function update_status(Request $request)
    {
        $purchase = PurchaseOrders::find($request->purchase_id);

        if ($purchase) {

            // this should be stored in approval login user id in there PurchaseOrders table in approval_by column
            $purchase->approved_by = Auth::user()->id;
            $supplier_payment = SupplierPaymentInfo::where('supplier_id', $purchase->supplier_id)->first();

            $payement_term = '';
            if ($supplier_payment) {
                $payement_term = $supplier_payment->PaymentTermsInfo->payement_term;
            }

            $first_payment = true;
            if ($payement_term == 'Prepay' && $request->status == '1') {
                $pay_count = count($purchase->payment_list);
                $first_payment = $pay_count > 0 ? true : false;
            }

            if ($first_payment == false) {
                $route = route('business.purchaseorder.update.form', $purchase->ref_no);
                return response()->json(['status' => false, 'message' => 'Supplier needed the Pre payment. Pay the first payment and continue the process', 'route' => $route]);
            }

            $purchase->status = $request->status;
            $purchase->update();

            // Approval History
            $history = new ApprovalHistory();
            $history->return_id = 0;
            $history->order_id = $purchase->id;
            $history->user_id = Auth::user()->id;
            $history->status = $request->status;
            $history->save();

            if ($request->status == 4) {

                $data["purchase_order"] = $purchase;
                $data["supplier"] = $purchase->supplier_Info;
                $data["title"] = 'New Purchase Order Request | ' . $purchase->business_info->name;
                $data["email"] = $purchase->supplier_Info->email;
                $data["name"] = $purchase->supplier_Info->name;
                $data["view"] = 'mail.order_mail';

                $pdf = PDF::loadView('business.pur_orders.download', $data);

                mailNotificationAttach($data, $pdf->output());
            }

            return response()->json(['status' => true, 'message' => 'Selected Purchase Order Status Updated Successfully!', 'route' => route('business.purchaseorder')]);
        }

        return response()->json(['status' => false, 'message' => 'Order not found']);
    }

    public function download_pdf($ref_no)
    {
        $purchase = PurchaseOrders::where('ref_no', $ref_no)->first();

        if (!$purchase) {
            return abort(404);
        }

        $data = [
            'purchase_order' => $purchase
        ];

        $pdf = PDF::loadView('business.pur_orders.download', $data);
        return $pdf->download('purchase' . date('Ymdhis') . '.pdf');

        // dd($purchase);
    }

    public function order_receive(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'PO_Received');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $purchase = PurchaseOrders::where('ref_no', $ref_no)->first();

        if (!$purchase) {
            return abort(404);
        }

        $request->merge([
            'order_id' => $ref_no,
            'business_id' => $this->business_id
        ]);

        $pur_orders = $this->pur_orders_repo->purchase_order_info($request);

        if ($pur_orders['status'] == false) {
            return abort(404);
        }

        return view('business.pur_orders.received_purchase', [
            'purchase' => $pur_orders['data']
        ]);
    }

    public function approval_histories(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_PurchaseOrder');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $request->merge([
            'order_id' => $ref_no,
            'business_id' => $this->business_id
        ]);

        $pur_orders = $this->pur_orders_repo->purchase_order_info($request);

        if ($pur_orders['status'] == false) {
            return abort(404);
        }

        return view('business.pur_orders.approval_history', [
            'purchase' => $pur_orders['data']
        ]);
    }

    public function approval_history_list(Request $request)
    {
        if ($request->json) {

            $approval_histories = ApprovalHistory::with(['pur_order_Info', 'order_user_info', 'modify_user_info'])->where('order_id', $request->order_id);

            // dd($approval_histories);
            $data =  Datatables::of($approval_histories)
                ->addIndexColumn()
                ->editColumn('invoice_id', function ($item) {
                    return $item->pur_order_Info ?  $item->pur_order_Info->invoice_id  : 'N/A';
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
                        return '<span class="custom-badge badge-borders status-received">Received</span>';
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
}
