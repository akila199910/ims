
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
                    <li class="breadcrumb-item active">Add new  Purchase Return</li>
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
                                    <h4>Add new Purchase Return<small class="text-primary-emphasis" style="font-size: 12px !important">(Select the Purchase Product ID to get the product)</small></h4>

                                </div>

                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="purchase_ref">Purchase Invoice Id<span class="text-danger">*</span> </label>
                                    <select class="form-control select2 purchase_ref" name="purchase_ref" id="purchase_ref">
                                        <option value="">Select Purchase Invoice Id</option>
                                        @foreach ( $purchases as $item)
                                            <option value="{{ $item->id }}">{{ $item->invoice_id }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_purchase_ref"></small>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-4 _purchase_orderitem_div">

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
        })

        $('#purchase_ref').change(function (e) {
            e.preventDefault();
            var order_id = $(this).val()

            var data = {
                'order_id' : order_id
            }

            $("#loader").show();
            $.ajax({
                type: "POST",
                url: "{{route('purchase_return.pur_item_filter')}}",
                data: data,
                success: function (response) {
                    $('#loader').hide()

                    $('._purchase_orderitem_div').html('')
                    $('._purchase_orderitem_div').html(response)
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
                url: "{{ route('business.purchase_return.create') }}",
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

        function errorClear()
        {
            $('#purchase_ref').removeClass('is-invalid')
            $('.err_purchase_ref').text('')

            $('.err_purchase_ref').text('')

        }
    </script>
@endsection
