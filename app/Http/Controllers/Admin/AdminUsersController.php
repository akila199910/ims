<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\AdminUserRepository;
use Illuminate\Support\Facades\Validator;

class AdminUsersController extends Controller
{
    //
    private $admin_repo;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            return $next($request);
        });

        $this->admin_repo = new AdminUserRepository();
    }

    public function index(Request $request)
    {
        if ($request->json) {
            $admin_users = User::role('admin')->get();

            $data = DataTables::of($admin_users)
                ->addIndexColumn()
                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="badge badge-soft-danger badge-border">Inactive</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-success badge-border">Active</span>';
                    }
                })
                ->addColumn('action', function ($item) {
                    $editUrl =  route('admin.admin-users.update.form', $item->ref_no);
                    $view_url = route('admin.admin-user.view_details', $item->ref_no);

                    $actions = '';
                    $actions .= action_buttons($actions, $editUrl, $item->id,  $view_url);
                    $action = '<div class="dropdown dropdown-action">
                            <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                    '</div></div>';
                return $action;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);

            return $data;
        }

        return view('admin.admin-users.index');
    }

    public function create_form()
    {
        return view('admin.admin-users.create');
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|regex:/^[a-z 0-9 A-Z]+$/u|max:50',
                'last_name' => 'required|regex:/^[a-z 0-9 A-Z]+$/u|max:50',
                'email' => 'required|max:190|unique:users,email,NULL,id,deleted_at,NULL',
                'contact' => 'required|digits:10|unique:users,contact,NULL,id,deleted_at,NULL',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }
        $request->merge([
            'password' => Str::random(16)
        ]);

        $data = $this->admin_repo->create($request);
        $data['route'] = route('admin.admin-users');
        return response()->json($data);
    }

    public function update_form($id)
    {
        $find_admin = User::where(['ref_no' => $id])->first();

        return view('admin.admin-users.update', [
            'find_admin' => $find_admin
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|regex:/^[a-z 0-9 A-Z]+$/u|max:50',
                'last_name' => 'required|regex:/^[a-z 0-9 A-Z]+$/u|max:50',
                'email' => 'required|max:190|unique:users,email,' . $id . ',id,deleted_at,NULL',
                'contact' => 'required|digits:10|unique:users,contact,' . $id . ',id,deleted_at,NULL',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }
        $data = $this->admin_repo->update($request);

        $data['route'] = route('admin.admin-users');

        return response()->json($data);
    }

    public function view_details(Request $request, $ref_no)
    {

        $admin_users = User::where('ref_no',$ref_no)->first();

        return view('admin.admin-users.view_details', [
            'admin_users' =>  $admin_users
        ]);
    }

    public function delete(Request $request)
    {
        $data = $this->admin_repo->delete($request);

        $data['route'] = route('admin.admin-users');

        return response()->json($data);
    }
}
