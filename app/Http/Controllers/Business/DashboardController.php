<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Models\StockTransfer;
use App\Models\PurchaseOrders;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Repositories\StockTransfersRepository;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    private $business_id;
    private $stock_transfer_repo;


    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->stock_transfer_repo = new StockTransfersRepository();
    }

    public function index()
    {
        // Getting the current date
        $today = now()->toDateString();

        // total Purchase
        $totalPurchases = PurchaseOrders::where('business_id', $this->business_id)->count();

        $totalstock_transfer = StockTransfer::where('business_id', $this->business_id)->count();

        $todayTotalstock_transfer = StockTransfer::where('business_id', $this->business_id)
            ->whereDate('transfer_date', $today)
            ->count();

        // today's total Purchase
        $todayTotalPurchases = PurchaseOrders::where('business_id', $this->business_id)
            ->whereDate('purchased_date', $today)
            ->count();

        // Purchase counts by status
        $pendingCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 0)
            ->count();

        $approvedCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 1)
            ->count();

        $onHoldCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 2)
            ->count();

        $canceledCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 3)
            ->count();

        $fullfilledCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 4)
            ->count();

        $receivedCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 5)
            ->count();

        $closedCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 6)
            ->count();

        $StockTransferCount = StockTransfer::where('business_id', $this->business_id)
            ->count();

        // Today's Purchase counts by status
        $todayPendingCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 0)
            ->whereDate('purchased_date', $today)
            ->count();

        $todayApprovedCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 1)
            ->whereDate('purchased_date', $today)
            ->count();

        $todayonholdCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 2)
            ->whereDate('purchased_date', $today)
            ->count();

        $todaycancelCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 3)
            ->whereDate('purchased_date', $today)
            ->count();

        $todayfullfilledCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 4)
            ->whereDate('purchased_date', $today)
            ->count();

        $todayreceivedCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 5)
            ->whereDate('purchased_date', $today)
            ->count();

        $todayclosedCount = PurchaseOrders::where('business_id', $this->business_id)
            ->where('status', 6)
            ->whereDate('purchased_date', $today)
            ->count();

        $todayStockTransferCount = StockTransfer::where('business_id', $this->business_id)
            ->whereDate('transfer_date', $today )
            ->count();


        return view('business.dashboard', [
            'totalPurchases' => $totalPurchases,
            'todayTotalPurchases' => $todayTotalPurchases,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'onHoldCount' => $onHoldCount,
            'canceledCount' => $canceledCount,
            'fullfilledCount' => $fullfilledCount,
            'receivedCount' => $receivedCount,
            'closedCount' => $closedCount,
            'todayPendingCount' => $todayPendingCount,
            'todayApprovedCount' => $todayApprovedCount,
            'todayonholdCount' => $todayonholdCount,
            'todaycancelCount' => $todaycancelCount,
            'todayfullfilledCount' => $todayfullfilledCount,
            'todayreceivedCount' => $todayreceivedCount,
            'todayclosedCount' => $todayclosedCount,
            'totalstock_transfer' => $totalstock_transfer,
            'todayTotalstock_transfer' => $todayTotalstock_transfer,
            'todayStockTransferCount' => $todayStockTransferCount,
            'StockTransferCount' => $StockTransferCount

        ]);
    }

    public function get_purchase(Request $request)
    {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $current = false;
        $view = 'business.purchase_all_list';

        if (isset($request->day) && !empty($request->day)) {
            if ($request->day == 'current') {
                $current = true;
                $view = 'business.purchase_list';
            }
        }

        return view($view, [
            'current' => $current,
            'status' => $request->status
        ]);
    }

    public function get_stockTransfer(Request $request)
    {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $current = false;
        $view = 'business.stockTransfer_all_list';

        if (isset($request->type) && !empty($request->type)) {
            if ($request->type == 'current') {
                $current = true;
                $view = 'business.stockTransfer';
            }
        }

        return view($view, [
            'current' => $current
        ]);
    }

    public function get_stockTransfer_list(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_StockTransfer');

        if ($check_premission == false) {
            return abort(404);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);

        // Getting Transfer list
        $stock_transfer = $this->stock_transfer_repo->transfer_list($request);

        $data =  Datatables::of($stock_transfer)
            ->addIndexColumn()
            ->addColumn('image', function ($item) {
                $url = $item->product_info ? config('awsurl.url') . ($item->product_info->image) : '';

                if ($item->product_info && $item->product_info->image == '' || $item->product_info->image == 0) {
                    return '<img src="layout_style/img/category.jpg" border="0" width="50" class="stylist-image" align="center" />';
                }
                return '<img src="' . $url . '" border="0" width="50" class="stylist-image" align="center" />';
            })
            ->editColumn('product_name', function ($item) {
                return ($item->product_info) ? Str::limit($item->product_info->name, 30) : 'N/A';
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
                $actions = '';
                $actions = action_btns_model($actions, $user, 'StockTransfer', $edit_route, $item->id, '','');

                $action = '<div class="dropdown dropdown-action">
            <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </a>
                <div class="dropdown-menu dropdown-menu-end">'
                    . $actions .
                    '</div></div>';

                return $action;
            })
            ->rawColumns(['action', 'product_name', 'image', 'warehouse_from', 'warehouse_to', 'created_by', 'edit_by'])
            ->make(true);

        return $data;

    }

    public function get_purchase_list(Request $request)
    {
        $purchase_query = PurchaseOrders::with(['supplier_Info', 'pur_orderItems', 'order_user_info'])->where('business_id', $this->business_id);
        if (isset($request->current) && $request->current == true)
            $purchase_query = $purchase_query->whereDate('purchased_date', date('Y-m-d'));

        if (isset($request->status) && $request->status != '')
            $purchase_query = $purchase_query->where('status', $request->status);

        $purchase = $purchase_query->orderBy('purchased_date', 'DESC');

        $data =  Datatables::of($purchase)
            ->addIndexColumn()
            ->addColumn('status', function ($item) {
                $action = '';
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
                return $action;
            })
            ->addColumn('supplier_name', function ($item) {
                $supplier_name = '';

                if ($item->supplier_Info) {
                    $supplier_name = $item->supplier_Info->name;
                }

                return $supplier_name;
            })
            ->addColumn('supplier_contact', function ($item) {
                $supplier_contact = '';

                if ($item->supplier_Info) {
                    $supplier_contact = $item->supplier_Info->contact;
                }

                return $supplier_contact;
            })
            ->addColumn('action', function ($item) {
                $user = Auth::user();
                $edit_url = '';
                if ($item->status != 2) {
                    $edit_url = route('business.purchaseorder.update.form', $item->ref_no);
                }

                $actions = '';
                $actions .= action_btns($actions, $user, 'PurchaseOrder', $edit_url, $item->id, '','');

                $action = '<div class="dropdown dropdown-action">
                                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </a>
                                    <div class="dropdown-menu dropdown-menu-end">'
                    . $actions .
                    '</div>
                </div>';

                return $action;
            })
            ->rawColumns(['action', 'status', 'supplier_name', 'supplier_contact'])
            ->make(true);

        return $data;
    }

    function purchase_status($item)
    {
        $action = '';
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

        return $action;
    }

    public function graph()
    {
        $business_id = $this->business_id;
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $counts = [
            'pending' => array_fill(0, 31, 0),
            'approved' => array_fill(0, 31, 0),
            'onhold' => array_fill(0, 31, 0),
            'cancelled' => array_fill(0, 31, 0),
            'fullfilled' => array_fill(0, 31, 0),
            'received' => array_fill(0, 31, 0),
            'closed' => array_fill(0, 31, 0)
        ];

        $assetCounts = DB::table('purchase_orders')
            ->select(DB::raw('DAY(purchased_date) as day'), 'status', DB::raw('count(*) as count'))
            ->where('business_id', $business_id)
            ->whereMonth('purchased_date', $currentMonth)
            ->whereYear('purchased_date', $currentYear)
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('DAY(purchased_date)'), 'status')
            ->get();

        $statusMap = [
            0 => 'pending',
            1 => 'approved',
            2 => 'onhold',
            3 => 'cancelled',
            4 => 'fullfilled',
            5 => 'received',
            6 => 'closed'
        ];

        foreach ($assetCounts as $assetCount) {
            $dayIndex = $assetCount->day - 1; // Day index for array (0-based)
            $statusKey = $statusMap[$assetCount->status];

            $counts[$statusKey][$dayIndex] = $assetCount->count;
        }

        return response()->json($counts);
    }
}
