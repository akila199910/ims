<?php

namespace App\Repositories;

use App\Models\Business;
use App\Models\Products;
use App\Models\PurchaseOrders;
use App\Models\ApprovalHistory;
use App\Models\PurchaseOrderItem;
use App\Models\PurchasePayements;
use App\Models\SupplierPaymentInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderRepository
{

    public function purchase_list($request)
    {

        $query = PurchaseOrders::with(['supplier_Info', 'order_user_info', 'modify_user_info'])
            ->where('business_id', $request->business_id);

        if (isset($request->status))
            $query = $query->where('status', $request->status);

        if (isset($request->statuses) && !empty($request->statuses))
            $query = $query->whereIn('status', $request->statuses);

        if (isset($request->ref_no) && !empty($request->ref_no))
            $query = $query->where('ref_no', $request->ref_no);

        if (isset($request->start_date) && !empty($request->start_date))
            $query = $query->whereDate('purchased_date', '>=', $request->start_date);

        if (isset($request->end_date) && !empty($request->end_date))
            $query = $query->whereDate('purchased_date', '<=', $request->end_date);

        if (isset($request->supplier_id) && !empty($request->supplier_id))
            $query = $query->where('supplier_id', $request->supplier_id);

        if (isset($request->supplier_ids) && !empty($request->supplier_ids))
            $query = $query->whereIn('supplier_id', $request->supplier_ids);

        $pur_orders = $query;

        return $pur_orders;
    }

    public function create($request)
    {
        $pur_order = new PurchaseOrders();
        $pur_order->purchased_date = $request->purchased_date;
        $pur_order->supplier_id = $request->supplier;
        $pur_order->business_id = $request->business_id;
        $pur_order->status = 0;
        $pur_order->due_amount = isset($request->due_amount) && !empty($request->due_amount) ? $request->due_amount : 0;
        $pur_order->discount_amount = isset($request->discount_amount) && !empty($request->discount_amount) ? $request->discount_amount : 0;
        $pur_order->discount_percentage = isset($request->discount_percentage) && !empty($request->discount_percentage) ? $request->discount_percentage : 0;
        $pur_order->total_amount = isset($request->total_amount) && !empty($request->total_amount) ? $request->total_amount : 0;
        $pur_order->tax_amount = isset($request->tax_amount) && !empty($request->tax_amount) ? $request->tax_amount : 0;
        $pur_order->shipping_amount = isset($request->shipping_amount) && !empty($request->shipping_amount) ? $request->shipping_amount : 0;
        $pur_order->other_amount = isset($request->other_amount) && !empty($request->other_amount) ? $request->other_amount : 0;
        $pur_order->final_amount = isset($request->final_amount) && !empty($request->final_amount) ? $request->final_amount : 0;
        $pur_order->order_by = $request->order_by;
        $pur_order->modify_by = $request->modify_by;
        $pur_order->approved_by = null;
        $pur_order->save();

        $ref_no = refno_generate(16, 2, $pur_order->id);
        $pur_order->ref_no = $ref_no;

        $invoice_id = auto_increment_id($pur_order->id);
        $pur_order->invoice_id = $invoice_id;

        //purchaseOrder Item
        $total_amount = 0;
        if (isset($request->product_ids) && !empty($request->product_ids)) {

            $product_id = $request->product_ids;
            $qty = $request->request_qtys;
            $retail_price = $request->retail_prices;

            for ($i = 0; $i < count($product_id); $i++) {
                $product = Products::find($product_id[$i]);

                if ($product) {
                    $request->merge([
                        'purchased_id' => $pur_order->id,
                        'product_id' => $product_id[$i],
                        'qty' => $qty[$i],
                        'unit_price' => $retail_price[$i],
                        'received_qty' => $qty[$i],
                        'available_qty' => $qty[$i]
                    ]);

                    $this->add_purchase_Item($request);
                }
            }

            $total_amount = $pur_order->pur_orderItems->sum('total_amount');
        }

        $tax_amount = $pur_order->tax_amount;
        $shipping_amount = $pur_order->shipping_amount;
        $other_amount = $pur_order->other_amount;
        $discount_amount = $pur_order->discount_amount;
        $amount = $total_amount + $shipping_amount + $other_amount + $tax_amount;

        $final_amount = $amount - $discount_amount;
        $due_amount = $final_amount;

        $pur_order->total_amount = $total_amount;
        $pur_order->due_amount = $due_amount;
        $pur_order->final_amount = $final_amount;
        $pur_order->update();

        return [
            'status' => true,
            'message' => 'New Purchase Order Created Successfully!'
        ];
    }

    // purchaseItem
    public function add_purchase_Item($request)
    {
        $unit_price = isset($request->unit_price) && !empty($request->unit_price) ? $request->unit_price : 0;
        $received_qty = isset($request->received_qty) && !empty($request->received_qty) ? $request->received_qty : 0;
        $total_amount = $unit_price * $received_qty;

        PurchaseOrderItem::updateOrCreate(
            [
                'purchased_id' => $request->purchased_id,
                'product_id' => $request->product_id
            ],
            [
                'unit_price' => isset($request->unit_price) && !empty($request->unit_price) ? $request->unit_price : 0,
                'qty' => isset($request->qty) && !empty($request->qty) ? $request->qty : 0,
                'available_qty' => isset($request->available_qty) && !empty($request->available_qty) ? $request->available_qty : 0,
                'received_qty' => isset($request->received_qty) && !empty($request->received_qty) ? $request->received_qty : 0,
                'total_amount' => $total_amount
            ]
        );

        return true;
    }

    public function purchase_order_info($request)
    {
        $status = false;
        $data = [];

        $purchase_order = PurchaseOrders::with(['pur_orderItems', 'supplier_Info', 'first_payement_info', 'payment_list','approved_user_info'])->where('business_id', $request->business_id)->where('ref_no', $request->order_id)->first();

        if ($purchase_order) {
            $status = true;

            $status_val = 0;
            $color = 'pending';
            $status_name = 'Pending';



            if ($purchase_order->status == 1) {
                $status_val = 1;
                $status_name = 'Approved';
                $color = 'approved';
            }

            if ($purchase_order->status == 2) {
                $status_val = 2;
                $status_name = 'On Hold';
                $color = 'onhold';
            }

            if ($purchase_order->status == 3) {
                $status_val = 3;
                $status_name = 'Cancelled';
                $color = 'cancelled';
            }

            if ($purchase_order->status == 4) {
                $status_val = 4;
                $status_name = 'Full Filled';
                $color = 'fullfilled';
            }

            if ($purchase_order->status == 5) {
                $status_val = 5;
                $status_name = 'Received';
                $color = 'received';
            }

            if ($purchase_order->status == 6) {
                $status_val = 6;
                $status_name = 'Closed';
                $color = 'closed';
            }

            $order_items = $purchase_order->pur_orderItems()->with(['product_info'])->get()->toArray();

            $item_list = [];
            if (count($order_items)) {

                foreach (array_chunk($order_items, 1000) as $item_chunk) {

                    foreach ($item_chunk as $item) {

                        $product = Products::find($item['product_id']);

                        if ($product) {
                            $request->merge([
                                'product_id' => $product->ref_no
                            ]);

                            $product_repo = (new ProductRepository)->product_info($request);

                            $product_data = [];
                            if ($product_repo['status'] == true) {
                                $product_data = $product_repo['data'];
                            }

                            $item_list[] = [
                                'id' => $item['id'],
                                'product_id' => $item['product_id'],
                                'product_name' => $item['product_info']['name'],
                                'unit_price' => $item['unit_price'],
                                'qty' => $item['qty'],
                                'available_qty' => $item['available_qty'],
                                'product_info' => $product_data
                            ];
                        }
                    }
                }
            }

            $item_ids = $purchase_order->pur_orderItems->pluck('product_id')->toArray();

            $paid_amount = $purchase_order->payment_list->sum('paid_amount');
            $due_amount =  $purchase_order->final_amount - $paid_amount;

            $due_amount = $due_amount > 0 ? $due_amount : 0;

            $data = [
                'id' => $purchase_order->id,
                'ref_no' => $purchase_order->ref_no,
                'invoice_id' => $purchase_order->invoice_id,
                'purchased_date' => date('Y-m-d', strtotime($purchase_order->purchased_date)),
                'supplier_id' => $purchase_order->supplier_id,
                'supplier_name' => $purchase_order->supplier_Info->name,
                'status' => $status_val,
                'status_name' => $status_name,
                'color' => $color,
                'due_amount' => $due_amount,
                'final_amount' => $purchase_order->final_amount,
                'discount_amount' => $purchase_order->discount_amount,
                'discount_percentage' => $purchase_order->discount_percentage,
                'total_amount' => $purchase_order->total_amount,
                'tax_amount' => $purchase_order->tax_amount,
                'shipping_amount' => $purchase_order->shipping_amount,
                'other_amount' => $purchase_order->other_amount,
                'paid_amount' => $paid_amount,
                'ordered_by' => (isset($purchase_order->order_user_info) && !empty($purchase_order->order_user_info)) ? $purchase_order->order_user_info->name : 'N/A',
                'modified_by' => (isset($purchase_order->modify_user_info) && !empty($purchase_order->modify_user_info)) ? $purchase_order->modify_user_info->name : 'N/A',
                'approved_by' => (isset($purchase_order->approved_user_info) && !empty($purchase_order->approved_user_info)) ? $purchase_order->approved_user_info->name : 'N/A',
                'item_list' => $item_list,
                'ordered_product_ids' => $item_ids,
                'supplier_info' => [
                    'id' => $purchase_order->supplier_id,
                    'supplier_id' => $purchase_order->supplier_Info->supplier_id,
                    'supplier_name' => $purchase_order->supplier_Info->name,
                    'supplier_address' => $purchase_order->supplier_Info->address,
                    'supplier_email' => $purchase_order->supplier_Info->email,
                    'supplier_contact' => $purchase_order->supplier_Info->contact,
                    'pay_term' => $purchase_order->supplier_Info->payment_information->PaymentTermsInfo->payement_term
                ],
                'first_payment' => [
                    'id' => (isset($purchase_order->first_payement_info) && !empty($purchase_order->first_payement_info)) ? $purchase_order->first_payement_info->id : null,
                    'paid_amount' => (isset($purchase_order->first_payement_info) && !empty($purchase_order->first_payement_info)) ? $purchase_order->first_payement_info->paid_amount : null,
                    'payment_type' => (isset($purchase_order->first_payement_info) && !empty($purchase_order->first_payement_info)) ? $purchase_order->first_payement_info->payment_type : null,
                    'payment_id' => (isset($purchase_order->first_payement_info) && !empty($purchase_order->first_payement_info)) ? $purchase_order->first_payement_info->payment_id : null,
                    'payment_date' => (isset($purchase_order->first_payement_info) && !empty($purchase_order->first_payement_info)) ? $purchase_order->first_payement_info->payment_date : null,
                    'scan_doc' => (isset($purchase_order->first_payement_info) && !empty($purchase_order->first_payement_info)) ? config('aws_url.url') . $purchase_order->first_payement_info->scan_doc : null,
                ]
            ];
        }

        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function update_purchase($request)
    {
        $pur_order = PurchaseOrders::find($request->id);
        $pur_order->due_amount = isset($request->due_amount) && !empty($request->due_amount) ? $request->due_amount : 0;
        $pur_order->final_amount = isset($request->final_amount) && !empty($request->final_amount) ? $request->final_amount : 0;
        $pur_order->discount_amount = isset($request->discount_amount) && !empty($request->discount_amount) ? $request->discount_amount : 0;
        $pur_order->discount_percentage = isset($request->discount_percentage) && !empty($request->discount_percentage) ? $request->discount_percentage : 0;
        $pur_order->total_amount = isset($request->total_amount) && !empty($request->total_amount) ? $request->total_amount : 0;
        $pur_order->modify_by = $request->modify_by;
        $pur_order->status = isset($request->status) && !empty($request->status) ? $request->status : 0;
        $pur_order->update();

        // Update the order total
        //purchaseOrder Item
        $total_amount = 0;
        if (isset($request->product_ids) && !empty($request->product_ids)) {

            $product_id = $request->product_ids;
            $qty = $request->request_qtys;
            $retail_price = $request->retail_prices;
            $received_qty = $request->received_qtys;

            for ($i = 0; $i < count($product_id); $i++) {
                $product = Products::find($product_id[$i]);

                if ($product) {
                    $request->merge([
                        'purchased_id' => $pur_order->id,
                        'product_id' => $product_id[$i],
                        'qty' => $qty[$i],
                        'unit_price' => $retail_price[$i],
                        'received_qty' => (isset($request->received_qtys) && !empty($request->received_qtys)) ? $received_qty[$i] : $qty[$i],
                        'available_qty' => (isset($request->received_qty) && !empty($request->received_qtys)) ? $received_qty[$i] : $qty[$i]
                    ]);

                    $this->add_purchase_Item($request);

                    if (isset($request->status) && !empty($request->status) && $request->status == 5) {
                        $this->update_product_qty($request);
                    }
                }
            }

            $total_amount = $pur_order->pur_orderItems()->where('purchased_id',$pur_order->id)->whereNotIn('product_id',$product_id)->delete();
        }

        $total_amount = $pur_order->pur_orderItems()->sum('total_amount');

        $tax_amount = $pur_order->tax_amount;
        $shipping_amount = $pur_order->shipping_amount;
        $other_amount = $pur_order->other_amount;
        $discount_amount = $pur_order->discount_amount;
        $amount = $total_amount + $shipping_amount + $other_amount + $tax_amount;

        $final_amount = $amount - $discount_amount;
        $due_amount = $final_amount;

        $pur_order->total_amount = $total_amount;
        $pur_order->due_amount = $due_amount;
        $pur_order->final_amount = $final_amount;
        $pur_order->update();

        $paid_amount = 0;
        if ((isset($request->prepay_amount) && !empty($request->prepay_amount)) &&
            (isset($request->paid_date) && !empty($request->paid_date)) &&
            (isset($request->paid_type) && !empty($request->paid_type)) &&
            (isset($request->payment_reference) && !empty($request->payment_reference))
        ) {
            $scan_doc = '';

            $first_payment = PurchasePayements::find($request->first_payment_id);

            $uploaded_file = '';
            if ($first_payment) {
                if (!$first_payment->image)
                    $uploaded_file = '';
                else
                    $uploaded_file = $first_payment->scan_doc;
            }

            if (isset($request->scan_document) && $request->scan_document->getClientOriginalName()) {
                $scan_doc = file_upload($request->scan_document, 'payment_doc');
            } else {
                $scan_doc = $uploaded_file;
            }

            if ($first_payment) {
                $first_payment->purchased_id = $pur_order->id;
                $first_payment->paid_amount = $request->prepay_amount;
                $first_payment->payment_reference = $request->payment_reference;
                $first_payment->payment_id = $request->paid_type;
                $first_payment->payment_date = $request->paid_date;
                $first_payment->scan_doc = $scan_doc;
                $first_payment->update();
            } else {
                $first_payment = new PurchasePayements();
                $first_payment->purchased_id = $pur_order->id;
                $first_payment->paid_amount = $request->prepay_amount;
                $first_payment->payment_reference = $request->payment_reference;
                $first_payment->pay_type_id = $request->paid_type;
                $first_payment->payment_date = $request->paid_date;
                $first_payment->scan_doc = $scan_doc;
                $first_payment->save();
            }

            $paid_amount = $first_payment->paid_amount;
        }

        $paid_amount = $pur_order->payment_list()->sum('paid_amount');

        $due_amount =  $pur_order->final_amount - $paid_amount;

        $pur_order->due_amount = $due_amount > 0 ? $due_amount : 0;
        $pur_order->update();

        if (isset($request->status) && !empty($request->status)) {
            // Approval History
            $history = new ApprovalHistory();
            $history->order_id = $pur_order->id;
            $history->return_id = 0;
            $history->user_id = Auth::user()->id;
            $history->status = $request->status;
            $history->save();
        }

        return [
            'status' => true,
            'message' => 'Selected Purchase Order Updated Successfully!'
        ];
    }

    public function update_product_qty($request)
    {
        $product = Products::find($request->product_id);
        if ($product) {
            $product->qty = $product->qty + $request->received_qty;
            $product->update();
        }
    }


    public function delete($request){

        $purchase = PurchaseOrders::find($request->id);

        if (!$purchase) {
            return [
                'status' => false,
                'message' => 'Purchase Not Found'
            ];
        }

        $purchase->delete();

        return [
            'status' => true,
            'message' => 'Selected purchase Deleted Successfully!'
        ];
    }

    public function store_payments($request)
    {
        $purchase = PurchaseOrders::find($request->purchased_id);

        // if ($request->payment_type == "3") {
        //     $supplier_payment = SupplierPaymentInfo::where('supplier_id',$purchase->supplier_id)->first();
        //     if ($supplier_payment) {
        //         $payment_id = $supplier_payment->id;
        //     }
        // }

        $purchase_pay = new PurchasePayements();
        $purchase_pay->purchased_id = $request->purchased_id;
        $purchase_pay->payment_id = $request->payment_type;
        $purchase_pay->payment_reference = $request->payment_reference;
        $purchase_pay->paid_amount = $request->paid_amount;
        $purchase_pay->payment_date = $request->payment_date;
        $purchase_pay->save();

        $paid_amount = $request->paid_amount;

        //Update the Existing Purchase Total Amount
        $purchase_order = PurchaseOrders::find($request->purchased_id);

        $total_amount = $purchase_order->purchased_amount;
        $due_amount = $purchase_order->total_amount - $purchase_order->paid_amount;

        $paidamount = $purchase_order->payment_list()->sum('paid_amount');

        $purchase_order->due_amount = $due_amount;

        $due_amount = ($total_amount - $paidamount);

        return true;
    }

    public function delete_payement($request)
    {
        $purchase_pay = PurchasePayements::find($request->id);

        if (!$purchase_pay) {
            return [
                'status' => false,
                'message' => 'Purchase Payement Not Found'
            ];
        }

        $purchase_pay->delete();
    }

    public function update_payment($request)
    {
        $purchase_pay = PurchasePayements::find($request->id);

        $purchase_pay->payment_id = $request->payment_type;
        $purchase_pay->payment_reference = $request->payment_reference;
        $purchase_pay->paid_amount = $request->up_paid_amount;
        $purchase_pay->payment_date = $request->payment_date;
        $purchase_pay->update();


        if ($request->hasFile('scan_document')) {

            $file = $request->file('scan_document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents', $filename, 'public');

            if ($purchase_pay->scan_document) {
                Storage::disk('public')->delete($purchase_pay->scan_document);
            }

            $purchase_pay->scan_document = $path;
        }

        return [
            'status' => true,
            'message' => 'Selected  Payment updated successfully!'
        ];
    }
}
