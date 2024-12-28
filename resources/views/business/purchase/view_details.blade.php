@extends('layouts.business')

@section('title')
    Manage Purchases
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.purchases') }}"> Manage Purchases</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Purchase Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.purchases') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="col-12">
                            <div class="form-heading">
                                <h4>Purchase Details</h4>
                            </div>
                        </div>

                        <input type="hidden" name="id" id="id" class="form-control id" readonly
                            value="{{ $purchase['id'] }}">

                        <div class="col-12 col-md-6">
                            <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Invoice To
                            </span><br>
                            <span style="font-size: 14px">{{ Str::limit($purchase['supplier_info']['supplier_name'],30) }}</span><br>
                            <span style="font-size: 14px">{{ $purchase['supplier_info']['supplier_address'] }}</span><br>
                            <span style="font-size: 14px">{{ Str::limit($purchase['supplier_info']['supplier_email'],30) }}</span><br>
                            <span style="font-size: 14px">{{ $purchase['supplier_info']['supplier_contact'] }}</span>
                            <br>
                            <br>
                            <div class="col-12 col-md-12 col-xl-12 col-lg-12">
                                <div class="invoice-total-box invoice-top">
                                    <div class="invoice-total-inner">
                                        <p class="text-uppercase text-bold"
                                            style="font-weight: 600;font-size:14px;color:#000">Purchased Date <span
                                                style="font-size:12px;color:#2072AF">{{ $purchase['purchased_date'] }}</span>
                                            </span>
                                        </p>
                                        <p class="text-uppercase text-bold"
                                            style="font-weight: 600;font-size:14px;color:#000">Ordered By<span
                                                style="font-size:12px;color:#2072AF">{{ Str::limit($purchase['ordered_by'],30) }}</span>
                                            </span>
                                        </p>
                                        <p class="text-uppercase text-bold"
                                            style="font-weight: 600;font-size:14px;color:#000">Approved
                                            By <span
                                                style="font-size:12px;color:#2072AF">{{ number_format($purchase['shipping_amount'], 2, '.', '') }}</span>
                                        </p>
                                        <p class="text-uppercase text-bold"
                                            style="font-weight: 600;font-size:14px;color:#000">Vendor Name <span
                                                style="font-size:12px;color:#2072AF">{{ Str::limit($purchase['supplier_name'],30) }}</span>
                                        </p>
                                        <p class="text-uppercase text-bold"
                                            style="font-weight: 600;font-size:14px;color:#000">Total Amount <span
                                                style="font-size:12px;color:#2072AF">{{ $purchase['final_amount'] }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Order
                                Invoice Number - #{{ $purchase['invoice_id'] }} </span><br>
                            <span>Created At -
                                {{ date('jS M, Y', strtotime($purchase['purchased_date'])) }}</span><br><br>

                            <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Order
                                Status <span
                                    class="badge badge-soft-success badge-border">{{ $purchase['status_name'] }}</span>
                            </span><br>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-4 _product_item_content">
                            <div class="card mb-4" id="purchaseItem_info_table">
                                <div class="table-responsive">
                                    <table class="table table-stripped " id="data_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product Name</th>
                                                <th>Category Name</th>
                                                <th>Subcategory Date</th>
                                                <th>Unit Name</th>
                                                <th>Unit Price</th>
                                                <th>Qty</th>
                                                <th>Received Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody class="_add_product_div"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-8 col-xl-8 col-lg-8"></div>
                        <div class="col-12 col-md-4 col-xl-4 col-lg-4">
                            <div class="invoice-total-box">
                                <div class="invoice-total-inner">
                                    <p class="text-bold" style="font-weight: 600; color:#000">Sub Total <span
                                            style="font-weight: 500; color:#2072AF">{{ number_format($purchase['total_amount'], 2, '.', '') }}</span>
                                    </p>
                                    <p class="text-bold" style="font-weight: 600; color:#000">Tax Amount <span
                                            style="font-weight: 500; color:#2072AF">{{ number_format($purchase['tax_amount'], 2, '.', '') }}</span>
                                    </p>
                                    <p class="text-bold" style="font-weight: 600; color:#000">Shipping Amount <span
                                            style="font-weight: 500; color:#2072AF">{{ number_format($purchase['shipping_amount'], 2, '.', '') }}</span>
                                    </p>
                                    <p class="text-bold" style="font-weight: 600; color:#000">Other Amount <span
                                            style="font-weight: 500; color:#2072AF">{{ number_format($purchase['shipping_amount'], 2, '.', '') }}</span>
                                    </p>
                                </div>
                                <div class="invoice-total-footer" style="background-color: #2072AF; ">
                                    <h4 style="color:#fff">Net Amount
                                        <span>{{ number_format($purchase['final_amount'], 2, '.', '') }}</span></h4>
                                </div>
                            </div>

                            <div class="invoice-total-box">
                                <div class="invoice-total-inner">
                                    <p class="text-bold" style="font-weight: 600; color:#000">Paid Amount <span
                                            style="font-weight: 500; color:#2072AF">{{ number_format($purchase['paid_amount'], 2, '.', '') }}</span>
                                    </p>
                                </div>
                                <div class="invoice-total-footer" style="background-color: #2072AF; ">
                                    <h4 style="color:#fff">Due Amount
                                        <span>{{ number_format($purchase['due_amount'], 2, '.', '') }}</span></h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-4">
                            <div class="doctor-submit text-end">
                                <button type="button" class="btn btn-primary text-uppercase submit-form me-2"
                                    onclick="re_order({{ $purchase['id'] }})">Re order</button>

                                <a href="{{ route('business.purchaseorder.download_pdf', $purchase['ref_no']) }}"
                                    target="_blank"
                                    class="btn btn-lg btn-outline-primary submit-form me-2 text-uppercase"><i
                                        class="fa fa-download"></i> Download</a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
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
                "lengthMenu": [50, 100, 150],
                "pageLength": 50,
                processing: true,
                serverSide: true,
                orderable: false,
                ajax: {
                    url: "{{ route('business.purchases.item_list', ['json' => 1]) }}",
                    data: function(d) {
                        d.order_id = '{{ $purchase['id'] }}'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product_name',
                        name: 'product_info.name',
                        orderable: false,
                    },
                    {
                        data: 'category_name',
                        name: 'category_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sub_category_name',
                        name: 'sub_category_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'unit_name',
                        name: 'unit_name',
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
                        data: 'qty',
                        name: 'qty',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'received_qty',
                        name: 'received_qty',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: [6, 7], // Index of the column you want to center (0-based index)
                    className: 'text-center' // Your center class (can be any other class)
                }, ],
                rowId: 'id'
            });
        }

        function re_order(id) {
            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to reorder the purchase?',
                type: 'red',
                autoClose: 'cancel|10000',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-green',
                        action: function() {
                            $("#loader").show();
                            var data = {
                                "_token": $('input[name=_token]').val(),
                                "id": id,
                            }
                            $.ajax({
                                type: "POST",
                                url: "{{ route('business.purchases.re_order') }}",
                                data: data,
                                success: function(response) {
                                    $("#loader").hide();
                                    successPopup(response.message, response.route)
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
