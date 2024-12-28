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

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="ref_no">Purchased Ref No.</label>
                                    <input type="text" name="ref_no" id="ref_no" class="form-control ref_no" readonly
                                        value="{{ $purchase['ref_no'] }}">
                                </div>
                            </div>
                            <input type="hidden" name="id" id="id" class="form-control id" readonly
                                value="{{ $purchase['id'] }}">

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="purchased_date">Purchased Date </label>
                                    <input type="text" name="purchased_date" id="purchased_date"
                                        class="form-control purchased_date" readonly
                                        value="{{ $purchase['purchased_date'] }}">
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="ordered_by">Ordered By</label>
                                    <input type="text" name="ordered_by" id="ordered_by" class="form-control ordered_by"
                                        readonly value="{{ Str::limit($purchase['ordered_by'],30) }}">
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="modified_by">Modified By</label>
                                    <input type="text" name="modified_by" id="modified_by"
                                        class="form-control modified_by" readonly value="{{ Str::limit($purchase['modified_by'],30) }}">
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="supplier">Vendor Name <span class="text-danger">*</span> </label>
                                    <select class="form-control select2 supplier" name="supplier" id="supplier">
                                        <option value="">Select Vendor</option>
                                        @foreach ($suppliers as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $purchase['supplier_id'] == $item->id ? 'selected' : '' }}>
                                                {{ Str::limit($item->name,30) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_supplier"></small>
                                </div>
                            </div>

                            @php
                                $order_status = ['Pending', 'Approved'];

                                if($purchase['status'] == 1)
                                {
                                    $order_status = ['Pending', 'Approved', 'Received'];
                                }
                            @endphp

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="order_status">Order Status <span class="text-danger">*</span> </label>
                                    <select class="form-control select2 order_status" name="order_status" id="order_status">
                                        <option value="">Select Order Status</option>
                                        @foreach ($order_status as $key => $item)
                                            <option value="{{ $key }}"
                                                {{ $purchase['status'] == $key ? 'selected' : '' }}>{{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_order_status"></small>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-4 _product_item_content">

                            </div>

                        </div>
                        @if (Auth::user()->hasPermissionTo('Update_PurchaseOrder'))
                            <div class="col-12">
                                <div class="doctor-submit text-end">
                                    <button type="submit"
                                        class="btn btn-primary text-uppercase submit-form me-2">Update</button>
                                </div>
                            </div>
                        @endif
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
            $('#supplier').removeClass('is-invalid')
            $('.err_supplier').text('')

            $('#purchased_date').removeClass('is-invalid')
            $('.err_purchased_date').text('')
        }
    </script>
@endsection
