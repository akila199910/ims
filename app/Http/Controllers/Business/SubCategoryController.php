<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Repositories\SubcategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class SubCategoryController extends Controller
{
    //
    private $sub_category_repo;
    private $business_id;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->sub_category_repo = new SubcategoryRepository();
    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Sub_Category');

        if ($check_premission == false) {
            return abort(404);
        }

        if ($request->json) {

            $request->merge([
                'business_id' => $this->business_id
            ]);

            // Getting Sub Category list
            $sub_categories = $this->sub_category_repo->sub_category_list($request);

            $data =  Datatables::of($sub_categories)
                ->addIndexColumn()

                ->addColumn('category', function ($item) {
                    return $item->CategoryInfo ?  Str::limit($item->CategoryInfo->name,30)  : 'N/A';
                })
                ->addColumn('image', function ($item) {
                    $url = config('awsurl.url').($item->image);

                    if ($item->image == '' || $item->image == 0) {
                        return '<img src="layout_style/img/subcategory.jpg" border="0" width="50" height="50" style="border-radius:50%;object-fit: cover;" class="stylist-image" align="center" />';
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
                    $edit_route = route('business.sub_category.update.form', $item->ref_no);
                    $view_url = route('business.sub_category.view_details', $item->ref_no);

                    $actions = '';
                    $actions = action_btns($actions, $user, 'Sub_Category', $edit_route, $item->id,'',$view_url);

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                    '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'status','image','category'])
                ->make(true);

            return $data;
        }

        return view('business.sub_category.index');
    }

    public function create_form()
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_Category');

        if ($check_premission == false) {
            return abort(404);
        }

        $categories = Category::Where('status',1)->Where('business_id',$this->business_id)->get();

        return view('business.sub_category.create',[
            'categories'=>$categories
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-zA-Z0-9 ]+$/u|max:190|unique:sub_categories,name,NULL,id,deleted_at,NULL,business_id,'.$this->business_id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'category' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $business_id = $this->business_id;

        $request->merge([
            'business_id' => $business_id,
        ]);
        $data = $this->sub_category_repo->create($request);

        $data['status'] = true;
        $data['message'] = 'New Sub Category Created Successfully!';
        $data['route'] = route('business.sub_category');

        return response()->json($data);
    }

    public function update_form($id)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Update_Sub_Category');

        if ($check_premission == false) {
            return abort(404);
        }

        $sub_categories = SubCategory::where(['ref_no' => $id])->first();

        if (!$sub_categories) {
            return abort(404);
        }

        $categories = Category::Where('status',1)->Where('business_id',$this->business_id)->get();


        return view('business.sub_category.update',[
            'sub_categories' => $sub_categories,
            'categories' => $categories
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-z A-Z]+$/u|max:190|unique:sub_categories,name,'.$id.',id,deleted_at,NULL,business_id,'.$this->business_id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'category' => 'required'

            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);

        $data = $this->sub_category_repo->update($request);

        $data['route'] = route('business.sub_category');

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $data = $this->sub_category_repo->delete($request);

        $data['route'] = route('business.sub_category');

        return response()->json($data);
    }

    public function view_details(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Sub_Category');

        if ($check_premission == false) {
            return abort(404);
        }
        // End

        $sub_categories = SubCategory::with(['CategoryInfo'])->Where(['ref_no' => $ref_no, 'business_id' => $this->business_id])->first();

        if (!$sub_categories) {
            return abort(404);
        }

        return view('business.sub_category.view_details', [
            'sub_categories' =>  $sub_categories
        ]);
    }

}
