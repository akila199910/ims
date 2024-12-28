<div class="row">
    <div class="col-12 col-md-6 col-xl-6">
        <div class="input-block local-forms">
            <label for="name"> Product Name <span class="text-danger">*</span> </label>
            <select class="form-control select2 product" name="product" id="product">
                <option value="">Select Product Name</option>
                @foreach ( $products as $item)
                    <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                @endforeach
            </select>
            <small class="text-danger font-weight-bold err_product"></small>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-6">
        <div class="input-block local-forms">
            <label>Qty<span class="text-danger">*</span></label>
            <input type="hidden" name="av_qty" class="form-control av_qty" id="av_qty">
            <input type="text" name="qty" class="form-control qty" id="qty">
            <small class="text-danger font-weight-bold err_qty" id="err_qty"></small>
        </div>
    </div>

    <div class="col-12 col-md-12 col-xl-12">
        <div class="input-block local-forms">
            <label for="reason">Reason<span class="text-danger">*</span></label>
            <textarea name="reason" id="reason" class="form-control reason" rows="2"></textarea>
            <small class="text-danger font-weight-bold err_reason"></small>
        </div>
    </div>

</div>

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

