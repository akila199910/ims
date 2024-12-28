@extends('layouts.business')

@section('title')
   Manage Payements
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.purchases') }}">Manage Payements</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">You Can Pay here</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.purchases') }}"  class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <form method="POST" id="submitForm" enctype="multipart/form-data">
            @csrf

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Add Payement Details</h4>
                                </div>
                            </div>

                            <input type="hidden" name="purchased_id" value="{{ $purchase_order->id }}">

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label>Payment Type<span class="text-danger">*</span></label>
                                    <select name="payment_type" class="form-control payment_type" id="payment_type">
                                        <option value="">Select Payment Type</option>
                                        @foreach ($purchase_pays as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->payment_type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_payment_type"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="exampleFormControlInput2">Payment Date<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="payment_date" id="payment_date" max="{{ date('Y-m-d') }}"
                                        class="form-control">
                                    <small class="text-danger font-weight-bold err_payment_date"></small>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-block local-forms ">
                                    <label>Payment Reference Number<small class="text-primary-emphasis">(If Available)</small></label>
                                    <input type="text" name="payment_reference"
                                        class="form-control payment_reference" id="payment_reference">
                                    <small class="text-danger font-weight-bold err_payment_reference"></small>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-block local-forms ">
                                    <label>Scan Document <small class="text-primary-emphasis">(If Available)</small></label>
                                    <input type="file" name="scan_document"
                                        class="form-control scan_document decimal_val" id="scan_document">
                                    <small class="text-danger font-weight-bold err_scan_document"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="exampleFormControlInput2">Paid Amount<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="paid_amount" id="paid_amount" value=""
                                        class="form-control price">
                                    <small class="text-danger font-weight-bold err_paid_amount"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms" id="due_amount_div">
                                    <label for="exampleFormControlInput2">Purchased Amount<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="purchase_amount" id="purchase_amount"
                                        value="{{ number_format($purchase_order->final_amount, 2, '.', '') }}" readonly
                                        class="form-control price text-right font-weight-bold text-black">
                                </div>
                            </div>

                            <div class="input-block local-forms  form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 text-end"
                                id="due_amount_labl">
                                <label for="exampleFormControlInput2"
                                    class="font-weight-bold text-black text-right">Discount
                                    Amount</label>
                                <input type="text" name="discount_amount" id="discount_amount"
                                    value="{{ number_format($purchase_order->discount_amount, 2, '.', '') }}" readonly
                                    class="form-control price text-right font-weight-bold text-black">

                                <span class="text-danger font-weight-bold err_discount_amount"></span>
                            </div>

                            @php
                                $total_amount = $purchase_order->final_amount;
                                $discount = $purchase_order->discount_amount;
                                $paid_amount = 0;

                                // to check if payementInformations available
                                $paid_amount = $purchase_order->payment_list()->sum('paid_amount');

                                $due_amount = $total_amount - $discount - $paid_amount;

                            @endphp

                            <div class="input-block local-forms form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 text-end"
                                id="due_amount_labl">
                                <label for="exampleFormControlInput2" class="font-weight-bold text-black text-right">Paid
                                    Amount</label>
                                <input type="text" name="pur_paid_amount" id="pur_paid_amount"
                                    value="{{ number_format($paid_amount, 2, '.', '') }}" readonly
                                    class="form-control price text-right font-weight-bold text-black">

                                <span class="text-danger font-weight-bold error_pur_paid_amount"></span>
                            </div>

                            <div class="input-block local-forms form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 text-end"
                                id="due_amount_labl">
                                <label for="exampleFormControlInput2" class="font-weight-bold text-black text-right">Due
                                    Amount</label>
                                <input type="text" name="due_amount" id="due_amount"
                                    value="{{ number_format($due_amount, 2, '.', '') }}" readonly
                                    class="form-control price text-right font-weight-bold text-danger">

                                <span class="text-danger font-weight-bold err_due_amount"></span>
                            </div>

                            @if ($due_amount > 0)
                                <div class="col-lg-12 col-12 mt-5 mb-5" id="submit_button">
                                    <div class="form-group text-end text-sm-right">
                                        <button type="submit"
                                            class="btn btn-primary btn-max-200 text-uppercase font-weight-bold"
                                            style="width: 200px">Save</button>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12 ">
                        <h3 class="font-weight-bold pt-2 pb-2 text-uppercase">Payments Information</h3>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area br-6 m-2">
                <table id="data_table" class="table table-striped" style="width:100%">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Paid Method</th>
                            <th>Payment Reference Num</th>
                            <th>Purchased Date</th>
                            <th>Paid Amount</th>
                            <th></th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h4 id="offcanvasRightLabel" class="text-uppercase">Update Payment</h4>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body _update_model_content">
            <!-- Dynamic body will show here -->

        </div>
    </div>
@endsection


@section('scripts')
    <script>
        var table;
        $(document).ready(function() {

            $(function() {
                table = $('#data_table').DataTable({
                    "stripeClasses": [],
                    "lengthMenu": [10, 20, 50],
                    "pageLength": 10,

                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('purchases.get_payments', ['json' => 1]) }}",
                        data: function(d) {
                            d.order_id = '{{ $purchase_order->id }}'
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'payment_type',
                            name: 'PayementMethodInfo.payment_type'
                        },
                        {
                            data: 'payment_reference',
                            name: 'payment_reference'
                        },
                        {
                            data: 'payment_date',
                            name: 'payment_date'
                        },
                        {
                            data: 'paid_amount',
                            name: 'paid_amount',
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
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                    url: "{{ route('purchases.store_payments') }}",
                    data: formData,
                    dataType: "JSON",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        $("#loader").hide();
                        errorClear();
                        if (response.status == false) {
                            $.each(response.message, function(key, item) {
                                if (key) {
                                    $('.err_' + key).text(item)
                                    $('#' + key).addClass('is-invalid');
                                }
                            });
                        } else {
                            successPopup(response.message, '')
                            table.clear();
                            table.ajax.reload();
                            table.draw();
                            location.reload();
                        }
                    }
                });
            });

            $('#paid_amount').keyup(function(e) {
                e.preventDefault();

                var paid_val = $(this).val();
                var paid_amount = '{{ $due_amount }}';
                //Discount Validation

                var due_amount = (paid_amount - paid_val);

                if (due_amount >= 0) {
                    due_amount = due_amount;
                    $('.err_paid_amount').text('');
                    $('#submit_button').css('display', 'block');
                } else {
                    $('.err_paid_amount').text('Enter the valid Due Amount');
                    due_amount = parseFloat(0);
                    $('#submit_button').css('display', 'none');
                }

                $('#due_amount').val(due_amount.toFixed(2));

            });

            function errorClear() {
                $('#payment_type').removeClass('is-invalid')
                $('.err_payment_type').text('')

                $('#payment_date').removeClass('is-invalid')
                $('.err_payment_date').text('')

                $('#paid_amount').removeClass('is-invalid')
                $('.err_paid_amount').text('')
            }


        });
    </script>

    <script>


function openUpdateModal(id) {
            var data = {
                'id': id
            }

            $('#loader').show()

            $.ajax({
                type: "GET",
                url: "{{ route('business.purchases.update.form') }}",
                data: data,
                success: function(response) {
                    $('#loader').hide()
                    $('._update_model_content').html('')
                    $('._update_model_content').html(response)

                    var myOffcanvas = document.getElementById('offcanvasRight')
                    var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas)
                    bsOffcanvas.show()
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to Delete the Selected Payment?',
                type: 'red',
                autoClose: 'cancel|10000',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-green',
                        action: function() {
                            var data = {
                                "_token": $('input[name=_token]').val(),
                                "id": id,
                            }
                            $.ajax({
                                type: "POST",
                                url: "{{ route('purchases.deletePayement') }}",
                                data: data,
                                success: function(response) {
                                    location.reload();
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
