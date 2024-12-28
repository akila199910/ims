<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\SupplierPaymentInfo;

class SupplierRepository
{
    public function supplier_list($request)
    {
        $suppliers = Supplier::with(['payment_information'])->where('business_id', $request->business_id);

        return $suppliers;
    }

    public function add_update_contacts($request)
    {
        $supplier_contacts = SupplierContact::updateOrCreate(
            [
                'id' => $request->contact_id,
                'supplier_id' => $request->supplier_id
            ],
            [
                'name' => $request->contact_person_name,
                'contact' => $request->contact_person_contact,
                'status' => $request->status == true ? 1 : 0
            ]
        );

        $request->merge([
            'contact_id' => $supplier_contacts->id
        ]);

        $data = $this->contact_person_info($request);

        return $data;
    }

    public function contact_person_info($request)
    {
        $supplier_contacts = SupplierContact::find($request->contact_id);

        $data = [];

        if ($supplier_contacts) {
            $data = [
                'id' => $supplier_contacts->id,
                'supplier_id' => $supplier_contacts->supplier_id,
                'contact_person_name' => $supplier_contacts->name,
                'contact_person_contact' => $supplier_contacts->contact,
                'status' => $supplier_contacts->status,
                'status_name' => $supplier_contacts->status == 1 ? 'Active' : 'Inactive'
            ];
        }

        return $data;
    }

    public function create_supplier($request)
    {
        $file = '';
        if (isset($request->agreement_document) && $request->agreement_document->getClientOriginalName()) {
            $file = file_upload($request->agreement_document, 'supplier_doc');
        }

        // Create supplier
        $supplier = new Supplier();
        $supplier->business_id = $request->business_id;
        $supplier->supplier_id = $request->supplier_id;
        $supplier->name = $request->supplier_name;
        $supplier->address = $request->supplier_address;
        $supplier->email = $request->email;
        $supplier->date_of_agree = $request->date_of_agree;
        $supplier->date_agree_exp = $request->date_agree_exp;
        $supplier->contact = $request->contact;
        $supplier->status = 1;
        $supplier->agree_doc = $file;
        $supplier->save();


        $supplier_id = $supplier->id;
        $formatted_supplier_id = auto_increment_id($supplier_id);
        $supplier->supplier_id = $formatted_supplier_id;
        $supplier->update();

        // Generate Reference Number for supplier
        $ref_no = refno_generate(16, 2, $supplier->id);
        $supplier->ref_no = $ref_no;
        $supplier->update();

        // Create Supplier Payment Information
        $supplier_pay = new SupplierPaymentInfo();
        $supplier_pay->business_id = $request->business_id;
        $supplier_pay->supplier_id = $supplier->id;
        $supplier_pay->account_name = isset($request->account_name) && !empty($request->account_name) ? $request->account_name : 'NA';
        $supplier_pay->bank_name =  isset($request->bank_name) && !empty($request->bank_name) ? $request->bank_name : 'NA';
        $supplier_pay->branch_name = isset($request->branch_name) && !empty($request->branch_name) ? $request->branch_name : 'NA';
        $supplier_pay->account_number = isset($request->account_number) && !empty($request->account_number) ? $request->account_number : 'NA';
        $supplier_pay->payement_term = $request->payement_term;
        $supplier_pay->save();

        // Generate Reference Number for supplier payment
        $ref_no = refno_generate(16, 2, $supplier_pay->id);
        $supplier_pay->ref_no = $ref_no;
        $supplier_pay->update();

        if ((isset($request->person_name) && !empty($request->person_name)) && (isset($request->person_contact) && !empty($request->person_contact))) {

            $person_name = $request->person_name;
            $person_contact = $request->person_contact;

            foreach ($person_name as $key => $value) {
                $supplier_contact = new SupplierContact();
                $supplier_contact->business_id = $request->business_id;
                $supplier_contact->supplier_id = $supplier->id;
                $supplier_contact->name = $person_name[$key];
                $supplier_contact->contact = $person_contact[$key];
                $supplier_contact->status = 1;
                $supplier_contact->save();

                // Generate Reference Number for supplier contacts
                $ref_no = refno_generate(16, 2, $supplier_contact->id);
                $supplier_contact->ref_no = $ref_no;
                $supplier_contact->update();
            }
        }

        return $data = [
            'status' => true,
            'message' => 'New Vendor created successfully!'
        ];
    }

    public function supplier_info($request)
    {
        $status = false;
        $data = [];

        $supplier = Supplier::with(['payment_information','supplier_contacts'])->where('ref_no', $request->supplier_id)->where('business_id', $request->business_id)->first();

        if ($supplier) {
            $status = true;

            $data['supplier_info'] = [
                'id' => $supplier->id,
                'supplier_id' => $supplier->ref_no,
                'supplier_code' => $supplier->supplier_id,
                'business_id' => $supplier->business_id,
                'name' => $supplier->name,
                'email' => $supplier->email,
                'contact' => $supplier->contact,
                'address' => $supplier->address,
                'status' => $supplier->status,
                'status_name' => $supplier->status == 1 ? 'Active' : 'Inactive',
                'agreement_doc' => config('aws_url.url').$supplier->agree_doc,
                'date_of_agree' => $supplier->date_of_agree,
                'date_agree_exp' => $supplier->date_agree_exp
            ];

            $data['payment_information'] = [
                'id' => $supplier->payment_information->id,
                'payment_id' => $supplier->payment_information->ref_no,
                'account_name' => $supplier->payment_information->account_name,
                'bank_name' => $supplier->payment_information->bank_name,
                'branch_name' => $supplier->payment_information->branch_name,
                'account_number' => $supplier->payment_information->account_number,
                'payement_term' => $supplier->payment_information->payement_term
            ];

            $contacts = [];

            foreach ($supplier->supplier_contacts as $contact) {
                $contacts[] = [
                    'id' => $contact->id,
                    'contact_id' => $contact->ref_no,
                    'name' => $contact->name,
                    'contact' => $contact->contact,
                    'status' => $contact->status,
                    'status_name' => $contact->status == 1 ? 'Active' : 'Inactive'
                ];
            }

            $data['contacts'] = $contacts;
        }

        return [
            'status' => $status,
            'data' => $data
        ];
    }

    public function update_supplier($request)
    {
        $supplier = Supplier::find($request->id);

        $file = '';
        if (isset($request->agreement_document) && $request->agreement_document->getClientOriginalName()) {
            $file = file_upload($request->agreement_document, 'supplier_doc');
        }
        else
        {
            if (!$supplier->agree_doc)
                $file = '';
            else
                $file = $supplier->agree_doc;
        }

        // Update supplier
        $supplier->name = $request->supplier_name;
        $supplier->address = $request->supplier_address;
        $supplier->email = $request->email;
        $supplier->contact = $request->contact;
        $supplier->date_of_agree = $request->date_of_agree;
        $supplier->date_agree_exp = $request->date_agree_exp;
        // $supplier->status = $request->status == true ? 1 : 0;
        $supplier->agree_doc = $file;
        $supplier->update();

        // Update Supplier Payment Information
        $supplier->payment_information()->update(
            [
                'account_name' => isset($request->account_name) && !empty($request->account_name) ? $request->account_name : 'N/A',
                'bank_name' => isset($request->bank_name) && !empty($request->bank_name) ? $request->bank_name : 'N/A',
                'branch_name' => isset($request->branch_name) && !empty($request->branch_name) ? $request->branch_name : 'N/A',
                'account_number' => isset($request->account_number) && !empty($request->account_number) ? $request->account_number : 'N/A',
                'payement_term' => $request->payement_term
            ]
        );

        if ((isset($request->person_name) && !empty($request->person_name)) && (isset($request->person_contact) && !empty($request->person_contact))) {

            $person_name = $request->person_name;
            $person_contact = $request->person_contact;

            foreach ($person_name as $key => $value) {

                $supplier_contact = SupplierContact::updateOrCreate(
                    [
                        'business_id' => $supplier->business_id,
                        'supplier_id' => $supplier->id,
                        'contact' => $person_contact[$key]
                    ],
                    [
                        'name' => $person_name[$key],
                        'status' => 1
                    ]
                );

                if ($supplier_contact->ref_no == '') {
                    // Generate Reference Number for supplier contacts
                    $ref_no = refno_generate(16, 2, $supplier_contact->id);
                    $supplier_contact->ref_no = $ref_no;
                    $supplier_contact->update();
                }
            }

            SupplierContact::where('supplier_id',$supplier->id)->whereNotIn('contact',$person_contact)->delete();
        }

        return $data = [
            'status' => true,
            'message' => 'Selected Vendor Updated Successfully!'
        ];
    }
}
