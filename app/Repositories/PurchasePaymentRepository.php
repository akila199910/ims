<?php

namespace App\Repositories;

use App\Models\PurchaseOrders;
use App\Models\PurchasePayements;

class PurchasePaymentRepository
{
    public function payment_list($request)
    {
        $query = PurchasePayements::with(['payment_type_info','purchase_info']);

            if(isset($request->purchased_id) && !empty($request->purchased_id))
                $query = $query->where('purchased_id', $request->purchased_id);

            if(isset($request->payment_type) && !empty($request->payment_type))
                $query = $query->where('pay_type_id', $request->payment_type);

            if(isset($request->start_date) && !empty($request->start_date))
                $query = $query->whereDate('payment_date','>=', $request->start_date);

            if(isset($request->end_date) && !empty($request->end_date))
                $query = $query->whereDate('payment_date','<=', $request->end_date);

        $payment_list = $query;

        return $payment_list;
    }

    public function create_payment($request)
    {
        $scan_doc = '';
        if (isset($request->scan_document) && $request->scan_document->getClientOriginalName()) {
            $scan_doc = file_upload($request->scan_document, 'payment_doc');
        }

        $payment = new PurchasePayements();
        $payment->purchased_id = $request->purchase_id;
        $payment->paid_amount = $request->amount;
        $payment->payment_reference = $request->payment_reference;
        $payment->pay_type_id = $request->payment_type;
        $payment->payment_date = $request->paid_date;
        $payment->scan_doc = $scan_doc;
        $payment->save();

        $pur_order = PurchaseOrders::find($request->purchase_id);

        $paid_amount = $pur_order->payment_list()->sum('paid_amount');

        $due_amount =  $pur_order->final_amount - $paid_amount;

        $pur_order->due_amount = $due_amount > 0 ? $due_amount : 0;
        $pur_order->update();

        $data = [
            'status' => true,
            'message' => 'New Payment added successfully!'
        ];

        return $data;
    }

    public function update_payment($request)
    {
        $payment = PurchasePayements::find($request->payment_id);
        $scan_doc = '';
        if (isset($request->scan_document) && $request->scan_document->getClientOriginalName()) {
            $scan_doc = file_upload($request->scan_document, 'payment_doc');
        }
        else
        {
            if (!$payment->scan_doc)
                $scan_doc = '';
            else
                $scan_doc = $payment->scan_doc;
        }

        $payment->paid_amount = $request->amount;
        $payment->payment_reference = $request->payment_reference;
        $payment->pay_type_id = $request->payment_type;
        $payment->payment_date = $request->paid_date;
        $payment->scan_doc = $scan_doc;
        $payment->update();

        $pur_order = PurchaseOrders::find($payment->purchased_id);
        $pur_order->due_amount = $request->due_amount;
        $pur_order->update();

        $paid_amount = $pur_order->payment_list()->sum('paid_amount');

        $due_amount =  $pur_order->final_amount - $paid_amount;

        $pur_order->due_amount = $due_amount > 0 ? $due_amount : 0;
        $pur_order->update();

        $data = [
            'status' => true,
            'message' => 'Selected Payment Updated Successfully!'
        ];

        return $data;
    }

    public function delete_payment($request)
    {
        $payment = PurchasePayements::find($request->id);

        if ($payment) {
           $pur_order = PurchaseOrders::find($payment->purchased_id);
           if ($pur_order) {
                $pur_order->due_amount = $pur_order->due_amount + $payment->paid_amount;
                $pur_order->update();
           }

           $payment->delete();
        }

        $data = [
            'status' => true,
            'message' => 'Selected Payment deleted successfully!'
        ];

        return $data;
    }
}
