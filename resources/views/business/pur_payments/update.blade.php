<form id="updateForm" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <input type="hidden" name="payment_id" id="payment_id" value="{{ $payment->id }}">
        <div class="col-sm-12">
            <div class="input-block local-forms ">
                <label>Amount</label>
                <input type="hidden" name="purchased_date" id="purchased_date"
                    value="{{ date('Y-m-d', strtotime($purchase->purchased_date)) }}">

                <input type="hidden" name="due_amount" value="{{ $due_amount }}"
                    class="form-control due_amount decimal_val" id="due_amount">

                <input type="text" name="amount" value="{{ $payment->paid_amount }}"
                    class="form-control amount decimal_val" id="amount">

                <small class="text-danger font-weight-bold err_amount"></small>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="input-block local-forms ">
                <label>Paid Date</label>
                <input type="date" name="paid_date" class="form-control paid_date" id="paid_date"
                    max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d',strtotime($payment->payment_date)) }}">
                <small class="text-danger font-weight-bold err_paid_date"></small>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="input-block local-forms ">
                <label>Paid Type<span class="text-danger">*</span></label>
                <select name="payment_type" id="payment_type" class="form-control payment_type">
                    <option value="">Select Payment Type</option>
                    @foreach ($pay_types as $item)
                        <option value="{{ $item->id }}" {{$item->id == $payment->pay_type_id ? 'selected' : ''}}>
                            {{ $item->payment_type }}</option>
                    @endforeach
                </select>
                <small class="text-danger font-weight-bold err_payment_type"></small>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="input-block local-forms ">
                <label>Payment Reference Number<small class="text-primary-emphasis">(If Available)</small></label>
                <input type="text" name="payment_reference" value="{{$payment->payment_reference}}" class="form-control payment_reference"
                    id="payment_reference">
                <small class="text-danger font-weight-bold err_payment_reference"></small>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="input-block local-forms ">
                <label>Scan Document <small class="text-primary-emphasis">(If Available)</small></label>
                <input type="file" name="scan_document" class="form-control scan_document decimal_val"
                    id="scan_document">
                <small class="text-danger font-weight-bold err_scan_document"></small>
            </div>
            @if ($payment->scan_doc != '' && $payment->scan_doc != 0)
                <div class="text-center">
                    <a href="{{config('aws_url.url').$payment->scan_doc}}" class="btn btn-sm btn-outline-primary"><i class="fa fa-download"></i></a>
                </div>
            @endif
        </div>

        @if (Auth::user()->hasPermissionTo('Update_Payement'))
            <div class="col-12">
                <div class="doctor-submit text-end">
                    <button type="submit" class="btn btn-primary text-uppercase submit-form me-2">Update</button>
                </div>
            </div>
        @endif
    </div>
</form>

<script>
    $(document).ready(function() {
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
    });

    $('#updateForm').submit(function(e) {
        e.preventDefault();
        let formData = new FormData($('#updateForm')[0]);

        $.ajax({
            type: "POST",
            beforeSend: function() {
                $("#loader").show();
            },
            url: "{{ route('business.purchaseorder.payments.update') }}",
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
                            // $('#' + key).addClass('is-invalid');
                        }
                    });
                } else {
                    successPopup(response.message, '')
                    // location.reload()
                    $('#offcanvasRight').offcanvas('hide');
                    loadPaidDueAmount()

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

    function errorClear() {
        $('#amount').removeClass('is-invalid')
        $('.err_amount').text('')

        $('#payment_type').removeClass('is-invalid')
        $('.err_payment_type').text('')

        $('#paid_date').removeClass('is-invalid')
        $('.err_paid_date').text('')

        $('#payment_reference').removeClass('is-invalid')
        $('.err_payment_reference').text('')

        $('#scan_document').removeClass('is-invalid')
        $('.err_scan_document').text('')
    }
</script>
