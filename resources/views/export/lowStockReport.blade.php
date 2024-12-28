<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Low Stock Details</title>
</head>

<body>
    <table>
        <thead>
            <tr></tr>
            <tr style="background-color: #b4c6e7;">
                <td colspan="4">
                    <h4>Low Stock Details List</h4>
                </td>
            </tr>
            <tr></tr>
            <tr style="background-color: #b4c6e7;">
                <td colspan="4">
                    Business Name : {{ ucwords($business->name) }}
                </td>
            </tr>
            <tr style="background-color: #b4c6e7;">
                <td colspan="4">
                    Generated At : {{ date('M d, Y H:i:s A') }}
                </td>
            </tr>
            <tr></tr>
        </thead>
    </table>
    <table style="border: 1px solid #000">

        <thead>
            <tr>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">#</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Product Name</td>
                 <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">WareHouse</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Available Qty</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Qty</td>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp

            @foreach ($lowStocks as $item)
                @php
                    $i++;
                @endphp
                <tr>
                    <td>
                        {{ $i }}
                    </td>
                    <td>
                        {{ $item->product_info->name }}
                    </td>
                    <td>
                        {{ $item->warehouse_info->name }}
                    </td>
                    <td>
                        {{ $item->qty }}
                    </td>
                    <td>
                        {{ $item->qty_alert }}
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
</body>

</html>
