<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stock Transfer Details</title>
</head>

<body>
    <table>
        <thead>
            <tr></tr>
            <tr style="background-color: #b4c6e7;">
                <td colspan="4">
                    <h4>Stock Transfer Details List</h4>
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
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Transfer Date</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">WareHouse From</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">WareHouse To</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Created By</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Edited By</td>

            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp

            @foreach ($stock_transfer as $item)
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
                        {{ $item->transfer_date }}
                    </td>
                    <td>
                        {{ $item->from_warehouse->name }}
                    </td>
                    <td>
                        {{ $item->to_warehouse->name }}
                    </td>
                    <td>
                        {{ $item->creator_info->name }}
                    </td>
                    <td>
                        {{ $item->editor_info->name }}
                    </td>


                </tr>
            @endforeach
        </tbody>

    </table>
</body>

</html>
