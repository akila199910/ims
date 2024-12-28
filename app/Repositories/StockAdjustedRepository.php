<?php

namespace App\Repositories;

use App\Models\Products;
use App\Models\ProductWarehouse;
use App\Models\PurchaseOrderItem;
use App\Models\StockAdjustedItems;
use App\Models\StockAdjustments;
use Illuminate\Support\Facades\Storage;

class StockAdjustedRepository
{

    public function stockAdjust_list($request)
    {
        $query = StockAdjustments::with(['purchase_info','stock_adjust_item'])->where('business_id', $request->business_id);

        if (isset($request->ref_no) && !empty($request->ref_no))
            $query = $query->where('ref_no', $request->ref_no);

        $stock_adjust = $query;

        return $stock_adjust;
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
        $Stock_ad = new StockAdjustments();
        $Stock_ad->adjusted_date = $request->adjusted_date;
        $Stock_ad->purchased_id  = $request->purchase_ref;
        $Stock_ad->business_id = $request->business_id;
        $Stock_ad->save();

        $ref_no = refno_generate(16, 2, $Stock_ad->id);
        $Stock_ad->ref_no = $ref_no;
        $Stock_ad->update();

        $request_data = [];
        //purchaseOrder Item
        if ((isset($request->product_ids) && !empty($request->product_ids))
            && (isset($request->warehouse_ids) && !empty($request->warehouse_ids))
            && (isset($request->qtys) && !empty($request->qtys))
        ) {

            $product_id = $request->product_ids;
            $warehouse_id = $request->warehouse_ids;
            $qty = $request->qtys;
            $order_item_id = $request->order_item_ids;

            foreach ($product_id as $key => $product) {
                $request_data[] =
                [
                    'adjusted_id' => $Stock_ad->id,
                    'product_id' => $product_id[$key],
                    'warehouse_id' => $warehouse_id[$key],
                    'qty' => $qty[$key],
                    'order_item_id' => $order_item_id[$key]
                ];
            }
        }

        if (isset($request_data) && !empty($request_data)) {
            foreach (array_chunk($request_data,1000) as $request_chunk) {
                foreach ($request_chunk as $key => $item) {
                    $request->merge([
                        'adjusted_id' => $item['adjusted_id'],
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $item['warehouse_id'],
                        'qty' => $item['qty'],
                        'order_item_id' => $item['order_item_id'],
                    ]);

                    $this->add_stock_item($request);
                }
            }
        }

        return [
            'status' => true,
            'message' => 'New Stock Adjustment Created Successfully!'
        ];
    }

    // purchaseItem
    public function add_stock_item($request)
    {
        $purchase_order = PurchaseOrderItem::find($request->order_item_id);

        $product_warehouse = ProductWarehouse::where('product_id',$request->product_id)->where('warehouse_id',$request->warehouse_id)->first();

        if ($purchase_order && $product_warehouse) {
            StockAdjustedItems::updateOrCreate(
                [
                    'adjusted_id' => $request->adjusted_id,
                    'product_id' => $request->product_id,
                    'warehouse_id' => $request->warehouse_id,
                    'order_item_id' => $request->order_item_id
                ],
                [
                    'qty' => $request->qty
                ]
            );

            // Update purchase Item Qty
            $av_qty = $purchase_order->available_qty;
            $av_qty = $av_qty - $request->qty;

            $purchase_order->available_qty = $av_qty > 0 ? $av_qty : 0;
            $purchase_order->update();

            // Update product ware house item
            $product_warehouse->qty =  $product_warehouse->qty + $request->qty;
            $product_warehouse->update();
        }

        return true;
    }

    public function delete_stock_item($request)
    {
        $adjust_item = StockAdjustedItems::find($request->adjust_id);

        $purchase_order = PurchaseOrderItem::find($adjust_item->order_item_id);

        $product_warehouse = ProductWarehouse::where('product_id',$adjust_item->product_id)->where('warehouse_id',$adjust_item->warehouse_id)->first();

        if ($purchase_order && $product_warehouse) {

            $warehouse_qty = $product_warehouse->qty;
            $qty = $warehouse_qty - $adjust_item->qty;

            $up_qty = $qty > 0 ? $qty : 0;

            // Update product ware house item
            $product_warehouse->qty =  $up_qty;
            $product_warehouse->update();

            // adjust qty
            $adjust_qty = $adjust_item->qty;
            $available_qty = $warehouse_qty >= $adjust_qty ? $adjust_qty : $warehouse_qty;

            // Update Purchase order it availablity
            $purchase_order->available_qty = $purchase_order->available_qty + $available_qty;
            $purchase_order->update();


            $adjust_item->delete();
        }
    }

    public function delete_stock_adjust($request)
    {
        $stock_adjust = StockAdjustments::find($request->id);

        if ($stock_adjust) {
            $items = $stock_adjust->stock_adjust_item->pluck('id')->toArray();

            foreach (array_chunk($items, 1000) as $item_chunk) {
                foreach ($item_chunk as $item) {
                    $request->merge([
                        'adjust_id' => $item
                    ]);

                    $this->delete_stock_item($request);
                }
            }

            $stock_adjust->delete();
        }
    }
}
