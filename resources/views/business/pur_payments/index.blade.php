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
                    <li class="breadcrumb-item active">Payment Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.purchaseorder') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                <h4>Purchase Order Payment details</h4>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id" value="{{ $purchase['id'] }}">
                        <div class="col-12 col-md-6">
                            <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Invoice To
                            </span><br>
                            <span style="font-size: 14px">{{ Str::limit($purchase['supplier_info']['supplier_name'],30) }}</span><br>
                            <span style="font-size: 14px">{{ Str::limit($purchase['supplier_info']['supplier_address'],30) }}</span><br>
                            <span style="font-size: 14px">{{ Str::limit($purchase['supplier_info']['supplier_email'],30) }}</span><br>
                            <span style="font-size: 14px">{{ $purchase['supplier_info']['supplier_contact'] }}</span>
                            <br>
                            <br>
                        </div>

                        <div class="col-sm-6">
                            <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Order
                                Invoice Number - #{{ $purchase['invoice_id'] }} </span><br>
                            <span>Purchased At -
                                {{ date('jS M, Y', strtotime($purchase['purchased_date'])) }}</span><br><br>
                        </div>

                        <div class="col-12 col-md-8 col-xl-8 col-lg-8"></div>
                        <div class="col-12 col-md-4 col-xl-4 col-lg-4">
                            <div class="invoice-total-box">
                                <div class="invoice-total-footer" style="background-color: #2072AF; ">
                                    <h4 style="color:#fff">Total Amount
                                        <span>{{ number_format($purchase['final_amount'], 2, '.', '') }}</span>
                                    </h4>
                                </div>
                            </div>

                            <div class="invoice-total-box">
                                <div class="invoice-total-inner">
                                    <p class="text-bold" style="font-weight: 600; color:#000">Paid Amount
                                        <span id="paid_amount"
                                            style="font-weight: 500; color:#2072AF">{{ number_format($purchase['paid_amount'], 2, '.', '') }}</span>
                                    </p>
                                </div>
                                <div class="invoice-total-footer" style="background-color: #2072AF; ">
                                    <h4 style="color:#fff">Due Amount
                                        <span
                                            id="due_amount">{{ number_format($purchase['due_amount'], 2, '.', '') }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-xl-12 col-lg-12 mt-4 _add_payment_button">
                            @if (Auth::user()->hasPermissionTo('Create_Payement') && $purchase['due_amount'] > 0)
                                <a href="javascript:;" onclick="open_create_model()" class="btn btn-primary ms-2">
                                    +&nbsp;Add New Payment
                                </a>
                            @endif
                        </div>

                        <div class="col-12 col-md-12 col-xl-12 col-lg-12 mt-4">
                            <div class="table-responsive">
                                <table class="table table-stripped " id="data_table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Payment Ref No.</th>
                                            <th>Payment Type</th>
                                            <th>Paid Date</th>
                                            <th>Paid Amount</th>
                                            <th>Scan Doc</th>
                                            <th class="text-end"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Add Payment model -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h4 id="offcanvasRightLabel" class="text-uppercase">[Add/Update Payment]</h4>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body _add_update_body">
            <!-- Dynamic body will show here -->
        </div>
    </div>
    <!-- END ------------------------------>
@endsection

@section('scripts')
    <script>
        var table;
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            loadData()
            loadPaidDueAmount()
        });

        function loadData() {
            table = $('#data_table').DataTable({
                "stripeClasses": [],
                "lengthMenu": [10, 20, 50],
                "pageLength": 10,
                processing: true,
                serverSide: true,
                orderable: false,
                ajax: {
                    url: "{{ route('business.purchaseorder.payments.list', ['json' => 1]) }}",
                    data: function(d) {
                        d.purchased_id = '{{ $purchase['id'] }}'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'payment_reference',
                        name: 'payment_reference',
                        orderable: false,
                    },
                    {
                        data: 'payment_type',
                        name: 'payment_type_info.payment_type',
                        orderable: false
                    },
                    {
                        data: 'payment_date',
                        name: 'payment_date',
                        orderable: false
                    },
                    {
                        data: 'paid_amount',
                        name: 'paid_amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'scan_doc',
                        name: 'scan_doc',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        }

        function loadPaidDueAmount() {
            var data = {
                'purchase_id': '{{ $purchase['id'] }}'
            }

            $.ajax({
                type: "POST",
                url: "{{ route('business.purchaseorder.payments.load_paid_due') }}",
                data: data,
                dataType: 'JSON',
                success: function(response) {
                    $("#loader").hide();
                    $('#paid_amount').text(response.paid_amount)
                    $('#due_amount').text(response.due_amount)
                    var due_amount = parseFloat(response.due_amount)

                    $('._create_button').hide()
                    $('._add_payment_button').hide()
                    if (response.due_amount > 0) {
                        $('._create_button').show()
                        $('._add_payment_button').show()
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

        function deleteConfirmation(id) {
            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to Delete the Selected payment?',
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
                                url: "{{ route('business.purchaseorder.payments.delete') }}",
                                data: data,
                                success: function(response) {
                                    $("#loader").hide();
                                    table.clear();
                                    table.ajax.reload();
                                    table.draw();
                                    loadPaidDueAmount()
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

        function open_create_model() {
            $('#loader').show()

            var data = {
                'purchased_id': '{{ $purchase['id'] }}'
            }

            $('#offcanvasRightLabel').text('Add New Payment')

            $.ajax({
                type: "GET",
                url: "{{ route('business.purchaseorder.payments.create.form') }}",
                data: data,
                success: function(response) {
                    $('#loader').hide()
                    $('._add_update_body').html('')
                    $('._add_update_body').html(response)

                    var myOffcanvas = document.getElementById('offcanvasRight');
                    var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);
                    bsOffcanvas.show();
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

        function openUpdateModal(id) {
            $("#loader").show();
            $('#offcanvasRightLabel').text('Update Payment')
            var data = {
                "_token": $('input[name=_token]').val(),
                "id": id,
            }
            $.ajax({
                type: "GET",
                url: "{{ route('business.purchaseorder.payments.update.form') }}",
                data: data,
                success: function(response) {
                    $("#loader").hide();

                    if (response.status == false) {
                        errorPopup(response.message, response.route)
                    } else {
                        $('._add_update_body').html('')
                        $('._add_update_body').html(response)

                        var myOffcanvas = document.getElementById('offcanvasRight');
                        var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);
                        bsOffcanvas.show();
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
    </script>
@endsection
