<?php

namespace App\Http\Controllers\Business;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\UserBusiness;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserManageRepository;


class UserManageController extends Controller
{
    //
    private $business_id;
    private $user_repo;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->user_repo = new UserManageRepository();
    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Users');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        if (isset($request->json)) {

            $user_id = UserBusiness::where('business_id', $this->business_id)->pluck('user_id')->toArray();

            $users = User::role('user')->whereIn('id', $user_id);

            $data =  Datatables::of($users)
                ->addIndexColumn()
                ->addColumn('profile', function ($item) {
                    $url = config('aws_url.url') . ($item->UserProfile->profile);

                    if ($item->UserProfile->profile == '') {
                        $url = "/admin_staff/assets/img/profile.jpg";
                    }
                    return '<img src="' . $url . '" border="0" width="50" height="50" style="border-radius:50%" class="stylist-image" align="center" />';
                })
                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="badge badge-soft-danger badge-border">Inactive</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-success badge-border">Active</span>';
                    }
                })

                ->addColumn('permissions', function ($item) {
                    $permissions = $item->getDirectPermissions()->pluck('name')->toArray();

                    $data = '';
                    foreach ($permissions as $perm) {
                        $name = explode('_', $perm);

                        $data .= '<a class="dropdown-item" style="cursor: none;" href="javascript:;">' . implode(' ', $name) . '</a>';
                    }

                    return '<div class="dropdown action-label scrollbar">
                                <a class="custom-badge status-purple dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false" >
                                    View Permissions
                                </a>
                                <div class="dropdown-menu dropdown-menu-end status-staff" style="max-height: 200px; position: relative;overflow: hidden;width: 100%;overflow-y: scroll;">
                                    ' . $data . '
                                </div>
                            </div>';
                })
                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_url = route('business.users.update.form', $item->ref_no);
                    $view_url = route('business.users.view_details', $item->ref_no);

                    $actions = '';
                    $actions .= action_btns($actions, $user, 'Users', $edit_url, $item->id, '', $view_url);

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                        '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'status', 'profile', 'permissions'])
                ->make(true);

            return $data;
        }

        return view('business.users.index');
    }

    public function create_form(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_Users');

        if ($check_premission == false) {
            return abort(404);
        }
        //END

        $action = ['Read', 'Create', 'Update', 'Delete'];
        $permissions = [
            'Product',
            'Category',
            'Sub_Category',
            'Unit',
            'Supplier',
            'Warehouse',
            'PurchaseOrder',
            'StockTransfer',
            'StockAdjustment',
            'Payement',
            'Users',
            'PurchaseReturn',
            'WriteOff'
        ];

        $permission_list = [];

        foreach ($permissions as $perm) {
            $permission_list[$perm] = []; // Initialize an empty array for each permission
            foreach ($action as $act) {
                $permission_list[$perm][] = $act . '_' . $perm;
            }
        }

        $purchase_status = ['PO_Approval', 'PO_Cancel', 'PO_Hold', 'PO_Fullfillment', 'PO_Received', 'PO_Closed', 'Read_Approval_History'];

        // Add the $purchase_status array to 'PurchaseOrder'
        if (isset($permission_list['PurchaseOrder'])) {
            foreach ($purchase_status as $status) {
                $permission_list['PurchaseOrder'][] = $status;
            }
        }

        $purchase_return_status = ['PO_Return_Cancel', 'PO_Return_Approval', 'PO_Return_Hold', 'PO_Return_Fullfillment', 'PO_Return_Received', 'PO_Return_Closed', 'Read_Return_Approval_History'];

        // Add the $purchase_return_status array to 'PurchaseReturn'
        if (isset($permission_list['PurchaseReturn'])) {
            foreach ($purchase_return_status as $status) {
                $permission_list['PurchaseReturn'][] = $status;
            }
        }

        // $purchase_list = ['Read_Purchase'];
        $LowStock = ['Read_LowStock'];
        $report = ['Purchase_Report', 'StockTransfer_Report', 'LowStock_Report', 'Payment_Report', 'Writeoff_Report'];

        // $permission_list['Purchase'] = $purchase_list;
        $permission_list['LowStock'] = $LowStock;
        $permission_list['Report'] = $report;

        return view('business.users.create', [
            // 'action' => $action,
            'permissions' => $permissions,
            'permission_list' => $permission_list
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                'last_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                'email' => 'required|email:rfc,dns|max:190|unique:users,email,NULL,id,deleted_at,NULL',
                'contact' => 'required|digits:10|unique:users,contact,NULL,id,deleted_at,NULL',
                'permissions' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $business_id = $this->business_id;

        $request->merge([
            'business_id' => $business_id,
            'password' => Str::random(16)
        ]);

        $data = $this->user_repo->create_users($request, 'user');

        $data['status'] = true;
        $data['message'] = 'New User Created Successfully!';
        $data['route'] = route('business.users');

        return response()->json($data);
    }

    public function update_form(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_Users');

        if ($check_premission == false) {
            return abort(404);
        }
        //END

        $user = User::where('ref_no', $ref_no)->first();

        if (!$user) {
            return abort(404);
        }

        //check user exist with current location
        $user_business = UserBusiness::where(['user_id' => $user->id, 'business_id' => $this->business_id])->first();

        if (!$user_business) {
            return abort(404);
        }

        $action = ['Read', 'Create', 'Update', 'Delete'];
        $permissions = [
            'Product',
            'Category',
            'Sub_Category',
            'Unit',
            'Supplier',
            'Warehouse',
            'PurchaseOrder',
            'StockTransfer',
            'StockAdjustment',
            'Payement',
            'Users',
            'PurchaseReturn',
            'WriteOff'
        ];

        $permission_list = [];

        foreach ($permissions as $perm) {
            $permission_list[$perm] = []; // Initialize an empty array for each permission
            foreach ($action as $act) {
                $permission_list[$perm][] = $act . '_' . $perm;
            }
        }

        $purchase_status = ['PO_Approval', 'PO_Cancel', 'PO_Hold', 'PO_Fullfillment', 'PO_Received', 'PO_Closed', 'Read_Approval_History'];

        // Add the $purchase_status array to 'PurchaseOrder'
        if (isset($permission_list['PurchaseOrder'])) {
            foreach ($purchase_status as $status) {
                $permission_list['PurchaseOrder'][] = $status;
            }
        }

        $purchase_return_status = ['PO_Return_Cancel', 'PO_Return_Approval', 'PO_Return_Hold', 'PO_Return_Fullfillment', 'PO_Return_Received', 'PO_Return_Closed', 'Read_Return_Approval_History'];

        // Add the $purchase_return_status array to 'PurchaseReturn'
        if (isset($permission_list['PurchaseReturn'])) {
            foreach ($purchase_return_status as $status) {
                $permission_list['PurchaseReturn'][] = $status;
            }
        }

        // $purchase_list = ['Read_Purchase'];
        $LowStock = ['Read_LowStock'];
        $report = ['Purchase_Report', 'StockTransfer_Report', 'LowStock_Report', 'Payment_Report', 'Writeoff_Report'];

        // $permission_list['Purchase'] = $purchase_list;
        $permission_list['LowStock'] = $LowStock;
        $permission_list['Report'] = $report;

        $user_permission = $user->getDirectPermissions()->pluck('name')->toArray();

        return view('business.users.update', [
            // 'action' => $action,
            'permissions' => $permissions,
            'permission_list' => $permission_list,
            'user_permission' => $user_permission,
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                'last_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                'email' => 'required|email:rfc,dns|max:190|unique:users,email,' . $id . ',id,deleted_at,NULL',
                'contact' => 'required|digits:10|unique:users,contact,' . $id . ',id,deleted_at,NULL',
                'permissions' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $data = $this->user_repo->update_users($request);

        $data['status'] = true;
        $data['message'] = 'Selected User Updated Successfully!';
        $data['route'] = route('business.users');

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $data = $this->user_repo->delete_user($request);

        $data['route'] = route('business.users');

        return response()->json($data);
    }

    public function view_details(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Users');

        if ($check_premission == false) {
            return abort(404);
        }

        $user_id = UserBusiness::where('business_id', $this->business_id)->pluck('user_id')->toArray();

        $users = User::role('user')->whereIn('id', $user_id)->where('ref_no', $ref_no)->first();

        if (!$users) {
            return abort(404);
        }

        $user_permission = $users->getDirectPermissions()->pluck('name')->toArray();

        return view('business.users.view_details', [
            'users' =>  $users,
            'user_permission' => $user_permission
        ]);
    }
}
