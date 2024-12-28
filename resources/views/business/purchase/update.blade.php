<form id="updatePaymentForm" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{ $purchase_pay->id }}">
    <div class="col-sm-12">
        <h4 class="mt-3 mb-2">Payment Details</h4>
    </div>

    <div class="col-sm-12 mt-3">
        <div class="input-block local-forms ">
            <label>Payment Type<span class="text-danger">*</span></label>
            <select name="payment_type" class="form-control payment_type" id="payment_type">
                <option value="">Select Payment Type</option>
                @foreach ($purchase_pays as $item)
                    <option value="{{ $item->id }}"{{ $item->id == $purchase_pay->payment_id ? ' selected' : '' }}>
                        {{ $item->payment_type }}
                    </option>
                @endforeach
            </select>
            <small class="text-danger font-weight-bold err_payment_type"></small>
        </div>
    </div>

    <div class="col-sm-12 mt-3">
        <div class="input-block local-forms ">
            <label for="exampleFormControlInput2">Payment Date<span class="text-danger">*</span></label>
            <input type="date" name="payment_date" id="payment_date" max="{{ date('Y-m-d') }}"
                value="{{ $purchase_pay->payment_date }}" class="form-control">
            <small class="text-danger font-weight-bold err_payment_date"></small>
        </div>
    </div>

    <div class="col-sm-12 mt-3">
        <div class="input-block local-forms ">
            <label>Payment Reference Number<small class="text-primary-emphasis">(If Available)</small></label>
            <input type="text" name="payment_reference" value="{{ $purchase_pay->payment_reference }}"
                class="form-control payment_reference" id="payment_reference">
            <small class="text-danger font-weight-bold err_payment_reference"></small>
        </div>
    </div>

    <div class="col-sm-12 mt-3">
        <div class="input-block local-forms ">
            <label>Scan Document <small class="text-primary-emphasis">(If Available)</small></label>
            <input type="file" name="scan_document" class="form-control scan_document decimal_val"
                id="scan_document">
            <small class="text-danger font-weight-bold err_scan_document"></small>
        </div>
    </div>

    <div class="col-sm-12 mt-3">
        <div class="input-block local-forms ">
            <label for="exampleFormControlInput2">Paid Amount<span class="text-danger">*</span></label>
            <input type="text" name="up_paid_amount" id="up_paid_amount" value="{{ $purchase_pay->paid_amount }}"
                class="form-control price">
            <small class="text-danger font-weight-bold err_up_paid_amount"></small>
        </div>
    </div>

    <div class="col-sm-12 mt-3">
        <div class="input-block local-forms ">
            <label for="exampleFormControlInput2">Purchased Amount<span class="text-danger">*</span></label>
            <input type="text" name="purchase_amount" id="purchase_amount"
                value="{{ number_format($purchase_order->total_amount, 2, '.', '') }}" readonly
                class="form-control price text-right font-weight-bold text-black">
        </div>
    </div>

    <div class="col-sm-12 mt-3">
        <div class="input-block local-forms ">
            <label for="exampleFormControlInput2" class="font-weight-bold text-black text-right">Discount
                Amount</label>
            <input type="text" name="discount_amount" id="discount_amount"
                value="{{ number_format($purchase_order->discount_amount, 2, '.', '') }}" readonly
                class="form-control price text-right font-weight-bold text-black">

            <span class="text-danger font-weight-bold err_discount_amount"></span>
        </div>
    </div>

    @php
        $total_amount = $purchase_order->total_amount;
        $discount = $purchase_order->discount_amount;
        $paid_amount = 0;

        $paid_amount = $purchase_order->payment_list()->sum('paid_amount');

        $due_amount = $total_amount - $discount - $paid_amount;

    @endphp


    <div class="col-sm-12 mt-3">
        <div class="input-block local-forms ">
            <label for="exampleFormControlInput2" class="font-weight-bold text-black text-right">Paid
                Amount</label>
            <input type="text" name="pur_paid_amount" id="pur_paid_amount"
                value="{{ number_format($paid_amount, 2, '.', '') }}" readonly
                class="form-control price text-right font-weight-bold text-black">

            <span class="text-danger font-weight-bold error_pur_paid_amount"></span>
        </div>
    </div>

    <div class="input-block local-forms form-group col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 text-end"
        id="due_amount_labl">
        <label for="exampleFormControlInput2" class="font-weight-bold text-black text-right">Due
            Amount</label>
        <input type="text" name="up_due_amount" id="up_due_amount" value="{{ number_format($due_amount, 2, '.', '') }}"
            readonly class="form-control price text-right font-weight-bold text-danger">

        <span class="text-danger font-weight-bold err_up_due_amount"></span>
    </div>

    <div class="col-12">
        <div class="doctor-submit text-end">
            <button type="submit" class="btn btn-primary text-uppercase submit-form me-2">Update</button>
        </div>
    </div>


</form>

<script>
    $('#updatePaymentForm').submit(function(e) {
        e.preventDefault();

        let formData = new FormData($('#updatePaymentForm')[0]);

        $.ajax({
            type: "POST",
            beforeSend: function() {
                $("#loader").show();
            },
            url: "{{ route('business.purchases.update') }}",
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


    $('#up_paid_amount').keyup(function(e) {
        e.preventDefault();

        var paid_val = $(this).val();
        var up_paid_amount = '{{ $due_amount }}';


        var up_due_amount = (up_paid_amount - paid_val);

        if (up_due_amount >= 0) {
            up_due_amount = up_due_amount;
            $('.err_up_paid_amount').text('');

        } else {
            $('.err_up_paid_amount').text('Enter the valid Due Amount');
            up_due_amount = parseFloat(0);

        }

        $('#up_due_amount').val(up_due_amount.toFixed(2));

    });

    function errorClear() {
        $('#payment_type').removeClass('is-invalid')
        $('.err_payment_type').text('')

        $('#payment_date').removeClass('is-invalid')
        $('.err_payment_date').text('')

        $('#paid_amount').removeClass('is-invalid')
        $('.err_paid_amount').text('')
    }
</script>
