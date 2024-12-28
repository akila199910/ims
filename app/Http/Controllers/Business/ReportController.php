<?php

namespace App\Http\Controllers\Business;

use App\Models\Business;
use App\Models\Products;
use App\Models\Supplier;
use App\Models\Warehouses;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Models\StockTransfer;
use App\Models\PurchaseOrders;
use App\Exports\PurchaseExport;
use App\Models\ProductWarehouse;
use App\Models\PurchasePayements;
use App\Exports\Payment_repExport;
use App\Exports\LowStock_repExport;
use App\Exports\StockTransferExport;
use App\Exports\WriteOff_repExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\ExportRepository;
use App\Repositories\WriteoffRepository;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\StockTransfersRepository;
use App\Repositories\PurchasePaymentRepository;
use Illuminate\Support\Str;


class ReportController extends Controller
{
    //
    private $business_id;
    private $pur_order_repo;
    private $stock_transfer_repo;
    private $payment_repo;
    private $writeoff_repo;


    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->pur_order_repo = new PurchaseOrderRepository();
        $this->stock_transfer_repo = new StockTransfersRepository();
        $this->payment_repo = new PurchasePaymentRepository();
        $this->writeoff_repo = new WriteoffRepository();

    }

    public function purchase_list(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Purchase_Report');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $suppliers = Supplier::where(['business_id' => $this->business_id, 'status' => 1])->orderBy('name', 'ASC')->get();


        if ($request->json) {
            $request->merge([
                'business_id' => $this->business_id
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
                ->addColumn('purchased_date', function ($item) {
                    $purchased_date = 'N/A';
                    if (isset($item->purchased_date) && !empty($item->purchased_date))
                        $purchased_date = $item->purchased_date;

                    return $purchased_date;
                })
                ->addColumn('order_by', function ($item) {
                    $order_by = 'N/A';
                    if (isset($item->order_user_info) && !empty($item->order_user_info))
                        $order_by = Str::limit($item->order_user_info->name,30);

                    return $order_by;
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
                ->rawColumns(['status', 'supplier', 'purchased_date','order_by','modify_by'])
                ->make(true);

            return $data;
        }

        return view('business.reports.purchase',[
            'suppliers' => $suppliers
        ]);
    }

    public function purchase_export(Request $request)
    {
        $request->merge([
            'business_id' => $this->business_id
        ]);


        $data = $this->pur_order_repo->purchase_list($request);

        $data = $data->get();

        $file_name = 'purchase' . date('_YmdHis') . '.xlsx';

        $business = Business::find($this->business_id);

        return Excel::download(new PurchaseExport($data, count($data), $business), $file_name);
    }

    public function stockTransfer_list(Request $request)
    {

        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'StockTransfer_Report');

        if ($check_premission == false) {
            return abort(404);
        }

        $warehouses = Warehouses::where(['business_id' => $this->business_id, 'status' => 1])->orderBy('name', 'ASC')->get();

        $products = Products::where(['business_id' => $this->business_id, 'status' => 1])->orderBy('name', 'ASC')->get();

        if ($request->json) {

            $request->merge([
                'business_id' => $this->business_id
            ]);

            // Getting Transfer list
            $stock_transfer = $this->stock_transfer_repo->transfer_list($request);

            $data =  Datatables::of($stock_transfer)
                ->addIndexColumn()
                ->editColumn('product', function ($item) {
                    return ($item->product_info) ? Str::limit($item->product_info->name,30) : 'N/A';
                })
                ->editColumn('warehouse_from', function ($item) {
                    return ($item->from_warehouse) ? Str::limit($item->from_warehouse->name,30) : 'N/A';
                })
                ->editColumn('warehouse_to', function ($item) {
                    return ($item->to_warehouse) ? Str::limit($item->to_warehouse->name,30) : 'N/A';
                })
                ->addColumn('transfer_date', function ($item) {
                    $transfer_date = 'N/A';
                    if (isset($item->transfer_date) && !empty($item->transfer_date))
                        $transfer_date = $item->transfer_date;

                    return $transfer_date;
                })
                ->editColumn('created_by', function ($item) {
                    return ($item->creator_info) ? Str::limit($item->creator_info->name,30) : 'N/A';
                })
                ->editColumn('edit_by', function ($item) {
                    return ($item->editor_info) ? Str::limit($item->editor_info->name,30) : 'N/A';
                })
                ->addColumn('image', function ($item) {

                    // $url = $item->product_info ? config('awsurl.url').($item->product_info->image) : '';
                    if (!isset($item->product_info) ||  $item->product_info->image == '' || $item->product_info->image == 0) {
                        return '<img src="'.asset('layout_style/img/icons/product_100.png').'"  border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                    }

                    $url = config('awsurl.url').($item->product_info->image);
                    return '<img src="' . $url . '"  border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                })
                ->rawColumns(['image','product', 'warehouse_from', 'warehouse_to','created_by','edit_by','transfer_date'])
                ->make(true);

            return $data;
        }

        return view('business.reports.stockTransfer',[
            'warehouses' => $warehouses,
            'products' => $products
        ]);

    }

    public function stock_transfer_export(Request $request)
    {
        $request->merge([
            'business_id' => $this->business_id
        ]);

        $data = $this->stock_transfer_repo->transfer_list($request);

        $data = $data->get();

        $file_name = 'stockTransfer' . date('_YmdHis') . '.xlsx';

        $business = Business::find($this->business_id);

        return Excel::download(new StockTransferExport($data, count($data), $business), $file_name);
    }


    public function lowStock_rep_list(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'LowStock_Report');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $warehouses = Warehouses::where(['business_id' => $this->business_id, 'status' => 1])->orderBy('name', 'ASC')->get();

        $products = Products::where(['business_id' => $this->business_id, 'status' => 1])->orderBy('name', 'ASC')->get();


        if ($request->json) {

            $product_ids = Products::where('business_id', $this->business_id)->where('status', 1)->pluck('id')->toArray();

            $lowStocks = ProductWarehouse::with(['product_info', 'warehouse_info'])->whereIn('product_id', $product_ids)->whereColumn('qty', '<=', 'qty_alert');

                if (isset($request->warehouse) && !empty($request->warehouse))
                    $lowStocks = $lowStocks->where('warehouse_id',$request->warehouse);

                if (isset($request->product) && !empty($request->product))
                    $lowStocks = $lowStocks->where('product_id',$request->product);

                if (isset($request->select_qty) && !empty($request->select_qty))
                    $lowStocks = $lowStocks->where('qty', '<=', $request->select_qty);

            $data = DataTables::of($lowStocks)
                ->addIndexColumn()

                ->addColumn('product', function ($item) {
                    return $item->product_info ?  Str::limit($item->product_info->name,30)  : 'N/A';
                })
                ->addColumn('warehouse', function ($item) {
                    return $item->warehouse_info ?  Str::limit($item->warehouse_info->name,30)  : 'N/A';
                })
                ->addColumn('image', function ($item) {

                    // $url = $item->product_info ? config('awsurl.url').($item->product_info->image) : '';
                    if (!isset($item->product_info) ||  $item->product_info->image == '' || $item->product_info->image == 0) {
                        return '<img src="'.asset('layout_style/img/icons/product_100.png').'"  border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                    }

                    $url = config('awsurl.url').($item->product_info->image);
                    return '<img src="' . $url . '"  border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                })
                ->rawColumns(['image', 'product', 'warehouse', 'qty'])

                ->make(true);

            return $data;
        }

        return view('business.reports.lowstock',[
            'warehouses' => $warehouses,
            'products' => $products
        ]);
    }

    public function lowStock_rep_export(Request $request)
    {
       $request->merge([
        'business_id' => $this->business_id
       ]);

        $data = (new ExportRepository)->lowStockExport($request);

        $file_name = 'lowStock' . date('_YmdHis') . '.xlsx';

        $business = Business::find($this->business_id);

        return Excel::download(new LowStock_repExport($data, count($data), $business), $file_name);
    }

    public function payment_rep_list(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Payment_Report');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $payment = PurchasePayements::where('purchased_id', $request->purchased_id);

        $payment_type = PaymentType::all();

        $pur_orders = PurchaseOrders::where(['business_id' => $this->business_id])->get();


        if ($request->json) {
            $request->merge([
                'business_id' => $this->business_id
            ]);

            $payment_list = $this->payment_repo->payment_list($request);

            $data =  Datatables::of($payment_list)
                ->addIndexColumn()
                ->addColumn('invoice_id', function ($item) {
                    $invoice_id = 'N/A';
                    if (isset($item->purchase_info) && !empty($item->purchase_info))
                        $invoice_id = $item->purchase_info->invoice_id;

                    return $invoice_id;
                })
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
                ->rawColumns(['status', 'supplier', 'purchased_date','order_by','modify_by','invoice_id'])
                ->make(true);

            return $data;
        }

        return view('business.reports.payment',[
            'payment' => $payment,
            'payment_type' => $payment_type,
            'pur_orders' => $pur_orders
        ]);
    }


    public function payment_rep_export(Request $request)
    {
        $request->merge([
            'business_id' => $this->business_id
        ]);


        $data =  $this->payment_repo->payment_list($request);

        $data = $data->get();

        $file_name = 'payment' . date('_YmdHis') . '.xlsx';

        $business = Business::find($this->business_id);

        return Excel::download(new Payment_repExport($data, count($data), $business), $file_name);
    }


    public function writeoff_rep_list(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Writeoff_Report');

        if ($check_premission == false) {
            return abort(404);
        }
        //End


        $products = Products::where(['business_id' => $this->business_id])->get();

        $warehouses = Warehouses::where(['business_id' => $this->business_id])->get();


        if ($request->json) {
            $request->merge([
                'business_id' => $this->business_id
            ]);

            $writeoff_list = $this->writeoff_repo->writeoff_list($request);

            $data =  Datatables::of($writeoff_list)
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

                ->rawColumns([ 'warehouse','product','qty','retail_price'])
                ->make(true);

            return $data;
        }

        return view('business.reports.writeoff',[
            'products' => $products,
            'warehouses' => $warehouses

        ]);
    }

    public function writeoff_rep_export(Request $request)
    {
        $request->merge([
            'business_id' => $this->business_id
        ]);


        $data =  $this->writeoff_repo->writeoff_list($request);

        $data = $data->get();

        $file_name = 'writeoff' . date('_YmdHis') . '.xlsx';

        $business = Business::find($this->business_id);

        return Excel::download(new WriteOff_repExport($data, count($data), $business), $file_name);
    }
}
