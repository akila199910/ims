<form id="updateStockForm" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{$stock_transfer->id}}">
    <div class="col-sm-12">
        <h4 class="mt-3 mb-2">Stock Transfer Details</h4>
        <small class="text-info-emphasis">(This will use for adjust the transferred QTY)</small>
        <span class="d-flex mt-5" style="font-size: 14px">
            <p style="font-weight: 600">From Warehouse </p>&nbsp;:
            {{ Str::limit($stock_transfer->from_warehouse->name,30) }}
        </span>
        <span class="d-flex" style="font-size: 14px">
            <p style="font-weight: 600">To Warehouse </p>&nbsp;:
            {{ Str::limit($stock_transfer->to_warehouse->name,30) }}
        </span>
        <span class="d-flex" style="font-size: 14px">
            <p style="font-weight: 600">Trasnferred Date </p>&nbsp;:
            {{ $stock_transfer->transfer_date }}
        </span>
        <span class="d-flex" style="font-size: 14px">
            <p style="font-weight: 600">Product Name </p>&nbsp;:
            {{ Str::limit($stock_transfer->product_info->name,30) }}
        </span>
    </div>
    <div class="col-sm-12 mt-3 text-center">
        <img src="{{ $product->image == '' || $product->image == 0 ? asset('layout_style/img/icons/product_100.png') : config('aws_url.url') . $product->image }}"
            style="width: 75px; height: 75px;" alt="">
    </div>

    <div class="col-sm-12 mt-3">
        <div class="input-block local-forms ">
            <label>Qty<span class="text-danger">*</span></label>
            <input type="hidden" name="av_qty" id="av_qty" value="{{ $av_qty }}">
            <input type="text" name="qty" class="form-control qty" id="qty"
                value="{{ $stock_transfer->qty }}" placeholder="Enter the Qty">
            <small class="text-danger font-weight-bold err_qty"></small>
        </div>
    </div>

    <div class="col-12">
        <div class="doctor-submit text-end">
            <button type="submit" class="btn btn-primary text-uppercase submit-form me-2">Update</button>
        </div>
    </div>

</form>

<script>
    $('#updateStockForm').submit(function(e) {
        e.preventDefault();
        let formData = new FormData($('#updateStockForm')[0]);

        $.ajax({
            type: "POST",
            beforeSend: function() {
                $("#loader").show();
            },
            url: "{{ route('business.stock_transfer.update') }}",
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

    function errorClear()
    {
        $('.err_qty').text('')
    }
</script>
