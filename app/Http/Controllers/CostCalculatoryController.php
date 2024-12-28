<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDF;

class CostCalculatoryController extends Controller
{
    public function index(Request $request)
    {
        if (session()->get('calculated_data')) {
            session()->forget('calculated_data');
        }

        return view('common.index');
    }

    public function cost_calculation(Request $request)
    {
        //Creating validation rules according to the $request->closed_value
        $rules['menu_item'] = ['required'];
        $rules['menu_price'] = ['required', 'numeric', 'between:0,9999999999.99'];;
        $rules['food_cost'] =  ['required', 'numeric', 'between:0,100'];;
        foreach ($request->closed_value as $key => $value) {
            $rules['ingredients_name_'.$value] = ['required'];
            $rules['purchase_price_'.$value] = ['required', 'numeric', 'between:0,9999999999.99'];
            $rules['quantity_purchased_'.$value] = ['required', 'numeric', 'between:0,9999999999.99'];
            $rules['radio_quantity_purchased_'.$value] = ['required'];
            $rules['quantity_used_'.$value] = ['required', 'numeric', 'between:0,9999999999.99'];
            $rules['radio_quantity_used_'.$value] = ['required'];
        }

        $messages = [];
        foreach ($request->closed_value as $key => $value) {
            $messages['ingredients_name_' . $value . '.required'] = "The ingredient name field is required.";
            $messages['purchase_price_' . $value . '.required'] = "The purchase price field is required.";
            $messages['purchase_price_' . $value . '.numeric'] = "The purchase price field must be a valid number.";
            $messages['purchase_price_' . $value . '.between'] = "The purchase price field must be between 0 and 9999999999.99.";
            $messages['quantity_purchased_' . $value . '.required'] = "The quantity purchased field is required.";
            $messages['quantity_purchased_' . $value . '.numeric'] = "The quantity purchased field must be a valid number.";
            $messages['quantity_purchased_' . $value . '.between'] = "The quantity purchased field must be between 0 and 9999999999.99.";
            $messages['radio_quantity_purchased_' . $value . '.required'] = "The unit type quantity purchased in  field is required.";
            $messages['quantity_used_' . $value . '.required'] = "The quantity used field is required.";
            $messages['quantity_used_' . $value . '.numeric'] = "The quantity used field must be a valid number.";
            $messages['quantity_used_' . $value . '.between'] = "The quantity used field must be between 0 and 9999999999.99.";
            $messages['radio_quantity_used_' . $value . '.required'] = "The unit type quantity used in  field is required.";
        }

        $validator = Validator::make(
            $request->all(),
            $rules,
            $messages
        );

        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()]);
        }

        $item_list = [];
        $total_cost = 0;
        $actual_food_cost_percentage = 0;
        $selling_price = 0;
        $profit = 0;
        $expected_food_cost = 0;
        $item_name_list = [];
        $item_cost_list = [];
        foreach ($request->closed_value as $key => $value) {
            $item_list[] = [
               'ingredients_name' => $request->input('ingredients_name_'.$value),
               'purchase_price' => $request->input('purchase_price_'.$value),
               'quantity_purchased' => $request->input('quantity_purchased_'.$value),
               'radio_quantity_purchased' => $request->input('radio_quantity_purchased_'.$value),
               'quantity_used' => $request->input('quantity_used_'.$value),
               'radio_quantity_used' => $request->input('radio_quantity_used_'.$value),
               'ingredient_cost' => $request->input('ingredient_cost_'.$value)
            ];

            $item_name_list[] = $request->input('ingredients_name_'.$value);
            $item_cost_list[] = floatval($request->input('ingredient_cost_'.$value));

            $total_cost+=$request->input('ingredient_cost_'.$value);
        }

        $actual_food_cost_percentage = ($total_cost * 100) /$request->menu_price;
        $profit = $request->menu_price - $total_cost;
        $selling_price = ($total_cost * 100) /$request->food_cost;
        $expected_food_cost = ($request->menu_price * $request->food_cost) /100;

        $data = [
            'menu_item' => $request->menu_item,
            'menu_price' => $request->menu_price,
            'food_cost' => $request->food_cost,
            'item_list' => $item_list,
            'total_cost' => number_format($total_cost,2,'.',''),
            'actual_food_cost_percentage' => number_format($actual_food_cost_percentage, 2, '.', '').'%',
            'profit' => number_format($profit,2,'.',''),
            'selling_price' => number_format($selling_price,2,'.',''),
            'expected_food_cost' => number_format($expected_food_cost, 2, '.', ''),
            'item_name_list' => $item_name_list,
            'item_cost_list' =>  $item_cost_list,
            'comments' => isset($request->comments) ? $request->comments : '---'
        ];

        session()->put('calculated_data',$data);

        return response()->json(['status'=>true, 'data' => $data]);
    }

    public function export_calculation(Request $request)
    {
        $chartImage = $request->input('chart_image');

        if (session()->get('calculated_data'))
        {
            $data['calculated_data'] = session()->get('calculated_data');
            $data['chart_img'] = $chartImage;

            $pdf = PDF::loadView('common.download', $data);

            return response($pdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="chart.pdf"');

            return $pdf->download('purchase' . date('Ymdhis') . '.pdf');
        }
    }

    public function cost_calculator_download(Request $request)
    {
        if (session()->get('calculated_data'))
        {
            $data['calculated_data'] = session()->get('calculated_data');
            // $data['chart_img'] = $chartImage;
            // dd($data);
            $pdf = PDF::loadView('common.download', $data);
            return $pdf->stream();
        }
    }
}
