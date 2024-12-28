@extends('layouts.business')

@section('title')
Manage Write Offs
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.writeoff') }}">  Manage Write Offs</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Update  Write Offs</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.writeoff') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h4>Update Write Off</h4>
                                </div>
                            </div>

                            <input type="hidden" name="id" value="{{ $writeoff->id }}">

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="warehouse">Warehouse<span class="text-danger">*</span> </label>
                                    <select name="warehouse" class="form-control warehouse select2" id="warehouse">
                                        <option value="">Select Warehouse name</option>
                                        @foreach ($warehouse as $item)
                                            <option
                                                value="{{ $item->id }}"{{ $item->id == $writeoff->warehouse_id ? ' selected' : '' }}>
                                                {{ Str::limit($item->name,30) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_warehouse"></small>
                                </div>
                            </div>


                            <div class="col-12">
                                <div class="items_data row">
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <label for="product">Product Name<span class="text-danger">*</span> </label>
                                            <select name="product" class="form-control product select2" id="product">
                                                <option value="">Select Product Name</option>
                                                @foreach ($products as $item)
                                                    <option
                                                        value="{{ $item->id }}"{{ $item->id == $writeoff->product_id ? ' selected' : '' }}>
                                                        {{ Str::limit($item->name,30) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger font-weight-bold err_product"></small>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="input-block local-forms">
                                            <label for="qty">Qty <span class="text-danger">*</span> </label>
                                            <input type="hidden" name="av_qty" id="av_qty" value="{{ $av_qty }}">
                                            <input type="text" name="qty" value="{{ $writeoff->qty }}"
                                                class="form-control qty" id="qty" >
                                            <small class="text-danger font-weight-bold err_qty"></small>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-12 col-xl-12">
                                        <div class="input-block local-forms">
                                            <label for="reason">Reason<span class="text-danger">*</span></label>
                                            <textarea name="reason" id="reason" class="form-control reason" rows="2">{{ $writeoff->reason }}</textarea>
                                            <small class="text-danger font-weight-bold err_reason"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (Auth::user()->hasPermissionTo('Update_WriteOff'))
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
        })

        $('#warehouse').change(function(e) {
            e.preventDefault();

            var data = {
                'warehouse_id': $(this).val()
            }

            $('#loader').show()

            $.ajax({
                type: "POST",
                url: "{{ route('business.writeoff.item_filter') }}",
                data: data,
                success: function(response) {
                    console.log(response);
                    $('#loader').hide();
                    $('.items_data').html('');
                    $('.items_data').html(response);
                },
                error: function(data) {
                    $('#loader').hide()
                    alert('something went to wrong')
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
                url: "{{ route('business.writeoff.update') }}",
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
            $('#product').removeClass('is-invalid')
            $('.err_product').text('')

            $('#warehouse').removeClass('is-invalid')
            $('.err_warehouse').text('')

            $('#qty').removeClass('is-invalid')
            $('.err_qty').text('')

            $('#reason').removeClass('is-invalid')
            $('.err_reason').text('')


        }
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();


            $(".number_only").on("input", function(evt) {
                var self = $(this);
                self.val(self.val().replace(/[^0-9]/g, ''));
                if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                    evt.preventDefault();
                }
            });


            $('#product').change(function(e) {
                e.preventDefault();

                var productId = $(this).val();
                if (productId) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('business.writeoff.get_details') }}",
                        data: {
                            '_token': "{{ csrf_token() }}",
                            'id': productId,
                            'warehouse_id' : $('#warehouse').val()
                        },
                        dataType: "JSON",
                        success: function(response) {
                            console.log(response);

                            if (response.product_warehouses && response.product_warehouses.qty > 0) {
                                available_qty = response.product_warehouses.qty;
                                $('#qty').val(available_qty);
                                $('#err_qty').text('');
                                $('#qty').removeClass('is-invalid');
                                $('#av_qty').val(available_qty)
                            } else {
                                available_qty = 0;
                                $('#qty').val('Not Available');
                                $('#err_qty').text('Product Qty not available in this warehouse');
                                $('#qty').addClass('is-invalid');
                            }
                        },
                        error: function() {
                            $('#qty').val('Error retrieving data');
                            $('#err_qty').text('Error retrieving data');
                            $('#qty').addClass('is-invalid');
                        }
                    });
                } else {
                    $('#qty').val('');
                    $('#err_qty').text('');
                    $('#qty').removeClass('is-invalid');
                }
            });
        });
    </script>
@endsection
