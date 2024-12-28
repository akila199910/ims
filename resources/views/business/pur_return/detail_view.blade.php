@extends('layouts.business')

@section('title')
   Manage Purchase Returns
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.purchase_return') }}"> Manage Purchase Returns</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Purchase Return Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.purchase_return') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <form id="submitForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Purchase Return details</h4>
                                </div>
                            </div>
                            <input type="hidden" name="id" id="id" value="{{ $pur_return['id'] }}">
                            <div class="col-12 col-md-6">
                                <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Return To
                                </span><br>
                                <span style="font-size: 14px">{{ Str::limit($pur_return['purchase_info']['supplier_info']['supplier_name'],30) }}</span><br>
                                <span
                                    style="font-size: 14px">{{ $pur_return['purchase_info']['supplier_info']['supplier_address'] }}</span><br>
                                <span style="font-size: 14px">{{ Str::limit($pur_return['purchase_info']['supplier_info']['supplier_email'],30) }}</span><br>
                                <span style="font-size: 14px">{{ $pur_return['purchase_info']['supplier_info']['supplier_contact'] }}</span>
                                <br>
                                <br>
                                <div class="col-12 col-md-12 col-xl-12 col-lg-12">
                                    <div class="invoice-total-box invoice-top">
                                        <div class="invoice-total-inner">
                                            <p class="text-uppercase text-bold"
                                                style="font-weight: 600;font-size:14px;color:#000">Returned Date <span
                                                    style="font-size:12px;color:#2072AF">{{ $pur_return['return_date'] }}</span>
                                                </span>
                                            </p>
                                            <p class="text-uppercase text-bold"
                                                style="font-weight: 600;font-size:14px;color:#000">Vendor Name <span
                                                    style="font-size:12px;color:#2072AF">{{ Str::limit($pur_return['purchase_info']['supplier_info']['supplier_name'],30) }}</span>
                                            </p>
                                            <p class="text-uppercase text-bold"
                                                style="font-weight: 600;font-size:14px;color:#000">Total Amount <span
                                                    style="font-size:12px;color:#2072AF">{{ $pur_return['net_total_amount'] }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Order
                                    Invoice Number - #{{ $pur_return['purchase_info']['invoice_id'] }} </span><br>
                                <span>Created At -
                                    {{ date('jS M, Y', strtotime($pur_return['return_date'])) }}</span><br><br>

                                <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Order
                                    Status <span
                                        class="custom-badge status-{{ $pur_return['color'] }} badge-border">{{ $pur_return['status_name'] }}</span>
                                </span><br>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-4 _return_product_item_content">
                                <div class="card mb-4" id="purchaseItem_info_table">
                                    <div class="table-responsive">
                                        <table class="table table-stripped" id="data_table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item #</th>
                                                    <th>Description</th>
                                                    <th>QTY</th>
                                                    <th>Unit Price</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="_add_returnproduct_div"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-8 col-xl-8 col-lg-8"></div>
                            <div class="col-12 col-md-4 col-xl-4 col-lg-4">
                                <div class="invoice-total-box">
                                    <div class="invoice-total-inner">
                                        <p class="text-bold" style="font-weight: 600; color:#000">Sub Total <span
                                                style="font-weight: 500; color:#2072AF">{{ number_format($pur_return['sub_total_amount'], 2, '.', '') }}</span>
                                        </p>
                                        <p class="text-bold" style="font-weight: 600; color:#000">Tax Amount <span
                                                style="font-weight: 500; color:#2072AF">{{ number_format($pur_return['tax_amount'], 2, '.', '') }}</span>
                                        </p>
                                        <p class="text-bold" style="font-weight: 600; color:#000">Shipping Amount <span
                                                style="font-weight: 500; color:#2072AF">{{ number_format($pur_return['shipping_amount'], 2, '.', '') }}</span>
                                        </p>
                                        <p class="text-bold" style="font-weight: 600; color:#000">Other Amount <span
                                                style="font-weight: 500; color:#2072AF">{{ number_format($pur_return['other_amount'], 2, '.', '') }}</span>
                                        </p>
                                    </div>
                                    <div class="invoice-total-footer" style="background-color: #2072AF; ">
                                        <h4 style="color:#fff">Net Amount
                                            <span>{{ number_format($pur_return['net_total_amount'], 2, '.', '') }}</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>


                            <div class="col-12 mt-4">

                                <div class="doctor-submit text-end">
                                    @if (Auth::user()->hasPermissionTo('PO_Approval') && $pur_return['status'] == 0)
                                        <button type="button" class="btn btn-primary text-uppercase submit-form me-2"
                                            onclick="change_status({{ $pur_return['id'] }},1)">Approve</button>
                                    @endif

                                    @if (Auth::user()->hasPermissionTo('PO_Hold') && $pur_return['status'] == 1)
                                        <button type="button" class="btn btn-primary text-uppercase submit-form me-2"
                                            onclick="change_status({{ $pur_return['id'] }},2)">On Hold</button>
                                    @endif

                                    @if (Auth::user()->hasPermissionTo('PO_Cancel') && in_array($pur_return['status'], [0, 1, 2]))
                                        <button type="button" class="btn btn-primary text-uppercase submit-form me-2"
                                            onclick="change_status({{ $pur_return['id'] }},3)">Cancel</button>
                                    @endif

                                    @if (Auth::user()->hasPermissionTo('PO_Fullfillment') && ($pur_return['status'] == 1 || $pur_return['status'] == 2))
                                        <button type="button" class="btn btn-primary text-uppercase submit-form me-2"
                                            onclick="change_status({{ $pur_return['id'] }},4)">Fullfillment</button>
                                    @endif

                                    @if (Auth::user()->hasPermissionTo('PO_Received') && $pur_return['status'] == 4)
                                        <button type="button" class="btn btn-primary text-uppercase submit-form me-2"
                                            onclick="change_status({{ $pur_return['id'] }},5)">Received</button>
                                    @endif

                                    @if (Auth::user()->hasPermissionTo('PO_Closed') && $pur_return['status'] == 5)
                                        <button type="button" class="btn btn-primary text-uppercase submit-form me-2"
                                            onclick="change_status({{ $pur_return['id'] }},6)">Close</button>
                                    @endif

                                    <a href="{{ route('business.purchase_return.download_pdf', $pur_return['ref_no']) }}"
                                        target="_blank"
                                        class="btn btn-lg btn-outline-primary submit-form me-2 text-uppercase"><i
                                            class="fa fa-download"></i> Download</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>


@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            loadData()
        });


        function loadData() {
            table = $('#data_table').DataTable({
                "stripeClasses": [],
                pageLength: -1, // Set default length to show all records
                lengthMenu: [
                    [-1],
                    ['All']
                ], // Set options for length menu
                processing: true,
                serverSide: true,
                orderable: false,
                paging: false,
                info: false,
                ajax: {
                    url: "{{ route('business.purchase_return.item_list', ['json' => 1]) }}",
                    data: function(d) {
                        d.return_id = '{{ $pur_return['id'] }}'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product_id',
                        name: 'product_info.product_id',
                        orderable: false,
                    },
                    {
                        data: 'product_name',
                        name: 'product_info.name',
                        orderable: false
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: [3], // Index of the column you want to center (0-based index)
                    className: 'text-center' // Your center class (can be any other class)
                }, {
                    targets: [4, 5], // Index of the column you want to center (0-based index)
                    className: 'text-end' // Your center class (can be any other class)
                }, ],
                rowId: 'id'
            });
        }


        function change_status(pur_return_id, status) {
            var data = {
                'pur_return_id': pur_return_id,
                'status': status,
                "_token": $('input[name=_token]').val(),
            }

            var message = 'Do you want to update the Selected Purchase Return status?'
            if (status == 1) {
                message = 'Do you want to PO Return approve the Selected  Purchase Return status?'
            } else if (status == 2) {
                message = 'Do you want to  PO Return on hold the Selected  Purchase Return status?'
            } else if (status == 3) {
                message = 'Do you want to  PO Return cancel the Selected  Purchase Return status?'
            } else if (status == 4) {
                message = 'Do you want to PO Return full fill the Selected  Purchase Return status?'
            } else if (status == 5) {
                message = 'Do you want to PO Return received the Selected  Purchase Return status?'
            } else if (status == 6) {
                message = 'Do you want to PO Return close the Selected  Purchase Return status?'
            }

            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: message,
                type: 'red',
                autoClose: 'cancel|10000',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-green',
                        action: function() {
                            $("#loader").show();

                            $.ajax({
                                type: "POST",
                                url: "{{ route('business.purchase_return.update_status') }}",
                                data: data,
                                success: function(response) {
                                    $("#loader").hide();

                                    if (response.status == false) {
                                        errorPopup(response.message, response.route)
                                    } else {
                                        successPopup(response.message, response.route)
                                    }
                                },
                                statusCode: {
                                    401: function() {
                                        window.location.href =
                                            '{{ route('login') }}'; //or what ever is your login URI
                                    },
                                    419: function() {
                                        window.location.href =
                                            '{{ route('login') }}'; //or what ever is your login URI
                                    },
                                },
                                error: function(data) {
                                    someThingWrong();
                                }
                            });
                        }
                    },

                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-red',
                        action: function() {

                        }
                    },
                }
            });
        }
    </script>
@endsection
