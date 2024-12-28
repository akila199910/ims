<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Write Off Details</title>
</head>

<body>
    <table>
        <thead>
            <tr></tr>
            <tr style="background-color: #b4c6e7;">
                <td colspan="4">
                    <h4>Write Off Details List</h4>
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
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Product</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Retail Price</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">WareHouse</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Qty</td>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp

            @foreach ($writeoff as $item)
                @php
                    $i++;
                @endphp
                <tr>
                    <td>
                        {{ $i }}
                    </td>
                    <td>
                        {{ $item->Product_info->name }}
                    </td>
                    <td>
                        {{ $item->Product_info->retail_price }}
                    </td>
                    <td>{{ $item->WareHouse_info->name }}</td>
                    <td>{{ $item->qty }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>
</body>

</html>
