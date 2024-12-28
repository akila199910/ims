<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-4">
    <div class="row">
        <div class="col-12 col-md-6 col-xl-5">
            <div class="input-block local-forms">
                <label>Select Product</label>
                <select class="form-control select2 product" name="product" id="product">
                    <option value="">Select Product</option>
                    @foreach ($products as $item)
                        <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                    @endforeach
                </select>

                <small class="text-danger font-weight-bold err_product"></small>
                <small class="text-danger err_product_ids"></small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-5">
            <div class="input-block local-forms ">
                <label>Qty</label>
                <input type="text" name="qty" class="form-control number_only_val qty"
                    value="{{ old('qty') }}" id="qty" placeholder="Enter the Qty">
                <small class="text-danger font-weight-bold err_qty"></small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-2">
            <button class="btn btn-lg btn-secondary text-uppercase btn-bottom" type="button" id="add_button">+</button>
        </div>

        <div class="card mb-4" id="purchaseItem_info_table">
            <div class="table-responsive">
                <table class="table table-stripped " id="data_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item #</th>
                            <th>Description</th>
                            <th>QTY</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th class="text-end"></th>
                        </tr>
                    </thead>
                    <tbody class="_add_product_div"></tbody>
                </table>
            </div>
        </div>
        <div class="col-12 col-md-9 col-xl-9 col-lg-9"></div>
        <div class="col-12 col-md-3 col-xl-3 col-lg-3">
            <div class="row">
                <div class="col-sm-12">
                    <div class="input-block local-forms ">
                        <label>Sub Total</label>
                        <input type="text" readonly name="sub_total_amount"
                            class="form-control sub_total_amount text-end" style="font-weight: 600"
                            id="sub_total_amount" value="{{ number_format($purchase->total_amount, 2, '.', '') }}">
                        <small class="text-danger font-weight-bold err_sub_total_amount"></small>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="input-block local-forms ">
                        <label>Tax Amount</label>
                        <input type="text" name="tax_amount" class="form-control tax_amount decimal_val text-end"
                            id="tax_amount" value="{{ number_format($purchase->tax_amount, 2, '.', '') }}">
                        <small class="text-danger font-weight-bold err_tax_amount"></small>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="input-block local-forms ">
                        <label>Shipping Amount</label>
                        <input type="text" name="shipping_amount"
                            class="form-control shipping_amount decimal_val text-end" id="shipping_amount"
                            value="{{ number_format($purchase->shipping_amount, 2, '.', '') }}">
                        <small class="text-danger font-weight-bold err_shipping_amount"></small>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="input-block local-forms ">
                        <label>Other Amount</label>
                        <input type="text" name="other_amount" class="form-control other_amount decimal_val text-end"
                            id="other_amount" value="{{ number_format($purchase->other_amount, 2, '.', '') }}">
                        <small class="text-danger font-weight-bold err_other_amount"></small>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="input-block local-forms ">
                        <label>Net Amount</label>
                        <input type="text" readonly name="net_total_amount"
                            class="form-control net_total_amount decimal_val text-end" style="font-weight: 600"
                            id="net_total_amount" value="{{ number_format($purchase->final_amount, 2, '.', '') }}">
                        <small class="text-danger font-weight-bold err_net_total_amount"></small>
                    </div>
                </div>
            </div>
        </div>
        @if ($purchase->status == 0 && $purchase->supplier_Info->payment_information->PaymentTermsInfo->payement_term == 'Prepay')
            <div class="col-sm-12 mt-4 mb-4">
                <h4>Prepayment Informations</h4>
            </div>

            @php
                $id = (isset($purchase->first_payement_info) && !empty($purchase->first_payement_info)) ? $purchase->first_payement_info->id : null;
                $paid_amount = (isset($purchase->first_payement_info) && !empty($purchase->first_payement_info)) ? $purchase->first_payement_info->paid_amount : null;
                $payment_reference = (isset($purchase->first_payement_info) && !empty($purchase->first_payement_info)) ? $purchase->first_payement_info->payment_reference : null;
                $payment_id = (isset($purchase->first_payement_info) && !empty($purchase->first_payement_info)) ? $purchase->first_payement_info->payment_id : null;
                $payment_date = (isset($purchase->first_payement_info) && !empty($purchase->first_payement_info)) ? $purchase->first_payement_info->payment_date : null;
                $scan_doc = (isset($purchase->first_payement_info) && !empty($purchase->first_payement_info)) ? ($purchase->first_payement_info->scan_doc == '' || $purchase->first_payement_info->scan_doc == 0 ? null :  config('aws_url.url').$purchase->first_payement_info->scan_doc) : null;

            @endphp

            <div class="col-sm-4">
                <input type="hidden" name="first_payment_id" value="{{$id}}">
                <div class="input-block local-forms ">
                    <label>Prepay Amount</label>
                    <input type="text" name="prepay_amount" value="{{$paid_amount}}" class="form-control prepay_amount decimal_val"
                        id="prepay_amount">
                    <small class="text-danger font-weight-bold err_prepay_amount"></small>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="input-block local-forms ">
                    <label>Paid Date</label>
                    <input type="date" name="paid_date" class="form-control paid_date" id="paid_date"
                        max="{{ date('Y-m-d') }}" value="{{$payment_date}}">
                    <small class="text-danger font-weight-bold err_paid_date"></small>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="input-block local-forms ">
                    <label>Paid Type<span class="text-danger">*</span></label>
                    <select name="paid_type" id="paid_type" class="form-control paid_type">
                        <option value="">Select Payment Type</option>
                        @foreach ($pay_types as $item)
                            <option value="{{ $item->id }}" {{$item->id == $payment_id ? 'selected' : ''}}>{{ $item->payment_type }}</option>
                        @endforeach
                    </select>
                    <small class="text-danger font-weight-bold err_paid_type"></small>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="input-block local-forms ">
                    <label>Payment Reference Number<small class="text-primary-emphasis">(If Available)</small></label>
                    <input type="text" name="payment_reference" value="{{$payment_reference}}" class="form-control payment_reference"
                        id="payment_reference">
                    <small class="text-danger font-weight-bold err_payment_reference"></small>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="input-block local-forms ">
                    <label>Scan Document <small class="text-primary-emphasis">(If Available)</small></label>
                    <input type="file" name="scan_document" class="form-control scan_document decimal_val"
                        id="scan_document">
                    <small class="text-danger font-weight-bold err_scan_document"></small>
                </div>
                @if ($scan_doc != null)
                    <div class="text-center">
                        <a href="{{$scan_doc}}" target="_blank"><i class="fa fa-download"></i></a>
                    </div>
                @endif

            </div>
        @endif
    </div>
</div>

<script>
    var table;

    $(document).ready(function() {
        loadData()

        $('.select2').select2()
        //number only validation
        $(document).on("input", ".number_only_val", function() {
            var self = $(this);
            self.val(self.val().replace(/\D/g, "")); // Remove non-numeric characters
        });

        //Decimal validation
        $(document).on("input", ".decimal_val", function() {
            var self = $(this);

            // Allow only numbers and a single decimal point
            self.val(self.val().replace(/[^0-9\.]/g, ''));

            // Prevent entering more than one decimal point
            if ((self.val().match(/\./g) || []).length > 1) {
                self.val(self.val().slice(0, -1)); // Remove the last entered decimal point
            }
        });

        loadSubTotal()
    });

    function loadData() {
        table = $('#data_table').DataTable({
            "stripeClasses": [],
            pageLength: -1, // Set default length to show all records
            lengthMenu: [
                [-1],
                ['All']
            ], // Set options for length menu
            processing: true,
            serverSide: true,
            orderable: false,
            paging: false,
            info: false,
            ajax: {
                url: "{{ route('business.purchaseorder.get_order_items.list', ['json' => 1]) }}",
                data: function(d) {
                    d.order_id = '{{ $purchase->id }}'
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'product_id',
                    name: 'product_info.product_id',
                    orderable: false,
                },
                {
                    data: 'product_name',
                    name: 'product_info.name',
                    orderable: false
                },
                {
                    data: 'qty',
                    name: 'qty',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'unit_price',
                    name: 'unit_price',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'total_amount',
                    name: 'total_amount',
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

    function loadSubTotal() {

        var data = {
            'order_id': '{{ $purchase->id }}'
        }

        $.ajax({
            type: "POST",
            url: "{{ route('business.purchaseorder.product_subtotal') }}",
            data: data,
            dataType: "JSON",
            success: function(response) {
                var total_amount = parseFloat(response.total_amount);
                $('#sub_total_amount').val(total_amount.toFixed(2))
                calculate_net_total()
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

    function calculate_net_total() {
        var sub_total_amount = parseFloat($('#sub_total_amount').val())
        var tax_amount = parseFloat($('#tax_amount').val())
        var shipping_amount = parseFloat($('#shipping_amount').val())
        var other_amount = parseFloat($('#other_amount').val())

        var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
        $('#net_total_amount').val(net_total_amount.toFixed(2))
    }

    $('#tax_amount').keyup(function(e) {
        var key_amount = $(this).val();

        if (key_amount == "" || key_amount == 0) {
            var amount = parseFloat(0);
        } else {
            var amount = parseFloat(key_amount);
        }

        var sub_total_amount = parseFloat($('#sub_total_amount').val())
        var tax_amount = parseFloat(amount)
        var shipping_amount = parseFloat($('#shipping_amount').val())
        var other_amount = parseFloat($('#other_amount').val())

        var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
        $('#net_total_amount').val(net_total_amount.toFixed(2))
    });

    $('#shipping_amount').keyup(function(e) {
        var key_amount = $(this).val();

        if (key_amount == "" || key_amount == 0) {
            var amount = parseFloat(0);
        } else {
            var amount = parseFloat(key_amount);
        }

        var sub_total_amount = parseFloat($('#sub_total_amount').val())
        var tax_amount = parseFloat($('#tax_amount').val())
        var shipping_amount = parseFloat(amount)
        var other_amount = parseFloat($('#other_amount').val())

        var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
        $('#net_total_amount').val(net_total_amount.toFixed(2))
    });

    $('#other_amount').keyup(function(e) {
        var key_amount = $(this).val();

        if (key_amount == "" || key_amount == 0) {
            var amount = parseFloat(0);
        } else {
            var amount = parseFloat(key_amount);
        }

        var sub_total_amount = parseFloat($('#sub_total_amount').val())
        var tax_amount = parseFloat($('#tax_amount').val())
        var shipping_amount = parseFloat($('#shipping_amount').val())
        var other_amount = parseFloat(amount)

        var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
        $('#net_total_amount').val(net_total_amount.toFixed(2))
    });

    $(document).on('keyup', '.request_qty', function(e) {
        e.preventDefault()

        var key_value = $(this).val();

        if (key_value == "" || key_value == 0) {
            var qty = parseInt(1);
            $(this).closest('tr').find('.request_qty').val(qty)
        } else {
            var qty = parseInt(key_value);
        }

        var unit_price = $(this).closest('tr').find('.retail_price').val()
        var total_price = $(this).closest('tr').find('.total_price').val()
        // row_total_price

        unit_price = parseFloat(unit_price)
        total_price = parseFloat(total_price)

        var sub_total_amount = parseFloat($('#sub_total_amount').val())
        sub_total_amount = parseFloat(sub_total_amount - total_price)

        var new_total_price = parseFloat(qty * unit_price);
        sub_total_amount = parseFloat(sub_total_amount + new_total_price)
        $(this).closest('tr').find('.total_price').val(new_total_price.toFixed(2))
        $(this).closest('tr').find('.row_total_price').text(new_total_price.toFixed(2))

        var tax_amount = parseFloat($('#tax_amount').val())
        var shipping_amount = parseFloat($('#shipping_amount').val())
        var other_amount = parseFloat($('#other_amount').val())

        var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
        $('#net_total_amount').val(net_total_amount.toFixed(2))
        $('.net_total_amount').text(net_total_amount.toFixed(2))
        $('#sub_total_amount').val(sub_total_amount.toFixed(2))
    });

    $(document).on('keyup', '.retail_price', function(e) {
        e.preventDefault()

        var key_value = $(this).val();

        if (key_value == "" || key_value == 0) {
            var unit_price = parseFloat(0);
            $(this).closest('tr').find('.retail_price').val(unit_price.toFixed(2))
        } else {
            var unit_price = parseFloat(key_value);
        }

        var qty = $(this).closest('tr').find('.request_qty').val()
        var total_price = $(this).closest('tr').find('.total_price').val()
        // row_total_price

        unit_price = parseFloat(unit_price)
        total_price = parseFloat(total_price)

        var sub_total_amount = parseFloat($('#sub_total_amount').val())
        sub_total_amount = parseFloat(sub_total_amount - total_price)

        var new_total_price = parseFloat(qty * unit_price);
        sub_total_amount = parseFloat(sub_total_amount + new_total_price)
        $(this).closest('tr').find('.total_price').val(new_total_price.toFixed(2))
        $(this).closest('tr').find('.row_total_price').text(new_total_price.toFixed(2))

        var tax_amount = parseFloat($('#tax_amount').val())
        var shipping_amount = parseFloat($('#shipping_amount').val())
        var other_amount = parseFloat($('#other_amount').val())

        var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
        $('#net_total_amount').val(net_total_amount.toFixed(2))
        $('.net_total_amount').text(net_total_amount.toFixed(2))
        $('#sub_total_amount').val(sub_total_amount.toFixed(2))
    });

    function update_qty(id) {
        var data = {
            'item_id': id
        }

        $.ajax({
            type: "POST",
            url: "{{ route('business.purchaseorder.get_item_info') }}",
            data: data,
            dataType: "JSON",
            success: function(response) {
                $("#loader").hide();
                $('#edit_qty').val(response.qty)
                $('#order_item_id').val(response.item_id)
                $('#updateItemQty').modal('show')
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

    function delete_confirmation(id) {
        $.confirm({
            theme: 'modern',
            columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
            icon: 'far fa-question-circle text-danger',
            title: 'Are you Sure!',
            content: 'Do you want to Delete the Selected Purchase Orders Item?',
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
                            url: "{{ route('business.purchaseorder.delete_item') }}",
                            data: data,
                            success: function(response) {
                                $("#loader").hide();
                                loadSubTotal()
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

    $('#add_button').click(function(e) {
        e.preventDefault();

        var product = $('#product').val();
        var qty = $('#qty').val();
        $('#loader').show()

        var data = {
            'product': product,
            'qty': qty,
            'order_id': '{{ $purchase->id }}'
        }

        $.ajax({
            type: "POST",
            url: "{{ route('business.purchaseorder.add_purchase_item') }}",
            data: data,
            dataType: "JSON",
            success: function(response) {
                $('#loader').hide()
                clearError();

                if (response.status == false) {
                    $.each(response.errors, function(key, item) {
                        if (key) {
                            $('.err_' + key).text(item);
                        }
                    });
                } else {
                    clearInput()
                    loadSubTotal()
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

    function clearError() {
        $('.err_product').text('');
        $('.err_qty').text('');

    }

    function clearInput() {
        $('#product').val('').change();
        $('#qty').val('');
    }

    $('#updateItemQtyForm').submit(function(e) {
        e.preventDefault();
        let formData = new FormData($('#updateItemQtyForm')[0]);

        $.ajax({
            type: "POST",
            beforeSend: function() {
                $("#loader").show();
            },
            url: "{{ route('business.purchaseorder.update_qty') }}",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                $("#loader").hide();
                $('.err_edit_qty').text('')

                if (response.status == false) {
                    $.each(response.message, function(key, item) {
                        if (key) {
                            $('.err_' + key).text(item)
                            $('#' + key).addClass('is-invalid');
                        }
                    });
                } else {
                    loadSubTotal()
                    table.clear();
                    table.ajax.reload();
                    table.draw();
                    $('#updateItemQty').modal('hide')
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
</script>
