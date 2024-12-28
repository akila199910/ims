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
                            <input type="hidden" name="id" id="id" value="{{$purchase['id']}}">
                            <div class="col-sm-6">
                                <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Invoice To </span><br>
                                <span style="font-size: 14px">{{ Str::limit($purchase['supplier_info']['supplier_name'],30) }}</span><br>
                                <span style="font-size: 14px">{{ $purchase['supplier_info']['supplier_address'] }}</span><br>
                                <span style="font-size: 14px">{{ Str::limit($purchase['supplier_info']['supplier_email'],30) }}</span><br>
                                <span style="font-size: 14px">{{ $purchase['supplier_info']['supplier_contact'] }}</span>
                            </div>

                            <div class="col-sm-6">
                                <span class="text-uppercase font-weight-bold text-bold" style="font-weight: 800">Order Invoice Number - #{{$purchase['invoice_id']}} </span><br>
                                <span>Created At - {{date('jS M, Y', strtotime($purchase['purchased_date']))}}</span>
                            </div>

                            <div class="col-sm-12 mt-4">
                                <h4>Ordered Items</h4>
                            </div>

                            <div class="col-sm-12 _product_item_content">

                            </div>

                            @if (Auth::user()->hasPermissionTo('Update_PurchaseOrder'))
                            <div class="col-12">
                                <div class="doctor-submit text-end">
                                    <button type="submit" class="btn btn-primary text-uppercase submit-form me-2">Update</button>
                                </div>
                            </div>
                        @endif

                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="updateItemQty" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="updateItemQtyLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="updateItemQtyLabel">Update QTY</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateItemQtyForm">
                    @csrf

                    <div class="mb-3">
                        <label for="edit_qty" class="form-label">QTY </label>
                        <input type="number" class="form-control" id="edit_qty" name="edit_qty">
                        <input type="hidden" class="form-control" id="purchased_id" value="{{$purchase['id']}}" name="purchased_id">
                        <input type="hidden" class="form-control" id="order_item_id" name="order_item_id">
                        <small class="text-danger font-weight-bold err_edit_qty"></small>
                    </div>

                    <button type="submit" class="btn btn-primary" >Save Changes</button>
                </form>
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

            loadTableContent()
        });

        function loadTableContent()
        {
            var data = {
                'order_id' : '{{$purchase['id']}}'
            }

            $("#loader").show();
            $.ajax({
                type: "GET",
                url: "{{route('business.purchaseorder.get_order_items.form')}}",
                data: data,
                success: function (response) {
                    $("#loader").hide();
                    $('._product_item_content').html('')
                    $('._product_item_content').html(response)
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
            $('#prepay_amount').removeClass('is-invalid')
            $('.err_prepay_amount').text('')

            $('#paid_date').removeClass('is-invalid')
            $('.err_paid_date').text('')

            $('#paid_type').removeClass('is-invalid')
            $('.err_paid_type').text('')

            $('#payment_reference').removeClass('is-invalid')
            $('.err_payment_reference').text('')

            $('#scan_document').removeClass('is-invalid')
            $('.err_scan_document').text('')
        }
    </script>
@endsection
