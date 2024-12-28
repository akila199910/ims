@extends('layouts.business')

@section('title')
   Manage Stock Transfers
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.stock_transfer') }}">Manage Stock Transfers</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Add new Stock Transfer</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.stock_transfer') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h4>Add new Stock Transfer</h4>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="warehouse_from">Transfer From<span class="text-danger">*</span> </label>
                                    <select name="warehouse_from" id="warehouse_from"
                                        class="form-control select2 warehouse_from">
                                        <option value="">Select From Warehouse</option>
                                        @foreach ($ware_house as $item)
                                            <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_warehouse_from"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="warehouse_to">Transfer To<span class="text-danger">*</span> </label>
                                    <select name="warehouse_to" id="warehouse_to" class="form-control select2 warehouse_to">
                                    </select>
                                    <small class="text-danger font-weight-bold err_warehouse_to"></small>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 _stock_transfer_item_div">

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
        });

        $('#warehouse_from').change(function(e) {
            e.preventDefault();
            var from_id = $(this).val()

            var data = {
                'from_id': from_id
            }

            $('#loader').show()

            $.ajax({
                type: "GET",
                url: "{{ route('business.stock_transfer.get_warehouse') }}",
                data: data,
                dataType: "JSON",
                success: function(response) {
                    $('#loader').hide()
                    $('#warehouse_to').html('')
                    $('#warehouse_to').append('<option value="">Select To Warehouse</option>');
                    $.each(response.data, function(key, item) {
                        $('#warehouse_to').append('<option value="' + item.id + '">' + item
                            .name + '</option>');
                    });
                    $('._stock_transfer_item_div').html('')
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

        $('#warehouse_to').change(function(e) {
            e.preventDefault();
            var from_id = $('#warehouse_from').val()
            var to_id = $(this).val()

            var data = {
                'from_id': from_id,
                'to_id': to_id
            }

            $('#loader').show()

            $.ajax({
                type: "GET",
                url: "{{ route('business.stock_transfer.get_trasnfer_item') }}",
                data: data,
                success: function(response) {
                    $('#loader').hide()
                    $('._stock_transfer_item_div').html('')
                    $('._stock_transfer_item_div').html(response)
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

        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('business.stock_transfer.create') }}",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $("#loader").hide();
                    errorClear()
                    error_input()
                    if (response.status == false) {
                        $.each(response.message, function(key, item) {
                            if (key) {
                                $('.err_' + key).text(item)
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

        function errorClear() {
            $('.err_warehouse_from').text('')
            $('.err_warehouse_to').text('')
        }

        function error_input() {
            $('.err_product').text('')
            $('.err_qty').text('')
            $('.err_product_ids').text('')
        }
    </script>
@endsection
