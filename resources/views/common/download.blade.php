<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <h1
        style="text-align: center; color:#2072AF;font-weight: bold; font-family: sans-serif; text-transform: uppercase; font-size: 20px">
        Food Cost Calculation
    </h1>

    <table border="0" width="100%" style="margin-top: 20px">
        <tr>
            <td style="width: 80%">
                <p style="color:#2072AF;font-weight: bold; font-family: sans-serif; text-transform: uppercase; font-size:14px">
                    Recipe Details
                </p>
                <table width="100%" style="border: none; margin-top: 10px;font-family: sans-serif; text-align: left">
                    <thead>
                        <tr style="text-align: left">
                            <th style="text-align: left;font-size:12px;">Menu Item Name</th>
                            <th style="text-align: left;font-size:12px;">Menu Price</th>
                            <th style="text-align: left;font-size:12px;">Target Food Cost(%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="font-size: 14px">{{$calculated_data['menu_item']}}</span>
                            </td>
                            <td>
                                <span style="font-size: 14px">{{$calculated_data['menu_price']}}</span>
                            </td>
                            <td>
                                <span style="font-size: 14px">{{$calculated_data['food_cost']}}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p style="color:#2072AF;font-weight: bold; font-family: sans-serif; text-transform: uppercase; font-size:14px">
                    Pricing Details
                </p>
                <table width="100%" style="border: none; margin-top: 10px;font-family: sans-serif; text-align: left">
                    <thead>
                        <tr style="text-align: left">
                            <th style="text-align: left;font-size:12px;">Ingredients Name</th>
                            <th style="text-align: left;font-size:12px;">Quantity Used</th>
                            <th style="text-align: left;font-size:12px;">Ingredients Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($calculated_data['item_list'] as $item)
                            <tr>
                                <td>
                                    <span style="font-size: 14px">{{$item['ingredients_name']}}</span>
                                </td>
                                <td>
                                    <span style="font-size: 14px">{{$item['quantity_used'].' '.$item['radio_quantity_used']}}</span>
                                </td>
                                <td>
                                    <span style="font-size: 14px">{{$item['ingredient_cost']}}</span>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

                <p style="color:#2072AF;font-weight: bold; font-family: sans-serif; text-transform: uppercase; font-size:14px">
                    Additional
                </p>

                <p style="font-family: sans-serif; text-transform: uppercase; font-size:12px">
                    Comments
                </p>

                <p style="font-family: sans-serif; text-transform: uppercase; font-size:12px">
                    {{$calculated_data['comments']}}
                </p>

                <table width="100%" style="border: none; margin-top: 10px;font-family: sans-serif; text-align: left">
                    <tr>
                        <td>
                            <table width="33%" style="border: none;font-family: sans-serif; text-align: center">
                                <img src="{{public_path('layout_style/img/icons/food_cost.png')}}" style="height: 50px; width: 50px" alt="">
                               <p style="font-family: sans-serif; text-transform: uppercase; font-size:12px">Actual Food Cost (%)</p>
                                <span style="color:#2072AF;font-weight: bold; font-family: sans-serif; text-transform: uppercase; font-size:14px">{{$calculated_data['actual_food_cost_percentage']}}</span>
                            </table>
                        </td>
                        <td>
                            <table width="33%" style="border: none;font-family: sans-serif; text-align: center">
                                <img src="{{public_path('layout_style/img/icons/profit.png')}}" style="height: 50px; width: 50px" alt="">
                               <p style="font-family: sans-serif; text-transform: uppercase; font-size:12px">Expected Profit</p>
                                <span style="color:#2072AF;font-weight: bold; font-family: sans-serif; text-transform: uppercase; font-size:14px">{{$calculated_data['profit']}}</span>
                            </table>
                        </td>
                        <td>
                            <table width="33%" style="border: none;font-family: sans-serif; text-align: center">
                                <img src="{{public_path('layout_style/img/icons/selling_price.png')}}" style="height: 50px; width: 50px" alt="">
                               <p style="font-family: sans-serif; text-transform: uppercase; font-size:12px">Expected Selling Price</p>
                                <span style="color:#2072AF;font-weight: bold; font-family: sans-serif; text-transform: uppercase; font-size:14px">{{$calculated_data['selling_price']}}</span>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 2%"></td>
            <td style="width: 18%">
                <img src="{{$chart_img}}" alt="Donut Chart">
            </td>
        </tr>
    </table>
</body>

</html>
