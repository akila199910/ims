<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\PurchaseOrderItem;
use App\Http\Controllers\Controller;
use App\Models\ApprovalHistory;
use App\Models\payement_type;
use App\Models\PaymentType;
use App\Models\Products;
use App\Models\PurchaseOrders;
use App\Models\PurchasePayements;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PurchaseOrderRepository;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    private $business_id;
    private $pur_repo;
    private $pur_order_repo;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->pur_order_repo = new PurchaseOrderRepository();
    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Purchase');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        if ($request->json) {
            $request->merge([
                'business_id' => $this->business_id,
                'statuses' => [6]
            ]);

            $pur_orders = $this->pur_order_repo->purchase_list($request);

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
                    $view_url = route('business.purchase.view_details', $item->ref_no);
                    $pur_url = route('business.purchases.payments', $item->ref_no);
                    $actions = '';
                    $actions = action_btns_pur($actions, $user, 'Sub_Category', $view_url, $item->id, $pur_url);


                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                        '</div></div>';

                    return $action;

                    return $actions;
                })
                ->rawColumns(['action', 'status', 'supplier', 'order_by', 'modify_by', 'created_at'])
                ->make(true);

            return $data;
        }

        return view('business.purchase.index');
    }

    public function view_details(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Purchase');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $request->merge([
            'order_id' => $ref_no,
            'business_id' => $this->business_id
        ]);

        $pur_orders = $this->pur_order_repo->purchase_order_info($request);

        if ($pur_orders['status'] == false) {
            return abort(404);
        }
        // dd($pur_orders['data']);
        return view('business.purchase.view_details', [
            'purchase' => $pur_orders['data']
        ]);
    }

    public function item_list(Request $request)
    {
        $purchase_items = PurchaseOrderItem::with(['product_info'])->where('purchased_id', $request->order_id);

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

    public function re_order(Request $request)
    {
        $purcahse = PurchaseOrders::find($request->id);

        if ($purcahse) {
            $product_id = Products::where('business_id', $this->business_id)->where('status', 1)->pluck('id')->toArray();
            $pur_orderItems = $purcahse->pur_orderItems->whereIn('product_id', $product_id)->toArray();

            $product_ids = [];
            $request_qtys = [];
            $retail_prices = [];

            foreach (array_chunk($pur_orderItems, 1000) as $item_chunk) {
                foreach ($item_chunk as $key => $order_item) {
                    $product_ids[] = $order_item['product_id'];
                    $request_qtys[] = $order_item['qty'];
                    $retail_prices[] = $order_item['unit_price'];
                }
            }

            $request->merge([
                'product_ids' => $product_ids,
                'request_qtys' => $request_qtys,
                'retail_prices' => $retail_prices,
                'supplier' => $purcahse->supplier_id,
                'business_id' => $purcahse->business_id,
                'order_by' => Auth::user()->id,
                'modify_by' => Auth::user()->id,
                'purchased_date' => date('Y-m-d')
            ]);

            $data = $this->pur_order_repo->create($request);
        }

        $data['route'] = route('business.purchaseorder');

        return response()->json($data);
    }

    public function payments(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Payement');

        if ($check_premission == false) {
            return abort(404);
        }

        $id = $request->id;

        $purchase_pays = PaymentType::all();
        $purchase_order = PurchaseOrders::find($id);

        $purchase_order = PurchaseOrders::where(['ref_no' => $id])->first();


        if (!$purchase_order) {
            return abort(404);
        }

        return view('business.purchase.payement', [
            'purchase_order' => $purchase_order,
            'purchase_pays' => $purchase_pays,

        ]);
    }

    public function get_payments(Request $request)
    {
        $id = $request->order_id;

        if ($request->json) {

            $request->merge([
                'business_id' => $this->business_id
            ]);

            $purchase_pays = PurchasePayements::with(['PayementMethodInfo'])->where('purchased_id', $id)->orderBy('id', 'DESC');
            // dd($purchase_pays->get());
            $data =  Datatables::of($purchase_pays)
                ->addIndexColumn()
                // ->addColumn('payment_type', function ($item) {

                //     $payment_type = ucwords($item->PayementMethodInfo->payment_type);

                //     if ($item->payment_type == 3 && !empty($item->SupplierPayement_info->account_number)) {
                //         $account_number = $item->SupplierPayement_info->account_number;
                //         return $payment_type . ' (Account No: ' . $account_number . ')';
                //     }

                //     return $payment_type;
                // })

                ->addColumn('payment_type', function ($item) {
                    return $item->PayementMethodInfo ?  $item->PayementMethodInfo->payment_type  : 'N/A';
                })
                ->addColumn('payment_date', function ($item) {
                    return date('Y-m-d', strtotime($item->payment_date));
                })
                ->addColumn('paid_amount', function ($item) {
                    return ucwords($item->paid_amount);
                })
                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = route('business.purchases.update.form', $item->ref_no);
                    $actions = '';
                    $actions = action_btns_model($actions, $user, 'Payement', $edit_route, $item->id, '','');

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                        '</div></div>';

                    return $action;
                })
                ->rawColumns(['payment_type', 'action', 'payment_date', 'paid_amount'])
                ->make(true);

            return $data;
        }
    }


    public function delete(Request $request)
    {
        $data = $this->pur_order_repo->delete($request);

        $data['route'] = route('business.purchaseorder');

        return response()->json($data);
    }

    public function store_payments(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'payment_type' => 'required',
                'payment_date' => 'required|date|after:purchased_date',
                'paid_amount' => 'required|numeric|between:0,9999999999.99',
                'payment_reference' => 'nullable|max:191',
            ],
            [
                'payment_date.date' => 'Invalid date format.',
                'payment_date.before_or_equal' => 'The payment date must be before or equal to the purchased date.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $this->pur_order_repo->store_payments($request);

        $data['status'] = true;
        $data['message'] = 'New Payment added successfully!';
        $data['route'] = route('business.purchases');

        return response()->json($data);
    }

    public function update_form(Request $request)
    {
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_Payement');

        if ($check_premission == false) {
            return abort(404);
        }

        $purchase_pay = PurchasePayements::find($request->id);

        $purchase_pays = PaymentType::all();

        $purchase_order = PurchaseOrders::find($purchase_pay->purchased_id);

        return view('business.purchase.update', [
            'purchase_pay' => $purchase_pay,
            'purchase_pays' =>  $purchase_pays,
            'purchase_order' => $purchase_order
        ]);
    }

    public function update(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'payment_type' => 'required',
                'payment_date' => 'required|date|after:purchased_date',
                'up_paid_amount' => 'required|numeric|between:0,9999999999.99',
                'payment_reference' => 'nullable|max:191',
            ],
            [
                'payment_date.date' => 'Invalid date format.',
                'payment_date.before_or_equal' => 'The payment date must be before or equal to the purchased date.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);

        $data = $this->pur_order_repo->update_payment($request);


        $data['route'] = route('business.purchases');

        return response()->json($data);
    }

    public function deletePayement(Request $request)
    {
        $this->pur_order_repo->delete_payement($request);

        return response()->json(['status' => true,  'message' => 'Selected Purchase Payement Deleted Successfully!']);
    }

    // approval_histories
    public function approval_histories(Request $request, $id)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Approval_History');

        if ($check_premission == false) {
            return abort(404);
        }

        if ($request->json) {
            $request->merge([
                'business_id' => $this->business_id,
                'statuses' => [6]
            ]);

            $approval_histories = ApprovalHistory::with(['pur_order_Info', 'order_user_info', 'modify_user_info'])->get();

            // dd($approval_histories);
            $data =  Datatables::of($approval_histories)
                ->addIndexColumn()
                ->addColumn('invoice_id', function ($item) {
                    return $item->pur_order_Info ?  $item->pur_order_Info->invoice_id  : 'N/A';
                })
                ->addColumn('modify_by', function ($item) {
                    return $item->modify_user_info ?  Str::limit($item->modify_user_info->name ,30) : 'N/A';
                })
                ->addColumn('created_at', function ($item) {
                    return date('Y-m-d H:i:s', strtotime($item->created_at));
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
                ->rawColumns(['status', 'invoice_id', 'modify_by', 'created_at'])
                ->make(true);

            return $data;
        }

        return view('business.purchase.approval_his');
    }
}
