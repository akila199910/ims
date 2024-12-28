<?php

namespace App\Http\Controllers\Business;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\PurchaseOrders;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\PurchasePayements;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\PurchasePaymentRepository;

class PurchasePaymentController extends Controller
{
    private $business_id;
    private $pur_orders_repo;
    private $payment_repo;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->pur_orders_repo = new PurchaseOrderRepository();
        $this->payment_repo = new PurchasePaymentRepository();
    }

    public function index(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Payement');

        if ($check_premission == false) {
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

        return view('business.pur_payments.index', [
            'purchase' => $pur_orders['data']
        ]);
    }

    public function payments_list(Request $request)
    {
        $payment_list = $this->payment_repo->payment_list($request);

        $data =  Datatables::of($payment_list)
            ->addIndexColumn()
            ->editColumn('payment_type', function ($item) {
                $payment_type = 'N/A';
                if (isset($item->payment_type_info) && !empty($item->payment_type_info))
                    $payment_type = $item->payment_type_info->payment_type;

                return $payment_type;
            })
            ->addColumn('scan_doc', function ($item) {
                $scan_doc = 'N/A';
                if ($item->scan_doc != '' && $item->scan_doc == 0)
                    $scan_doc = '<a href="'.config('aws_url.url').$item->scan_doc.'" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-solid fas fa-download"></i></a>';

                return $scan_doc;
            })
            ->addColumn('action', function ($item) {
                $status= $item->purchase_info->status;
                $user = Auth::user();
                $edit_route = '';

                if($status == 3) $edit_route = '';

                $view_route = '';
                $actions = '';
                $actions = action_btns_model($actions, $user, 'Payement', $edit_route, $item->id, $view_route,'');

                if (!empty($actions)) {
                    return '<div class="dropdown dropdown-action">
                                <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">' . $actions . '</div>
                            </div>';
                }

                return '';


            })
            ->rawColumns(['action', 'payment_type', 'scan_doc'])
            ->make(true);

        return $data;
    }

    public function load_paid_due(Request $request)
    {
        $pur_order = PurchaseOrders::find($request->purchase_id);

        $paid_amount = $pur_order->payment_list()->sum('paid_amount');

        $due_amount =  $pur_order->final_amount - $paid_amount;

        $due_amount = $due_amount > 0 ? $due_amount : 0;

        return response()->json([
            'paid_amount' => number_format($paid_amount,2,'.',''),
            'due_amount' => number_format($due_amount,2,'.','')
        ]);

    }

    public function create_form(Request $request)
    {
        $purchase = PurchaseOrders::find($request->purchased_id);
        $pay_types = PaymentType::all();

        $view = 'business.pur_payments.create';
        if (isset($request->view) && !empty($request->view)) {
            $view = 'business.pur_orders.create_payment';
        }

        return view($view,[
            'purchase' => $purchase,
            'pay_types' => $pay_types
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'payment_type' => 'required',
                'paid_date' => 'required|date|after_or_equal:purchased_date',
                'amount' => 'required|numeric|between:1,'.$request->due_amount,
                'payment_reference' => 'nullable|max:190|unique:purchase_payements,payment_reference',
            ],
            [
                'paid_date.date' => 'Invalid date format.',
                'paid_date.after_or_equal' => 'The payment date must be after or equal to the purchased date.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $data = $this->payment_repo->create_payment($request);

        return response()->json($data);
    }

    public function update_form(Request $request)
    {
        $payment = PurchasePayements::find($request->id);

        if ($payment) {
            $purchase = $payment->purchase_info;

            if ($purchase) {
                $paid_amount = $payment->paid_amount;
                $due_amount = $purchase->due_amount;
                $due_amount = $due_amount + $paid_amount;
                $pay_types = PaymentType::all();

                return view('business.pur_payments.update',[
                    'payment' => $payment,
                    'purchase' => $purchase,
                    'due_amount' => $due_amount,
                    'payment' =>$payment,
                    'pay_types' => $pay_types
                ]);
            }

            $route = route('business.purchaseorder');
            return response()->json(['status'=>false, 'message'=>'Payment details not found', 'route'=> $route]);
        }

        $route = route('business.purchaseorder');
        return response()->json(['status'=>false, 'message'=>'Payment details not found', 'route'=> $route]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'payment_type' => 'required',
                'paid_date' => 'required|date|after_or_equal:purchased_date',
                'amount' => 'required|numeric|between:1,'.$request->due_amount,
                'payment_reference' => 'nullable|max:190|unique:purchase_payements,payment_reference',
            ],
            [
                'paid_date.date' => 'Invalid date format.',
                'paid_date.after_or_equal' => 'The payment date must be after or equal to the purchased date.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $data = $this->payment_repo->update_payment($request);

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $data = $this->payment_repo->delete_payment($request);

        return response()->json($data);
    }
}
