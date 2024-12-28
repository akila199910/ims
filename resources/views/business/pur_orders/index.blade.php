@extends('layouts.business')

@section('title')
Manage Purchase Orders
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;">Manage Purchase Orders</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <div class="page-table-header mb-2">
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <div class="doctor-table-blk">
                                    <h3 class="text-uppercase">Purchase Orders</h3>
                                </div>
                            </div>
                            <div class="col-auto text-end float-end ms-auto download-grp">
                                @if (Auth::user()->hasPermissionTo('Create_PurchaseOrder'))
                                    <a href="{{ route('business.purchaseorder.create.form') }}"
                                        class="btn btn-primary ms-2">
                                        +&nbsp;New Purchase Order
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="staff-search-table">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ml-2 mr-2">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms ">
                                        <label>Vendors</label>
                                        <select class="form-control select2 supplier" name="supplier" id="supplier">
                                            <option value="">All Vendors</option>
                                            @foreach ($suppliers as $item)
                                                <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms ">
                                        <label>Select status </label>
                                        <select class="form-control select2" id="status" name="status">
                                            <option value="">Status</option>
                                            <option value="0">Pending</option>
                                            <option value="1">Approved</option>
                                            <option value="2">On Hold</option>
                                            <option value="3">Cancelled</option>
                                            <option value="4">Full Filled</option>
                                            <option value="5">Received</option>
                                            <option value="6">Closed</option>
                                        </select>
                                        <small class="text-danger font-weight-bold err_status"></small>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 "></div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 text-end">
                                    <button type="button" class="btn btn-primary btn-lg" style="width: 100px;" id="reset_button">RESET</button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-stripped " id="data_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purchase No.</th>
                                    <th>Vendor Name</th>
                                    <th>Request Date</th>
                                    <th>Order By</th>
                                    <th>Modify By</th>
                                    <th>Status</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>
                        </table>
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
            loadData()
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
                    url: "{{ route('business.purchaseorder', ['json' => 1]) }}",
                    data: function(d) {
                        d.supplier_id = $('#supplier').val(),
                            d.status = $('#status').val(),
                            d.key_word = $('#key_word').val()
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_id',
                        name: 'invoice_id',
                        orderable: false,
                    },
                    {
                        data: 'supplier',
                        name: 'supplier_Info.name',
                        orderable: false,
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                    },
                    {
                        data: 'order_by',
                        name: 'order_user_info.name',
                        orderable: false,
                    },
                    {
                        data: 'modify_by',
                        name: 'modify_user_info.name',
                        orderable: false,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        }

        $('#supplier').change(function(e) {
            e.preventDefault();

            table.clear();
            table.ajax.reload();
            table.draw();

        });

        $('#status').change(function(e) {
            e.preventDefault();

            table.clear();
            table.ajax.reload();
            table.draw();

        });

        $('#key_word').keyup(function(e) {
            //reload the table
            table.clear();
            table.ajax.reload();
            table.draw();
        });


        $('#reset_button').click(function(e) {
            e.preventDefault();

            $('#key_word').val('')
            $('#supplier').val('').change()
            $('#status').val('').change()

        });


        function deleteConfirmation(id) {
            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to Delete the Selected Purchase Orders?',
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
                                url: "{{ route('business.purchaseorder.delete') }}",
                                data: data,
                                success: function(response) {
                                    $("#loader").hide();
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

        function change_status(purchase_id, status) {
            var data = {
                'purchase_id': purchase_id,
                'status': status,
                "_token": $('input[name=_token]').val(),
            }

            var message = 'Do you want to update the Selected Purchase Orders status?'
            if (status == 1) {
                message = 'Do you want to approve the Selected Purchase Orders status?'
            } else if (status == 2) {
                message = 'Do you want to on hold the Selected Purchase Orders status?'
            } else if (status == 3) {
                message = 'Do you want to cancel the Selected Purchase Orders status?'
            } else if (status == 4) {
                message = 'Do you want to full fill the Selected Purchase Orders status?'
            } else if (status == 5) {
                message = 'Do you want to received the Selected Purchase Orders status?'
            } else if (status == 6) {
                message = 'Do you want to close the Selected Purchase Orders status?'
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
                                url: "{{ route('business.purchaseorder.update_status') }}",
                                data: data,
                                success: function(response) {
                                    $("#loader").hide();

                                    if (response.status == false) {
                                        errorPopup(response.message, '')
                                    } else {
                                        successPopup(response.message, '')
                                    }

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

        function send_mail(id) {
            $("#loader").show();
            var data = {
                "_token": $('input[name=_token]').val(),
                "id": id,
            }
            $.ajax({
                type: "POST",
                url: "{{ route('business.purchaseorder.send_mail') }}",
                data: data,
                success: function(response) {
                    $("#loader").hide();
                    successPopup(response.message, '')
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

        function re_order(id) {
            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to reorder the Selected Purchase Orders?',
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
                                    successPopup(response.message, '')
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

        function open_create_model(id)
        {
            $('#loader').show()

            var data = {
                'purchased_id': id,
                'view' : 'purchase_view'
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
    </script>
@endsection
