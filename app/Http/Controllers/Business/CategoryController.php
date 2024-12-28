<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    //
    private $category_repo;
    private $business_id;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->category_repo = new CategoryRepository();
    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Category');

        if ($check_premission == false) {
            return abort(404);
        }


        if ($request->json) {

            $request->merge([
                'business_id' => $this->business_id
            ]);

            // Getting Category list
            $categories = $this->category_repo->category_list($request);


            $data =  Datatables::of($categories)
                ->addIndexColumn()

                ->addColumn('image', function ($item) {
                    $url = config('awsurl.url').($item->image);

                    if ($item->image == '' || $item->image == 0) {
                        return '<img src="'.asset('layout_style/img/category.jpg').'" border="0" width="50" height="50"style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                  }
                    return '<img src="' . $url . '" border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
                })
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
                    $edit_route = route('business.category.update.form', $item->ref_no);
                    $view_url = route('business.category.view_details', $item->ref_no);

                    $actions = '';
                    $actions = action_btns($actions, $user, 'Category', $edit_route, $item->id,'',$view_url);

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                    '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'status','image'])
                ->make(true);

            return $data;
        }

        return view('business.category.index');
    }

    public function create_form()
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_Category');

        if ($check_premission == false) {
            return abort(404);
        }

        return view('business.category.create');
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-z 0-9 A-Z]+$/u|max:190|unique:categories,name,NULL,id,deleted_at,NULL,business_id,'.$this->business_id,
                'image'=>'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'

            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $business_id = $this->business_id;

        $request->merge([
            'business_id' => $business_id,
        ]);
        $data = $this->category_repo->create($request);

        $data['status'] = true;
        $data['message'] = 'New Category Created Successfully!';
        $data['route'] = route('business.category');

        return response()->json($data);
    }

    public function update_form($id)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_Category');

        if ($check_premission == false) {
            return abort(404);
        }

        $categories = Category::where(['ref_no' => $id])->first();

        if (!$categories) {
            return abort(404);
        }

        return view('business.category.update',[
            'categories' => $categories
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-z A-Z]+$/u|max:190|unique:categories,name,'.$id.',id,deleted_at,NULL,business_id,'.$this->business_id,
                'image'=>'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'

            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);

        $data = $this->category_repo->update($request);

        $data['route'] = route('business.category');

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $data = $this->category_repo->delete($request);

        $data['route'] = route('business.category');

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

        $categories = Category::Where(['ref_no' => $ref_no, 'business_id' => $this->business_id])->first();

        if (!$categories) {
            return abort(404);
        }

        return view('business.category.view_details', [
            'categories' =>  $categories
        ]);
    }
}
