<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
</head>

<body>
    <style>
        .invoice-item-box {

            border-radius: 5px;
            padding: 4px;
            width: 150px;
            float: right;
            margin-top: -4px;
            margin-bottom: 2px;
        }

        .invoice-item-box p {
            font-family: sans-serif;
            font-weight: 500;
            font-size: 14px;
            padding-bottom: 2px;

        }
    </style>
    <div>
        <h1
            style="text-align: right; color:#2072AF;font-weight: bold; font-family: sans-serif; text-transform: uppercase;">
            Purchase Order</h1>
        <table border="0" width="100%">
            <tr>
                <!-- Left side: Invoice details -->
                <td
                    style="width: 48%; text-align: left; font-size: 20px; font-weight: bold; font-family: sans-serif; text-transform: uppercase; padding: 10px 0px;">
                    <br>

                </td>
                <!-- Right side: Date and PO details -->
                <td style="width: 52%;">
                    <div class="invoice-item-box">
                        <p>Return Purchase_ID:<span style="font-weight: 400">#{{ $pur_return->purchase_info->invoice_id }}</span></p>
                        <p>Date : &nbsp;<span
                                style="font-weight: 400">{{ date('jS M, Y', strtotime($pur_return->created_at)) }}</span>
                        </p>
                    </div>
                </td>
            </tr>
        </table>
        <br>
        <br>


        <table border="0" width="100%" style="margin-top: 20px">
            <tr>
                <td style="width: 48%">
                    <table border="0" width="100%; height:150px">
                        <tr style="background_color:#2072AF; text-align:left; color:#fff">
                            <td
                                style="font-size: 14px; font-family: sans-serif; text-transform: uppercase; padding: 5px 10px 5px;">
                                <strong style="text-decoration: underline;">Invoice From : <br></strong>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{{ Str::limit($pur_return->business_info->name,30) }} <br></strong>
                                <span> {{ Str::limit($pur_return->business_info->address,30) }} <br></span>
                                <span> {{ Str::limit($pur_return->business_info->email,30) }} <br></span>
                                <span> {{ $pur_return->business_info->contact }} <br></span>
                                <span> {{ date('Y-m-d') }} <br></span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 4%"></td>
                <td style="width: 48%">
                    <table border="0" width="100%; height:150px">
                        <tr style="background_color:#2072AF; text-align:left; color:#fff">
                            <td
                                style="font-size: 14px; font-family: sans-serif; text-transform: uppercase; padding: 5px 10px 5px;">
                                <strong style="text-decoration: underline;">Invoice To : <br></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ Str::limit($pur_return->purchase_info->supplier_Info->name,30) }} <br></strong>
                                <span> {{ Str::limit($pur_return->purchase_info->supplier_Info->address,30) }} <br></span>
                                <span> {{ Str::limit($pur_return->purchase_info->supplier_Info->email,30) }} <br></span>
                                <span> {{ $pur_return->purchase_info->supplier_Info->contact }} <br></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <p
            style="font-size: 16px;font-weight: bold; font-family: sans-serif; text-transform: uppercase; text-align: center; margin-top: 30px">
            Product List
        </p>

        <table width="100%" style="border-collapse: collapse; border: 1px solid #2072AF; margin-top: 30px; ">
            <thead>
                <tr style="background_color:#2072AF;color: #fff">
                    <th>#</th>
                    <th>Product_ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>QTY</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 0;
                @endphp
                @foreach ($pur_return->pur_return_item as $item)
                    @php
                        $i++;

                    @endphp
                    <tr style="border: 1px solid #2072AF; padding-top: 10px; padding-bottom: 10px; text-align:center">
                        <td style="text-align: center">
                            {{ $i }}
                        </td>
                        <td>{{ $item->Product_info->product_id }}</td>
                        <td>{{ Str::limit($item->Product_info->name,30) }}</td>
                        <td>
                            {{ Str::limit($item->Product_info->name . ' - ' . $item->Product_info->unit_info_pdf->name,30) }}
                        </td>
                        <td style="text-align: center">
                            {{ $item->qty }}
                        </td>
                        <td>{{ $item->unit_price }}</td>
                        <td>{{ $item->unit_price * $item->qty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width: 100%;">

            <tr style="border: 1px solid #2072AF; padding-top: 10px; padding-bottom: 10px; text-align:left;">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: left">Sub Total</td>
                <td style="text-align: right;border: 1px solid #2072AF;">
                    {{ number_format($pur_return->sub_total_amount ?? 0, 2) }}</td>
            </tr>
            <tr style="border: 1px solid #2072AF; padding-top: 10px; padding-bottom: 10px; text-align:left">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: left">Tax Amount</td>
                <td style="text-align: right;border: 1px solid #2072AF;width:12%">
                    {{ number_format($pur_return->tax_amount ?? 0, 2) }}</td>
            </tr>
            <tr style="border: 1px solid #2072AF; padding-top: 10px; padding-bottom: 10px; text-align:left">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: left">Shipping Amount</td>
                <td style="text-align: right;border: 1px solid #2072AF;width:12%">
                    {{ number_format($pur_return->shipping_amount ?? 0, 2) }}</td>
            </tr>
            <tr style="border: 1px solid #2072AF; padding-top: 10px; padding-bottom: 10px; text-align:left">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: left">Other Amount</td>
                <td style="text-align: right;border: 1px solid #2072AF;width:12%">
                    {{ number_format($pur_return->other_amount ?? 0, 2) }}</td>
            </tr>
            <br>
            <tr style="border: 1px solid #2072AF; padding-top: 10px; padding-bottom: 10px; text-align:left">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: left">Net Amount</td>
                <td style="text-align: right;border: 1px solid #2072AF;width:12%;background_color:#2072AF;color: #fff">
                    {{ number_format($pur_return->net_total_amount ?? 0, 2) }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
