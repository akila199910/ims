<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Products;
use App\Models\ProductWarehouse;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\Storage;

class StockTransfersRepository
{

    public function transfer_list($request)
    {
        $query = StockTransfer::with(['from_warehouse', 'to_warehouse', 'creator_info', 'editor_info','product_info'])->where('business_id', $request->business_id);
                if (isset($request->from_warehouse) && !empty($request->from_warehouse))
                $query = $query->where('warehouse_from',$request->from_warehouse);

                if (isset($request->to_warehouse) && !empty($request->to_warehouse))
                $query = $query->where('warehouse_to',$request->to_warehouse);

                if (isset($request->product) && !empty($request->product))
                $query = $query->where('product_id',$request->product);

                if (isset($request->start_date) && !empty($request->start_date))
                $query = $query->whereDate('transfer_date','>=',$request->start_date);

                if (isset($request->to_date) && !empty($request->to_date))
                $query = $query->whereDate('transfer_date','<=',$request->to_date);

        $stock_transfer = $query;

        return $stock_transfer;
    }

    public function available_products($request)
    {
        $to_warehouse_item_ids = ProductWarehouse::where('warehouse_id', $request->to_id)->pluck('product_id')->toArray();
        $from_warehouse_from_ids = ProductWarehouse::where('warehouse_id', $request->from_id)->where('qty','>', 0)->whereIn('product_id',$to_warehouse_item_ids)->pluck('product_id')->toArray();

        $products = Products::where('status',1)->whereIn('id',$from_warehouse_from_ids)->get()->toArray();

        return $products;
    }

    public function create_transfer($request)
    {
        if ((isset($request->product_ids) && !empty($request->product_ids)) && (isset($request->qtys) && !empty($request->qtys))) {
            $product_ids = $request->product_ids;
            $qtys = $request->qtys;

            $save_data = [];
            $qty_update_data = [];
            foreach ($product_ids as $key => $product) {
                $save_data[] = [
                    'warehouse_from' => $request->warehouse_from,
                    'warehouse_to' => $request->warehouse_to,
                    'transfer_date' => $request->transfer_date,
                    'created_by' => $request->created_by,
                    'edit_by' => $request->edit_by,
                    'product_id' => $product_ids[$key],
                    'qty' => $qtys[$key],
                    'business_id' => $request->business_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $qty_update_data[] = [
                    'warehouse_from' => $request->warehouse_from,
                    'warehouse_to' => $request->warehouse_to,
                    'product_id' => $product_ids[$key],
                    'qty' => $qtys[$key],
                ];
            }

            foreach (array_chunk($save_data, 2000) as $save_chunk) {
                StockTransfer::insert($save_chunk);
            }

            foreach (array_chunk($qty_update_data, 2000) as $qty_chunk) {
                foreach ($qty_chunk as $key => $update_data) {
                    $from_warehouse = ProductWarehouse::where('warehouse_id',$update_data['warehouse_from'])->where('product_id',$update_data['product_id'])->first();
                    $to_warehouse = ProductWarehouse::where('warehouse_id',$update_data['warehouse_to'])->where('product_id',$update_data['product_id'])->first();

                    if ($from_warehouse && $to_warehouse) {
                        // Update From Warehouse Qty
                        $up_qty = $from_warehouse->qty - $update_data['qty'];
                        $up_qty = $up_qty > 0 ? $up_qty : 0;

                        $from_warehouse->qty = $up_qty;
                        $from_warehouse->update();

                        // Update To Warehouse qty
                        $to_warehouse->qty = $to_warehouse->qty + $update_data['qty'];
                        $to_warehouse->update();
                    }
                }
            }

            // Update Stock Transfer ref no
            $stock_transfer = StockTransfer::where('ref_no',null)->get()->toArray();

            if (count($stock_transfer)) {
                foreach (array_chunk($stock_transfer, 1000) as $transfer_chunk) {
                    foreach ($transfer_chunk as $transfer) {
                        $st_transfer = StockTransfer::find($transfer['id']);

                        if ($st_transfer) {
                            $ref_no = refno_generate(16, 2, $st_transfer->id);
                            $st_transfer->ref_no = $ref_no;
                            $st_transfer->update();
                        }
                    }
                }
            }
        }

        $data = [
            'status' => true,
            'message' => 'New Stock Transfer Created Successfully!'
        ];

        return $data;
    }

    public function update_transfer($request)
    {
        $stock_transfer = StockTransfer::find($request->id);

        if ($stock_transfer) {
            $from_warehouse = ProductWarehouse::where('warehouse_id',$stock_transfer->warehouse_from )->where('product_id',$stock_transfer->product_id)->first();
            $to_warehouse = ProductWarehouse::where('warehouse_id',$stock_transfer->warehouse_to)->where('product_id',$stock_transfer->product_id)->first();

            if ($from_warehouse && $to_warehouse) {
                $transfered_qty = $stock_transfer->qty;

                $from_qty = $from_warehouse->qty;
                $to_qty = $to_warehouse->qty;

                // This function checking requesting qty less than already transfered qty
                if ($transfered_qty > $request->qty) {
                    $from_qty = $from_qty + ($transfered_qty - $request->qty);
                    $to_qty = $to_qty - ($transfered_qty - $request->qty);
                }

                // This function checking requesting qty less than already transfered qty
                if ($request->qty > $transfered_qty) {
                    $from_qty = $from_qty - ($request->qty - $transfered_qty);
                    $to_qty = $to_qty + ($request->qty - $transfered_qty);
                }

                // Updating from ware house
                $from_warehouse->qty = $from_qty > 0 ? $from_qty : 0;
                $from_warehouse->update();

                // Updating to ware house
                $to_warehouse->qty = $to_qty > 0 ? $to_qty : 0;
                $to_warehouse->update();

                // Update transfer
                $stock_transfer->qty = $request->qty;
                $stock_transfer->edit_by = $request->edit_by;
                $stock_transfer->update();
            }
        }

        return [
            'status' => true,
            'message' => 'Selected Stock Transfer updated successfully!'
        ];
    }
}
