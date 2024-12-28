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
                    <li class="breadcrumb-item active">Add new Purchase Order</li>
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
                                    <h4>Add new Purchase Order</h4>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="supplier">Select Vendor <span class="text-danger">*</span> </label>
                                    <select class="form-control select2 supplier" name="supplier" id="supplier">
                                        <option value="">Select Vendor</option>
                                        @foreach ($suppliers as $item)
                                            <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_supplier"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-12 col-xl-12 _supplier_product_div">

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

        $('#supplier').change(function(e) {
            e.preventDefault();

            var data = {
                'supplier_id': $(this).val()
            }

            if ($(this).val() != '') {
                $('#loader').show()

                $.ajax({
                    type: "GET",
                    url: "{{ route('business.purchaseorder.get_products') }}",
                    data: data,
                    success: function(response) {
                        $('#loader').hide()

                        $('._supplier_product_div').html('')
                        $('._supplier_product_div').html(response)
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
            } else {
                $('._supplier_product_div').html('')
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
                url: "{{ route('business.purchaseorder.create') }}",
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

        function errorClear() {
            $('#supplier').removeClass('is-invalid')
            $('.err_supplier').text('')

            $('#purchased_date').removeClass('is-invalid')
            $('.err_purchased_date').text('')
        }
    </script>
@endsection
