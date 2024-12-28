<?php

namespace App\Repositories;

use App\Models\Business;
use App\Models\Products;
use App\Models\PurchaseReturn;
use App\Models\ApprovalHistory;
use App\Models\ProductWarehouse;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReturn_Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PurchaseReturnRepository
{
    public function pur_return_list($request)
    {
        $query = PurchaseReturn::with(['purchase_info'])->where('business_id', $request->business_id);

        if (isset($request->ref_no) && !empty($request->ref_no))
            $query = $query->where('ref_no', $request->ref_no);

        $pur_return = $query;

        return $pur_return;
    }


    public function get_purchase_items($request)
    {
        $query = PurchaseOrderItem::where('purchased_id', $request->order_id);
        if (isset($request->not_qty) && !empty($request->not_qty) && $request->not_qty == true)
            $query = $query->where('available_qty', '>', 0);

        $product_id = $query->pluck('product_id')->toArray();

        $products = Products::whereIn('id', $product_id)->get();

        return $products;
    }

    public function create($request)
    {
        $pur_return = new PurchaseReturn();
        $pur_return->return_date = $request->return_date;
        $pur_return->purchased_id  = $request->purchase_ref;
        $pur_return->status = 0;
        $pur_return->sub_total_amount = $request->sub_total_amount;
        $pur_return->tax_amount = $request->tax_amount;
        $pur_return->shipping_amount = $request->shipping_amount;
        $pur_return->other_amount = $request->other_amount;
        $pur_return->net_total_amount = $request->net_total_amount;
        $pur_return->business_id = $request->business_id;
        $pur_return->save();

        $ref_no = refno_generate(16, 2, $pur_return->id);
        $pur_return->ref_no = $ref_no;
        $pur_return->update();

        $request_data = [];

        $total_amount = 0;
        if (isset($request->product_ids) && !empty($request->product_ids)) {
            $pur_order_item_id = $request->pur_order_item_ids;
            $product_id = $request->product_ids;
            $qty = $request->request_qtys;
            $unit_price = $request->unit_prices;

            for ($i = 0; $i < count($product_id); $i++) {
                $purchase_item = PurchaseOrderItem::find($pur_order_item_id[$i]);

                if ($purchase_item && $purchase_item->available_qty > 0 && ($purchase_item->available_qty >= $qty[$i])) {
                    $request->merge([
                        'return_id' => $pur_return->id,
                        'product_id' => $product_id[$i],
                        'qty' => $qty[$i],
                        'order_item_id' => $pur_order_item_id[$i],
                        'unit_price' => $unit_price[$i],
                        'total_amount' => $qty[$i] * $unit_price[$i]
                    ]);

                    $this->add_pur_item($request);
                }
            }

            $total_amount = $pur_return->pur_orderItems->sum('total_amount');
        }

        return [
            'status' => true,
            'message' => 'New Purchase Return Created Successfully!'
        ];
    }

    public function purchase_return_info($request)
    {
        $status = false;
        $data = [];

        $pur_return = PurchaseReturn::with(['pur_return_item', 'purchase_info'])->where('business_id', $request->business_id)->where('ref_no', $request->return_id)->first();

        if ($pur_return) {
            $status = true;

            $status_val = 0;
            $color = 'pending';
            $status_name = 'Return Pending';

            if ($pur_return->status == 1) {
                $status_val = 1;
                $status_name = 'Return Approved';
                $color = 'approved';
            }

            if ($pur_return->status == 2) {
                $status_val = 2;
                $status_name = 'Return OnHold';
                $color = 'onhold';
            }

            if ($pur_return->status == 3) {
                $status_val = 3;
                $status_name = 'Return Cancelled';
                $color = 'cancelled';
            }

            if ($pur_return->status == 4) {
                $status_val = 4;
                $status_name = 'Return FullFilled';
                $color = 'fullfilled';
            }

            if ($pur_return->status == 5) {
                $status_val = 5;
                $status_name = 'Return Returned';
                $color = 'returned';
            }

            if ($pur_return->status == 6) {
                $status_val = 6;
                $status_name = 'Return Closed';
                $color = 'closed';
            }

            $order_items = $pur_return->pur_orderItems()->with(['product_info'])->get()->toArray();

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

            $item_ids = $pur_return->pur_orderItems->pluck('product_id')->toArray();
            $return_items = $pur_return->pur_return_item->pluck('product_id')->toArray();

            $data = [
                'id' => $pur_return->id,
                'ref_no' => $pur_return->ref_no,
                'status' => $status_val,
                'status_name' => $status_name,
                'return_date' => $pur_return->return_date,
                'created_date' => $pur_return->created_at,
                'sub_total_amount' => $pur_return->sub_total_amount,
                'tax_amount' => $pur_return->tax_amount,
                'shipping_amount' => $pur_return->shipping_amount,
                'other_amount' => $pur_return->other_amount,
                'net_total_amount' => $pur_return->net_total_amount,
                'color' => $color,
                'item_list' => $item_list,
                'ordered_product_ids' => $item_ids,
                'return_items_ids' => $return_items,
                'purchase_info' => [
                    'id' => $pur_return->purchased_id,
                    'supplier_id' => $pur_return->purchase_info->supplier_id,
                    'invoice_id' => $pur_return->purchase_info->invoice_id,
                    'final_amount' => $pur_return->purchase_info->final_amount,
                    'discount_amount' => $pur_return->purchase_info->discount_amount,
                    'discount_percentage' => $pur_return->purchase_info->discount_percentage,
                    'total_amount' => $pur_return->purchase_info->total_amount,
                    'tax_amount' => $pur_return->purchase_info->tax_amount,
                    'shipping_amount' => $pur_return->purchase_info->shipping_amount,
                    'other_amount' => $pur_return->purchase_info->other_amount,
                    'supplier_info' => [
                        'supplier_name' => $pur_return->purchase_info->supplier_Info->name,
                        'supplier_address' =>  $pur_return->purchase_info->supplier_Info->address,
                        'supplier_email' =>  $pur_return->purchase_info->supplier_Info->email,
                        'supplier_contact' =>  $pur_return->purchase_info->supplier_Info->contact
                    ]

                ],

            ];
        }

        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function add_pur_item($request)
    {
        $purchase_order = PurchaseOrderItem::find($request->order_item_id);

        if ($purchase_order) {
            PurchaseReturn_Item::updateOrCreate(
                [
                    'return_id' => $request->return_id,
                    'product_id' => $request->product_id,
                    'order_item_id' => $request->order_item_id
                ],
                [
                    'unit_price' => isset($request->unit_price) && !empty($request->unit_price) ? $request->unit_price : 0,
                    'qty' => isset($request->qty) && !empty($request->qty) ? $request->qty : 0,
                    'total_amount' => isset($request->total_amount) && !empty($request->total_amount) ? $request->total_amount : 0,
                ]
            );

            if (isset($request->av_qty) && !empty($request->av_qty)) {
                // Update the available_qty
                $purchase_order->available_qty = $request->av_qty;
                $purchase_order->update();

                // Recall the PurchaseOrderItem
                $purchase_order = PurchaseOrderItem::find($request->order_item_id);
            }

            $av_qty = $purchase_order->available_qty;
            $av_qty = $av_qty - $request->qty;

            $purchase_order->available_qty = $av_qty > 0 ? $av_qty : 0;
            $purchase_order->update();
        }

        return true;
    }

    public function delete_pur_item($request)
    {
        $pur_return_item = PurchaseReturn_Item::find($request->return_id);

        $purchase_order = PurchaseOrderItem::find($pur_return_item->order_item_id);

        if ($purchase_order) {

            $purchase_order->available_qty = $purchase_order->available_qty + $pur_return_item->qty;
            $purchase_order->update();

            $pur_return_item->delete();
        }
    }

    public function delete_pur_return($request)
    {
        $pur_return = PurchaseReturn::find($request->id);

        if ($pur_return) {
            $items = $pur_return->pur_return_item->pluck('id')->toArray();

            foreach (array_chunk($items, 1000) as $item_chunk) {
                foreach ($item_chunk as $item) {
                    $request->merge([
                        'return_id' => $item
                    ]);

                    $this->delete_pur_item($request);
                }
            }

            $pur_return->delete();
        }
    }

    public function update_purchase($request)
    {
        $pur_return = PurchaseReturn::find($request->id);

        if ($pur_return) {
            $pur_return->purchased_id  = $request->purchase_ref;
            $pur_return->status = isset($request->status) && !empty($request->status) ? $request->status : 0;
            $pur_return->sub_total_amount = $request->sub_total_amount;
            $pur_return->tax_amount = $request->tax_amount;
            $pur_return->shipping_amount = $request->shipping_amount;
            $pur_return->other_amount = $request->other_amount;
            $pur_return->net_total_amount = $request->net_total_amount;
            $pur_return->update();

            $request_data = [];

            $total_amount = 0;
            if (isset($request->product_ids) && !empty($request->product_ids)) {

                $pur_order_item_id = $request->pur_order_item_ids;
                $product_id = $request->product_ids;
                $qty = $request->request_qtys;
                $unit_price = $request->retail_prices;
                $av_qty = $request->av_qtys;

                for ($i = 0; $i < count($product_id); $i++) {
                    $purchase_item = PurchaseOrderItem::find($pur_order_item_id[$i]);

                    if ($purchase_item) {
                        $request->merge([
                            'return_id' => $pur_return->id,
                            'product_id' => $product_id[$i],
                            'qty' => $qty[$i],
                            'order_item_id' => $pur_order_item_id[$i],
                            'unit_price' => $unit_price[$i],
                            'total_amount' => $qty[$i] * $unit_price[$i],
                            'av_qty' => $av_qty[$i]
                        ]);

                        $this->add_pur_item($request);
                    }
                }
            }
        }

        return [
            'status' => true,
            'message' => 'New Purchase Return Created Successfully!'
        ];
    }
}
