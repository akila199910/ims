@extends('layouts.business')

@section('title')
Manage Purchase Orders
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.purchaseorder') }}">Manage Purchase Orders</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Update Purchase Order</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.purchaseorder') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h4>Update Purchase Order</h4>
                                </div>
                            </div>
                            <input type="hidden" name="id" id="id" value="{{ $purchase['id'] }}">
                            <div class="col-sm-6">
                                <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Invoice To
                                </span><br>
                                <span style="font-size: 14px">{{ Str::limit($purchase['supplier_info']['supplier_name'],30) }}</span><br>
                                <span
                                    style="font-size: 14px">{{ $purchase['supplier_info']['supplier_address'] }}</span><br>
                                <span style="font-size: 14px">{{ Str::limit($purchase['supplier_info']['supplier_email'],30) }}</span><br>
                                <span style="font-size: 14px">{{ $purchase['supplier_info']['supplier_contact'] }}</span>
                            </div>

                            <div class="col-sm-6">
                                <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Order
                                    Invoice Number - #{{ $purchase['invoice_id'] }} </span><br>
                                <span>Created At -
                                    {{ date('jS M, Y', strtotime($purchase['purchased_date'])) }}</span><br><br>

                                <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Order
                                    Status <span
                                        class="custom-badge status-{{ $purchase['color'] }} badge-border">{{ $purchase['status_name'] }}</span>
                                </span><br>

                            </div>

                            <div class="col-sm-12 mt-4">
                                <h4>Ordered Items</h4>
                            </div>

                            <div class="card mb-4" id="purchaseItem_info_table">
                                <div class="table-responsive">
                                    <table class="table table-stripped " id="data_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Item #</th>
                                                <th>Description</th>
                                                <th>Qty</th>
                                                <th>Unit Price</th>
                                                <th>Received Qty</th>
                                                <th>Total Amount</th>
                                                <th class="text-end"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="_add_product_div"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-12 col-md-9 col-xl-9 col-lg-9"></div>
                            <div class="col-12 col-md-3 col-xl-3 col-lg-3">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-block local-forms ">
                                            <label>Sub Total</label>
                                            <input type="text" readonly name="sub_total_amount"
                                                class="form-control sub_total_amount text-end" style="font-weight: 600"
                                                id="sub_total_amount"
                                                value="{{ number_format($purchase['total_amount'], 2, '.', '') }}">
                                            <small class="text-danger font-weight-bold err_sub_total_amount"></small>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-block local-forms ">
                                            <label>Tax Amount</label>
                                            <input type="text" name="tax_amount"
                                                class="form-control tax_amount decimal_val text-end" id="tax_amount"
                                                 value="{{ number_format($purchase['tax_amount'], 2, '.', '') }}">
                                            <small class="text-danger font-weight-bold err_tax_amount"></small>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-block local-forms ">
                                            <label>Shipping Amount</label>
                                            <input type="text" name="shipping_amount"
                                                class="form-control shipping_amount decimal_val text-end"
                                                id="shipping_amount"
                                                value="{{ number_format($purchase['shipping_amount'], 2, '.', '') }}">
                                            <small class="text-danger font-weight-bold err_shipping_amount"></small>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-block local-forms ">
                                            <label>Other Amount</label>
                                            <input type="text" name="other_amount"
                                                class="form-control other_amount decimal_val text-end" id="other_amount"
                                                value="{{ number_format($purchase['other_amount'], 2, '.', '') }}">
                                            <small class="text-danger font-weight-bold err_other_amount"></small>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="input-block local-forms ">
                                            <label>Net Amount</label>
                                            <input type="text" readonly name="net_total_amount"
                                                class="form-control net_total_amount decimal_val text-end"
                                                style="font-weight: 600" id="net_total_amount"
                                                value="{{ number_format($purchase['final_amount'], 2, '.', '') }}">
                                            <small class="text-danger font-weight-bold err_net_total_amount"></small>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="input-block local-forms ">
                                            <label>Paid Amount</label>
                                            <input type="text" readonly name="paid_amount"
                                                class="form-control paid_amount decimal_val text-end"
                                                style="font-weight: 600" id="paid_amount"
                                                value="{{ number_format($purchase['paid_amount'], 2, '.', '') }}">
                                            <small class="text-danger font-weight-bold err_paid_amount"></small>
                                        </div>
                                    </div>

                                    @php
                                        $due_amount = $purchase['final_amount'] - $purchase['paid_amount'];

                                        $due_amount = $due_amount > 0 ? $due_amount : 0;
                                    @endphp

                                    <div class="col-sm-12">
                                        <div class="input-block local-forms ">
                                            <label>Due Amount</label>
                                            <input type="text" readonly name="due_amount"
                                                class="form-control due_amount decimal_val text-end"
                                                style="font-weight: 600" id="due_amount"
                                                value="{{ number_format($due_amount, 2, '.', '') }}">
                                            <small class="text-danger font-weight-bold err_due_amount"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-12">
                                <input type="hidden" value="5" name="status">
                                <div class="doctor-submit text-end">
                                    <button type="submit" class="btn btn-primary text-uppercase submit-form me-2">Update
                                        & Received</button>

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

            $('#data_table').on('keypress', '.decimal_val', function(event) {
                // Allow: backspace, delete, tab, escape, enter, and .
                if (event.keyCode === 8 || event.keyCode === 46 || event.keyCode === 9 || event.keyCode ===
                    27 || event.keyCode === 13 || event.key === '.') {
                    return;
                }
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                if ((event.ctrlKey || event.metaKey) && (event.keyCode === 65 || event.keyCode === 67 ||
                        event.keyCode === 86 || event.keyCode === 88)) {
                    return;
                }
                // Ensure that it is a number and stop the keypress if not a number
                if ((event.keyCode < 48 || event.keyCode > 57)) {
                    event.preventDefault();
                }
            });

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
                    url: "{{ route('business.purchaseorder.get_order_items.list', ['json' => 1]) }}",
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
                        data: 'received_qty',
                        name: 'received_qty',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                rowId: 'id'
            });
        }

        function loadSubTotal() {

            var data = {
                'order_id': '{{ $purchase['id'] }}'
            }

            $.ajax({
                type: "POST",
                url: "{{ route('business.purchaseorder.product_subtotal') }}",
                data: data,
                dataType: "JSON",
                success: function(response) {
                    var total_amount = parseFloat(response.total_amount);
                    $('#sub_total_amount').val(total_amount.toFixed(2))
                    calculate_net_total()
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

        function calculate_net_total() {
            var sub_total_amount = parseFloat($('#sub_total_amount').val())
            var tax_amount = parseFloat($('#tax_amount').val())
            var shipping_amount = parseFloat($('#shipping_amount').val())
            var other_amount = parseFloat($('#other_amount').val())

            var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
            $('#net_total_amount').val(net_total_amount.toFixed(2))
        }

        $('#tax_amount').keyup(function(e) {
            var key_amount = $(this).val();

            if (key_amount == "" || key_amount == 0) {
                var amount = parseFloat(0);
            } else {
                var amount = parseFloat(key_amount);
            }

            var sub_total_amount = parseFloat($('#sub_total_amount').val())
            var tax_amount = parseFloat(amount)
            var shipping_amount = parseFloat($('#shipping_amount').val())
            var other_amount = parseFloat($('#other_amount').val())

            var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
            $('#net_total_amount').val(net_total_amount.toFixed(2))
        });

        $('#shipping_amount').keyup(function(e) {
            var key_amount = $(this).val();

            if (key_amount == "" || key_amount == 0) {
                var amount = parseFloat(0);
            } else {
                var amount = parseFloat(key_amount);
            }

            var sub_total_amount = parseFloat($('#sub_total_amount').val())
            var tax_amount = parseFloat($('#tax_amount').val())
            var shipping_amount = parseFloat(amount)
            var other_amount = parseFloat($('#other_amount').val())

            var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
            $('#net_total_amount').val(net_total_amount.toFixed(2))
        });

        $('#other_amount').keyup(function(e) {
            var key_amount = $(this).val();

            if (key_amount == "" || key_amount == 0) {
                var amount = parseFloat(0);
            } else {
                var amount = parseFloat(key_amount);
            }

            var sub_total_amount = parseFloat($('#sub_total_amount').val())
            var tax_amount = parseFloat($('#tax_amount').val())
            var shipping_amount = parseFloat($('#shipping_amount').val())
            var other_amount = parseFloat(amount)

            var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
            $('#net_total_amount').val(net_total_amount.toFixed(2))
        });

        $(document).on('keyup', '.received_qty', function(e) {
            e.preventDefault()

            var key_value = $(this).val();
            var request_qty = $(this).closest('tr').find('.request_qty').val()
            request_qty = parseInt(request_qty)

            if (key_value == "" || key_value == 0) {
                var qty = parseInt(0);
                $(this).closest('tr').find('.received_qty').val(qty)
            } else {
                if (key_value > request_qty) {
                    var qty = parseInt(request_qty);
                    $(this).closest('tr').find('.received_qty').val(qty)
                } else {
                    var qty = parseInt(key_value);
                }
            }

            var unit_price = $(this).closest('tr').find('.retail_price').val()
            var total_price = $(this).closest('tr').find('.total_price').val()
            // row_total_price

            unit_price = parseFloat(unit_price)
            total_price = parseFloat(total_price)

            var sub_total_amount = parseFloat($('#sub_total_amount').val())
            sub_total_amount = parseFloat(sub_total_amount - total_price)

            var new_total_price = parseFloat(qty * unit_price);
            sub_total_amount = parseFloat(sub_total_amount + new_total_price)
            $(this).closest('tr').find('.total_price').val(new_total_price.toFixed(2))
            $(this).closest('tr').find('.row_total_price').text(new_total_price.toFixed(2))

            var tax_amount = parseFloat($('#tax_amount').val())
            var shipping_amount = parseFloat($('#shipping_amount').val())
            var other_amount = parseFloat($('#other_amount').val())

            var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
            $('#net_total_amount').val(net_total_amount.toFixed(2))
            $('.net_total_amount').text(net_total_amount.toFixed(2))
            $('#sub_total_amount').val(sub_total_amount.toFixed(2))
        });

        $(document).on('click', '.received_qty', function(e) {
            e.preventDefault()

            var key_value = $(this).val();
            var request_qty = $(this).closest('tr').find('.request_qty').val()
            request_qty = parseInt(request_qty)


            if (key_value == "" || key_value == 0) {
                var qty = parseInt(0);
                $(this).closest('tr').find('.received_qty').val(qty)
            } else {
                if (key_value > request_qty) {
                    var qty = parseInt(request_qty);
                    $(this).closest('tr').find('.received_qty').val(qty)
                } else {
                    var qty = parseInt(key_value);
                }
            }

            var unit_price = $(this).closest('tr').find('.retail_price').val()
            var total_price = $(this).closest('tr').find('.total_price').val()
            // row_total_price

            unit_price = parseFloat(unit_price)
            total_price = parseFloat(total_price)

            var sub_total_amount = parseFloat($('#sub_total_amount').val())
            sub_total_amount = parseFloat(sub_total_amount - total_price)

            var new_total_price = parseFloat(qty * unit_price);
            sub_total_amount = parseFloat(sub_total_amount + new_total_price)
            $(this).closest('tr').find('.total_price').val(new_total_price.toFixed(2))
            $(this).closest('tr').find('.row_total_price').text(new_total_price.toFixed(2))

            var tax_amount = parseFloat($('#tax_amount').val())
            var shipping_amount = parseFloat($('#shipping_amount').val())
            var other_amount = parseFloat($('#other_amount').val())

            var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
            $('#net_total_amount').val(net_total_amount.toFixed(2))
            $('.net_total_amount').text(net_total_amount.toFixed(2))
            $('#sub_total_amount').val(sub_total_amount.toFixed(2))
        });

        $(document).on('keyup', '.retail_price', function(e) {
            e.preventDefault()

            var key_value = $(this).val();

            if (key_value == "" || key_value == 0) {
                var unit_price = parseFloat(0);
                $(this).closest('tr').find('.retail_price').val(unit_price.toFixed(2))
            } else {
                var unit_price = parseFloat(key_value);
            }

            var qty = $(this).closest('tr').find('.received_qty').val()
            var total_price = $(this).closest('tr').find('.total_price').val()
            // row_total_price

            unit_price = parseFloat(unit_price)
            total_price = parseFloat(total_price)

            var sub_total_amount = parseFloat($('#sub_total_amount').val())
            sub_total_amount = parseFloat(sub_total_amount - total_price)

            var new_total_price = parseFloat(qty * unit_price);
            sub_total_amount = parseFloat(sub_total_amount + new_total_price)
            $(this).closest('tr').find('.total_price').val(new_total_price.toFixed(2))
            $(this).closest('tr').find('.row_total_price').text(new_total_price.toFixed(2))

            var tax_amount = parseFloat($('#tax_amount').val())
            var shipping_amount = parseFloat($('#shipping_amount').val())
            var other_amount = parseFloat($('#other_amount').val())

            var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
            $('#net_total_amount').val(net_total_amount.toFixed(2))
            $('.net_total_amount').text(net_total_amount.toFixed(2))
            $('#sub_total_amount').val(sub_total_amount.toFixed(2))
        });

        function delete_confirmation(id) {
            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to Delete the Selected Purchase Orders Item?',
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
                                url: "{{ route('business.purchaseorder.delete_item') }}",
                                data: data,
                                success: function(response) {
                                    $("#loader").hide();
                                    loadSubTotal()
                                    table.clear();
                                    table.ajax.reload();
                                    table.draw();
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

         $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('business.purchaseorder.update') }}",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $("#loader").hide();
                    // errorClear()
                    if (response.status == false) {
                        $.each(response.message, function(key, item) {
                            if (key) {
                                $('.err_' + key).text(item)
                                $('#' + key).addClass('is-invalid');
                            }
                        });
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
        });
    </script>
@endsection
