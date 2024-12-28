<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Details</title>
</head>

<body>
    <table>
        <thead>
            <tr></tr>
            <tr style="background-color: #b4c6e7;">
                <td colspan="4">
                    <h4>Low Stocks Details List</h4>
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
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Vendor Name</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Request Date</td>
                <td style="font-weight: 600; background-color: #d6dce4; text-align: center;">Status</td>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp

            @foreach ($purchase as $item)
                @php
                    $i++;
                @endphp
                <tr>
                    <td>
                        {{ $i }}
                    </td>
                    <td>
                        {{ $item->supplier_Info->name }}
                    </td>
                    <td>
                        {{ $item->purchased_date }}
                    </td>
                    <td>
                        @php
                            $status = '';

                            if ($item->status == 0) {
                                $status = 'Pending';
                            }
                            if ($item->status == 1) {
                                $status = 'Approved';
                            }
                            if ($item->status == 2) {
                                $status = 'Received';
                            }
                        @endphp
                        {{ $status }}
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
</body>

</html>
