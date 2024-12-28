<?php

namespace App\Http\Controllers\Business;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\PaymentTerms;
use Illuminate\Support\Facades\Auth;
use App\Repositories\SupplierRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class SupplierController extends Controller
{
    private $business_id;
    private $supplier_repo;

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->business_id = session()->get('_business_id');
            return $next($request);
        });

        $this->supplier_repo = new SupplierRepository();
    }

    public function index(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Supplier');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        if ($request->json) {

            $request->merge([
                'business_id' => $this->business_id
            ]);

            // Getting supplier list
            $suppliers = $this->supplier_repo->supplier_list($request);
            // dd($suppliers->get());
            $data =  Datatables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('status', function ($item) {
                    if ($item->status == 0) {
                        return '<span class="badge badge-soft-danger badge-border">Inactive</span>';
                    }

                    if ($item->status == 1) {
                        return '<span class="badge badge-soft-success badge-borders">Active</span>';
                    }
                })
                ->addColumn('payement_term', function ($item) {
                    $term = 'N/A';

                    if (isset($item->payment_information->PaymentTermsInfo)) {
                        $term = $item->payment_information->PaymentTermsInfo->payement_term;
                    }
                    return $term;
                })
                ->addColumn('action', function ($item) {
                    $user = Auth::user();
                    $edit_route = route('business.suppliers.update.form', $item->ref_no);
                    $view_url = route('business.suppliers.view_details', $item->ref_no);

                    $actions = '';
                    $actions = action_btns($actions, $user, 'Supplier', $edit_route, $item->id,'', $view_url);

                    $action = '<div class="dropdown dropdown-action">
                        <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-end">'
                        . $actions .
                    '</div></div>';

                    return $action;
                })
                ->rawColumns(['action', 'status','payement_term'])
                ->make(true);

            return $data;
        }

        return view('business.supplier.index');
    }

    public function create_form(Request $request)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Create_Supplier');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $payment_terms = PaymentTerms::all();

        return view('business.supplier.create',[
            'payment_terms' => $payment_terms
        ]);
    }

    public function add_update_contacts(Request $request)
    {
        $id = NULL;
        if (isset($request->contact_id) && !empty($request->contact_id))
        $id = $request->contact_id;

        $validator = Validator::make(
            $request->all(),
            [
                'contact_person_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                'contact_person_contact' => 'required|digits:10|unique:supplier_contacts,contact,' . $id . ',id,deleted_at,NULL,business_id,'.$this->business_id,
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $message = [];
        if (isset($request->person_contacts) && !empty($request->person_contacts)) {
            if (in_array($request->contact_person_contact,$request->person_contacts)) {
                $message['contact_person_contact'] = ['The Sale Ref already has been taken.'];
            }
        }

        if (isset($message) && !empty($message)) {
            return response()->json(['status' => false,  'message' => $message]);
        }

        $data = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_contact' => $request->contact_person_contact
        ];

        if (isset($request->id) && !empty($request->id))
        {
            $request->merge([
                'supplier_id' => $request->id
            ]);

            $data = $this->supplier_repo->add_update_contacts($request);
        }

        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Sale Ref details submitted successfully!'
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'supplier_name' => 'required|regex:/^[a-z A-Z 0-9]+$/u|max:191|unique:suppliers,name,NULL,id,deleted_at,NULL,business_id,'.$this->business_id,
                'supplier_address' => 'required|max:300|regex:/^[a-zA-Z0-9\s,.\-\/\'#]+$/u',
                'email' => 'required|email:rfc,dns|max:191|unique:suppliers,email,NULL,id,deleted_at,NULL,business_id,'.$this->business_id,
                'contact' => 'required|digits:10|unique:suppliers,contact,NULL,id,deleted_at,NULL,business_id,'.$this->business_id,
                // 'account_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                // 'bank_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                // 'branch_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                // 'account_number' => 'required|numeric|unique:supplier_payment_infos,account_number,NULL,id,deleted_at,NULL,business_id,'.$this->business_id,
                'date_of_agree' =>  'required',
                'date_agree_exp' => 'required|after:date_of_agree',
                'person_contact' => 'required',
                'payement_term' => 'required'

            ],
            [
                'person_contact.required' => 'Fill atleast one Sale Ref details'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);


        // Create Supplier
        $data = $this->supplier_repo->create_supplier($request);

        $data['route'] = route('business.suppliers');

        return response()->json($data);
    }

    public function update_form(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Supplier');

        if ($check_premission == false) {
            return abort(404);
        }
        //End

        $request->merge([
            'supplier_id' => $ref_no,
            'business_id' => $this->business_id
        ]);

        $supplier = $this->supplier_repo->supplier_info($request);

        if ($supplier['status'] == false) {
            return abort(404);
        }

        $contacts = [];

        if (count($supplier['data']['contacts'])) {

            foreach ($supplier['data']['contacts'] as $key => $value) {
                $contacts[] = $value['contact'];
            }
        }

        $payment_terms = PaymentTerms::all();

        return view('business.supplier.update',[
            'supplier' => $supplier['data'],
            'contacts' => $contacts,
            'payment_terms' => $payment_terms
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $supplier = Supplier::find($id);
        $payment_id =$supplier->payment_information->id;

        $validator = Validator::make(
            $request->all(),
            [
                'supplier_name' => 'required|regex:/^[a-z A-Z 0-9]+$/u|max:190|unique:suppliers,name,'.$id.',id,deleted_at,NULL,business_id,'.$this->business_id,
                'supplier_address' => 'required|max:300|regex:/^[a-zA-Z0-9\s,.\-\/\'#]+$/u',
                'email' => 'required|email:rfc,dns|max:191|unique:suppliers,email,'.$id.',id,deleted_at,NULL,business_id,'.$this->business_id,
                'contact' => 'required|digits:10|unique:suppliers,contact,'.$id.',id,deleted_at,NULL,business_id,'.$this->business_id,
                // 'account_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                // 'bank_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                // 'branch_name' => 'required|regex:/^[a-z A-Z]+$/u|max:190',
                // 'account_number' => 'required|numeric|unique:supplier_payment_infos,account_number,'.$payment_id.',id,deleted_at,NULL,business_id,'.$this->business_id,
                'date_of_agree' =>  'required',
                'date_agree_exp' => 'required|after:date_of_agree',
                'person_contact' => 'required',
                'payement_term' => 'required'

            ],
            [
                'person_contact.required' => 'Fill atleast one Sale Ref details'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $request->merge([
            'business_id' => $this->business_id
        ]);

        // Create Supplier
        $data = $this->supplier_repo->update_supplier($request);

        $data['route'] = route('business.suppliers');

        return response()->json($data);
    }

    public function view_details(Request $request, $ref_no)
    {
        //Check User Permission
        $user = Auth::user();
        $check_premission = user_permission_check($user, 'Read_Supplier');

        if ($check_premission == false) {
            return abort(404);
        }
        // End

        $supplier = Supplier::with(['payment_information'])->Where(['ref_no' => $ref_no, 'business_id' => $this->business_id])->first();


        if (!$supplier) {
            return abort(404);
        }

        return view('business.supplier.view_details', [
            'supplier' =>  $supplier
        ]);
    }


    public function delete(Request $request)
    {
        $supplier = Supplier::destroy($request->id);

        return response()->json(['status' => true, 'message' => 'Selected Vendor deleted successfully!']);
    }

    public function update_status(Request $request)
    {
        $supplier = Supplier::find($request->vendor_id);

        if ($supplier) {
            $supplier->status = $request->status;
            $supplier->update();
        }

        $route = route('business.suppliers');

        return response()->json(['status' => true, 'message' => 'Selected Vendor Status Updated Successfully!', 'route'=> $route]);
    }
}
