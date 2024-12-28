@extends('layouts.business')

@section('title')
  Manage Stock Adjusted
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.stock_adjusted') }}">Manage Stock Adjusted</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Add/Delete Stock Adjusted Item</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.stock_adjusted') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h4>Add/Delete Stock Adjusted Item </h4>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="purchase_ref">Purchase Invoice Id</label>
                                    <input type="text" name="purchase_ref" class="form-control" id="purchase_ref"
                                        readonly value="{{ $stock_adjust->purchase_info->invoice_id }}">
                                    <small class="text-danger font-weight-bold err_purchase_ref"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="adjusted_date">Adjusted Date </label>
                                    <input type="text" name="adjusted_date" class="form-control" id="adjusted_date"
                                        readonly value="{{ $stock_adjust->adjusted_date }}">
                                    <small class="text-danger font-weight-bold err_adjusted_date"></small>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-4 _purchase_orderitem_div">
                                <div class="row">
                                    <div class="form-heading">
                                        <h4>Add Stock Adjusted Items</h4>
                                    </div>
                                    <form id="submitForm" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-xl-4 col-lg-3 col-md-4 col-sm-12 col-12">
                                            <div class="input-block local-forms">
                                                <label>Select Product<span class="text-danger">*</span></label>
                                                <select class="form-control my_select2 product" name="product"
                                                    id="product">
                                                    <option value="">Select Product</option>
                                                </select>

                                                <small class="text-danger font-weight-bold err_product"></small>
                                                <small class="text-danger err_product_id"></small>
                                            </div>
                                        </div>

                                        <input type="hidden" name="adjusted_id" id="adjusted_id" value="{{$stock_adjust->id}}">

                                        <div class="col-xl-4 col-lg-3 col-md-4 col-sm-12 col-12">
                                            <div class="input-block local-forms">
                                                <label>Select Warehouse<span class="text-danger">*</span></label>
                                                <select class="form-control my_select2 warehouse" name="warehouse"
                                                    id="warehouse">
                                                    <option value="">Select Warehouse</option>
                                                </select>

                                                <small class="text-danger font-weight-bold err_warehouse"></small>
                                            </div>
                                        </div>

                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12 col-12">
                                            <div class="input-block local-forms ">
                                                <label>Qty<span class="text-danger">*</span></label>
                                                <input type="hidden" name="av_qty" id="av_qty">
                                                <input type="hidden" name="order_item_id" id="order_item_id">
                                                <input type="text" name="qty" class="form-control qty"
                                                    value="{{ old('qty') }}" id="qty" placeholder="Enter the Qty">
                                                <small class="text-danger font-weight-bold err_qty"></small>
                                            </div>
                                        </div>

                                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 mb-4">
                                            <button class="btn btn-lg btn-secondary text-uppercase btn-bottom"
                                                type="submit" id="add_button">+</button>
                                        </div>
                                    </form>

                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
                                        <small class="text-danger font-weight-bold err_product_ids"></small>
                                    </div>

                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
                                        <div class="table-responsive">
                                            <table class="table table-stripped " id="data_table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Product Name</th>
                                                        <th>Warehouse Name</th>
                                                        <th>Qty</th>
                                                        <th class="text-end"></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="_add_product_div"></tbody>
                                            </table>
                                        </div>
                                    </div>

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
        var table;

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $(document).ready(function() {
                $('.my_select2').select2()
            });

            loadAvailableProducts()
            loadData()
        })

        function loadAvailableProducts() {
            $('#loader').show()

            var data = {
                'order_id': '{{ $stock_adjust->purchased_id }}'
            }

            $.ajax({
                type: "GET",
                url: "{{ route('business.stock_adjusted.product_list') }}",
                data: data,
                dataType: "JSON",
                success: function(response) {
                    $('#loader').hide()
                    $('.product').html('')
                    $('.product').append('<option value="">Select Product</option>');
                    $('#qty').val('');
                    $.each(response.product, function(key, item) {
                        $('.product').append('<option value="' + item.id + '">' + item.name +
                            '</option>');
                    });

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

        function loadData() {
            table = $('#data_table').DataTable({
                "stripeClasses": [],
                "lengthMenu": [25, 50, 100],
                "pageLength": 25,
                processing: true,
                serverSide: true,
                orderable: false,
                ajax: {
                    url: "{{ route('business.stock_adjusted.get_item_list', ['json' => 1]) }}",
                    data: function(d) {
                        d.adjust_id = '{{ $stock_adjust->id }}'
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
                        name: 'Product_info.name',
                        orderable: false,
                    },
                    {
                        data: 'warehouse_name',
                        name: 'warehouse_info.name',
                        orderable: false,
                    },
                    {
                        data: 'qty',
                        name: 'qty',
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

        $('#product').change(function(e) {
            e.preventDefault();

            if ($(this).val() != '') {
                var data = {
                    'product_id': $(this).val(),
                    'order_id': '{{ $stock_adjust->purchased_id }}'
                }

                $('#loader').show()
                $.ajax({
                    type: "POST",
                    url: "{{ route('stock_adjusted.get_ware_house') }}",
                    data: data,
                    dataType: "JSON",
                    success: function(response) {
                        console.log(response);
                        $('#loader').hide()
                        $('.warehouse').html('')
                        $('.warehouse').append('<option value="">Select Warehouse</option>');
                        $.each(response.data.ware_house, function(key, item) {
                            $('.warehouse').append('<option value="' + item.id + '">' + item
                                .name +
                                '</option>');
                        });
                        $('#qty').val(0)
                        $('#qty').val(response.data.qty)

                        $('#av_qty').val(0)
                        $('#av_qty').val(response.data.qty)

                        $('#order_item_id').val('')
                        $('#order_item_id').val(response.data.order_item_id)

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
        });

        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('business.stock_adjusted.add_item') }}",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $("#loader").hide();
                    errorClear()
                    if (response.status == false) {
                        $.each(response.message, function(key, item) {
                            if (key) {
                                $('.err_' + key).text(item)
                            }
                        });
                    } else {
                        successPopup(response.message, '')
                        loadAvailableProducts()
                        table.clear();
                        table.ajax.reload();
                        table.draw();
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

        function errorClear()
        {
            $('.err_product').text('')
            $('.err_warehouse').text('')
            $('.err_qty').text('')
        }

        function delete_confirmation(id)
        {
            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to Delete the Selected Stock Adjusted?',
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
                                url: "{{ route('business.stock_adjusted.delete_item') }}",
                                data: data,
                                success: function(response) {
                                    $("#loader").hide();
                                    table.clear();
                                    table.ajax.reload();
                                    table.draw();
                                    loadAvailableProducts()
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
