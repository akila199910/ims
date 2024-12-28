<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Details</title>
</head>

<body>
    <table>
        <thead>
            <tr></tr>
            <tr style="background-color: #b4c6e7;">
                <td colspan="4">
                    <h4>Payment Details List</h4>
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
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Pur.Invoice ID</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Payment Ref No.</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Payment Type</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Paid Date</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Paid Amount</td>

            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp

            @foreach ($payment as $item)
                @php
                    $i++;
                @endphp
                <tr>
                    <td>
                        {{ $i }}
                    </td>
                    <td>
                        {{ $item->purchase_info->invoice_id }}
                    </td>
                    <td>{{ $item->payment_reference }}</td>
                    <td>{{ $item->payment_type_info->payment_type }}</td>
                    <td>{{ $item->payment_date }}</td>
                    <td>{{ $item->paid_amount }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>
</body>

</html>
