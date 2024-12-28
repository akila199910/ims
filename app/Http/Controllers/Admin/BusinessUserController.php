<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BusinessUsers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Repositories\BusinessUserRepository;

class BusinessUserController extends Controller
{
    //
    private $business_id;
    private $businessuser_repo;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->businessuser_repo = new BusinessUserRepository();
    }
    public function index(Request $request)
    {
        if ($request->json) {
            $business_user = User::role('business_user')->get();

            $data = DataTables::of($business_user)
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

                    $editUrl =  route('admin.business-users.update.form', $item->ref_no);
                    $view_url = route('admin.business-users.view_details', $item->ref_no);

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

        return view('admin.business-users.index');
    }

    public function create_form()
    {
        $companies = Business::Where('status',1)->get();
        return view('admin.business-users.create',['companies'=>$companies]);
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
                'companies' => 'required|array',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }
            $request->merge([
                'password' => Str::random(16)
            ]);
        $data = $this->businessuser_repo->create($request);
        $data['route'] = route('admin.business-users');

        return response()->json($data);
    }

    public function update_form($id)
    {
        $find_business_user = User::where(['ref_no' => $id])->first();

        $business_user_companies = BusinessUsers::Where('user_id',$find_business_user->id)
        ->pluck('business_id')
        ->toArray();
;

        $all_business = Business::Where('status', 1)->get();

        return view('admin.business-users.update', [
            'find_business_user' => $find_business_user,
            'business_user_companies'=>$business_user_companies,
            'all_business'=>$all_business
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
                'companies' => 'required|array',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }


        $data = $this->businessuser_repo->update($request);

        $data['route'] = route('admin.business-users');

        return response()->json($data);
    }


    public function view_details(Request $request, $ref_no)
    {

        $user = User::where('ref_no',$ref_no)->first();

        return view('admin.business-users.view_details', [
            'user' =>  $user
        ]);
    }

    public function delete(Request $request)
    {
        $data = $this->businessuser_repo->delete($request);

        $data['route'] = route('admin.admin-users');

        return response()->json($data);

    }

}
