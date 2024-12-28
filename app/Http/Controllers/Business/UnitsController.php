<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Units;
use App\Repositories\UnitRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class UnitsController extends Controller
{
    //
    private $unit_repo;
    private $business_id;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->unit_repo = new UnitRepository();
    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Unit');

        if ($check_premission == false) {
            return abort(404);
        }


        if ($request->json) {

            $request->merge([
                'business_id' => $this->business_id
            ]);

            // Getting Unit list
            $units = $this->unit_repo->unit_list($request);

            $units = Units::where('business_id', $this->business_id)->get();
            $data =  Datatables::of($units)
                ->addIndexColumn()
                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="badge badge-soft-danger badge-border">Inactive</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-success badge-borders">Active</span>';
                    }
                })

                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = route('business.units.update.form', $item->ref_no);
                    $view_url = route('business.units.view_details', $item->ref_no);

                    $actions = '';
                    $actions = action_btns($actions, $user, 'Unit', $edit_route, $item->id,'', $view_url);

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

        return view('business.units.index');
    }

    public function create_form()
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_Unit');

        if ($check_premission == false) {
            return abort(404);
        }

        return view('business.units.create');
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-z 0-9 A-Z.]+$/u|max:190|unique:units,name,NULL,id,deleted_at,NULL,business_id,'.$this->business_id,

            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $business_id = $this->business_id;

        $request->merge([
            'business_id' => $business_id,
        ]);
        $data = $this->unit_repo->create($request);

        $data['status'] = true;
        $data['message'] = 'New Unit Created Successfully!';
        $data['route'] = route('business.units');

        return response()->json($data);
    }

    public function update_form($id)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_Unit');

        if ($check_premission == false) {
            return abort(404);
        }

        $units = Units::where(['ref_no' => $id])->first();

        if (!$units) {
            return abort(404);
        }

        return view('business.units.update',[
            'units' => $units
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-z 0-9 A-Z.]+$/u|max:190|unique:suppliers,name,'.$id.',id,deleted_at,NULL,business_id,'.$this->business_id,

            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);

        $data = $this->unit_repo->update($request);

        $data['route'] = route('business.units');

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $data = $this->unit_repo->delete($request);

        $data['route'] = route('business.units');

        return response()->json($data);
    }

    public function view_details(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Category');

        if ($check_premission == false) {
            return abort(404);
        }
        // End

        $units =  Units::Where(['ref_no' => $ref_no, 'business_id' => $this->business_id])->first();

        if (!$units) {
            return abort(404);
        }

        return view('business.units.view_details', [
            'units' =>  $units
        ]);
    }
}
