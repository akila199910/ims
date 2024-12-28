<div class="row">
    <div class="col-12">
        <div class="form-heading">
            <h4>Add Purchase Items</h4>
        </div>
    </div>
    <div class="col-12 col-md-5 col-xl-5">
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

    <div class="col-12 col-md-5 col-xl-5">
        <div class="input-block local-forms ">
            <label>Qty</label>
            <input type="text" name="qty" class="form-control qty number_only_val" value="{{ old('qty') }}"
                id="qty" placeholder="Enter the Qty">
            <small class="text-danger font-weight-bold err_qty"></small>
        </div>
    </div>

    <div class="col-12 col-md-2 col-xl-2">
        <button class="btn btn-lg btn-secondary text-uppercase btn-bottom" type="button" id="add_button">+</button>
    </div>

    <div class="col-12 col-md-12 col-xl-12 mb-4">
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary">Item #</th>
                        <th class="text-uppercase text-secondary">Description</th>
                        <th class="text-uppercase text-secondary">Qty</th>
                        <th class="text-uppercase text-secondary">Unit Price</th>
                        <th class="text-uppercase text-secondary">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="input_field_table">
                    <tr id="no_product_row">
                        <td colspan="6" class="text-center text-danger text-uppercase">No Product Added!</td>
                    </tr>
                </tbody>
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
                        class="form-control sub_total_amount text-end" style="font-weight: 600" id="sub_total_amount"
                        value="0.00">
                    <small class="text-danger font-weight-bold err_sub_total_amount"></small>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="input-block local-forms ">
                    <label>Tax Amount</label>
                    <input type="text" name="tax_amount" class="form-control tax_amount decimal_val text-end"
                        id="tax_amount" value="0.00">
                    <small class="text-danger font-weight-bold err_tax_amount"></small>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="input-block local-forms ">
                    <label>Shipping Amount</label>
                    <input type="text" name="shipping_amount"
                        class="form-control shipping_amount decimal_val text-end" id="shipping_amount" value="0.00">
                    <small class="text-danger font-weight-bold err_shipping_amount"></small>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="input-block local-forms ">
                    <label>Other Amount</label>
                    <input type="text" name="other_amount" class="form-control other_amount decimal_val text-end"
                        id="other_amount" value="0.00">
                    <small class="text-danger font-weight-bold err_other_amount"></small>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="input-block local-forms ">
                    <label>Net Amount</label>
                    <input type="text" readonly name="net_total_amount"
                        class="form-control net_total_amount decimal_val text-end" style="font-weight: 600"
                        id="net_total_amount" value="0.00">
                    <small class="text-danger font-weight-bold err_net_total_amount"></small>
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Create_PurchaseOrder'))
        <div class="col-12">
            <div class="doctor-submit text-end">
                <button type="submit" class="btn btn-primary text-uppercase submit-form me-2">Create</button>
            </div>
        </div>
    @endif
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
                <form id="updateItemQty">
                    @csrf

                    <div class="mb-3">
                        <label for="edit_qty" class="form-label">QTY </label>
                        <input type="number" class="form-control" id="edit_qty" name="edit_qty">
                        <input type="hidden" class="form-control" id="edit_raw_id" name="edit_raw_id">
                        <input type="hidden" class="form-control" id="edit_retail_price" name="edit_retail_price">
                        <input type="hidden" class="form-control" id="edit_total_price" name="edit_total_price">
                        <small class="text-danger font-weight-bold err_edit_qty"></small>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="save_change()">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
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

        calculate_net_total()
    });

    var count = 0;
    var product_ids = [];
    var sub_total = 0;

    function calculate_net_total() {
        var sub_total_amount = parseFloat($('#sub_total_amount').val())
        var tax_amount = parseFloat($('#tax_amount').val())
        var shipping_amount = parseFloat($('#shipping_amount').val())
        var other_amount = parseFloat($('#other_amount').val())

        var net_total_amount = parseFloat(sub_total_amount + tax_amount + shipping_amount + other_amount);
        $('#net_total_amount').val(net_total_amount.toFixed(2))
        $('.net_total_amount').text(net_total_amount.toFixed(2))

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
        $('.net_total_amount').text(net_total_amount.toFixed(2))
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
        $('.net_total_amount').text(net_total_amount.toFixed(2))
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
        $('.net_total_amount').text(net_total_amount.toFixed(2))
    });

    $('#add_button').click(function(e) {
        e.preventDefault();

        var product = $('#product').val();
        var qty = $('#qty').val();
        var purchase_product = [];

        $('input[name="product_id[]"]').each(function() {
            purchase_product.push($(this).val());
        });

        var data = {
            'product': product,
            'qty': qty,
            'product_ids': product_ids
        }

        $('#loader').show()

        $.ajax({
            type: "POST",
            url: "{{ route('business.purchaseorder.purchase_item_validation') }}",
            data: data,
            dataType: "JSON",
            success: function(response) {
                console.log(response);
                $('#loader').hide()
                clearError();

                if (response.status == false) {
                    $.each(response.errors, function(key, item) {
                        if (key) {
                            $('.err_' + key).text(item);
                        }
                    });
                } else {
                    clearInput();
                    append_table(response.data)
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
                $('#loader').hide()
                alert('Something went to wrong')
            }
        });
    })

    function append_table(data) {
        $('#no_product_row').remove()
        // Increment count for unique row ID
        count++;

        // Build the HTML string
        var html = "<tr id='row_" + count + "'>";
        html += "<td>" + data.product_no + "</td>";
        html += "<td>";
        html += '<input type="hidden" name="product_ids[]" value="' + data.id + '" >';
        html += data.name + ' - ' + data.unit_name;
        html += "</td>";
        html += "<td>";
        html +=
            '<input type="text" class="form-control request_qty number_only_val" style="width:75px" name="request_qtys[]" id="request_qtys_' +
            count + '" value="' + data.qty + '" >';
        html += "</td>";
        html += "<td>";
        html +=
            '<input type="text" class="form-control retail_price decimal_val" style="width:120px" name="retail_prices[]" id="retail_prices_' +
            count + '" value="' + data.retail_price + '" >';
        html += "</td>";
        html += "<td>";
        html +=
            '<input type="hidden" class="form-control total_price decimal_val" style="width:75px" name="total_prices[]" id="total_prices_' +
            count + '" value="' + data.total_price + '" >';
        html += "<span class='row_total_price' id='row_total_price_" + count + "'>" + parseFloat(data.total_price).toFixed(2) + "</span>";
        html += "</td>";

        html += "<td>";
        // Button to remove the contact using count as row ID
        // html += '<button type="button" class="btn btn-sm btn-outline-primary" onclick="edit_product(' + count +
        //     ')" id="btn_' + count + '"><i class="fa fa-edit"></i></button> ';
        html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="remove_product(' + count +
            ', \'' + data.id + '\', \'' + data.total_price + '\')" id="btn_' + count +
            '"><i class="fa fa-minus"></i></button>';
        html += "</td>";
        html += "</tr>"; // Correct closing tag

        // Ensure the table is displayed
        $('#purchaseItem_info_table').css('display', 'block');

        // Append the HTML row to the table
        $('#input_field_table').append(html);

        // Add the email and contact to the arrays
        product_ids.push(data.id);

        sub_total = parseFloat(sub_total) + parseFloat(data.total_price);
        $('#sub_total_amount').val(parseFloat(sub_total).toFixed(2))
        $('.sub_total_amount').text(parseFloat(sub_total).toFixed(2))
        calculate_net_total()
    }

    function remove_product(count, product_id, total_price) {
        // Remove the row with the specific ID
        product_id = parseInt(product_id)
        $.confirm({
            theme: 'modern',
            columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
            icon: 'far fa-question-circle text-danger',
            title: 'Are you Sure!',
            content: 'Do you want to Delete the Selected Product?',
            type: 'red',
            autoClose: 'cancel|10000',
            buttons: {
                confirm: {
                    text: 'Yes',
                    btnClass: 'btn-green',
                    action: function() {
                        $('#row_' + count).remove();

                        // Optionally, you can also remove the email and contact from the arrays
                        product_ids = product_ids.filter(pro_id => pro_id !== product_id);

                        if (product_ids.length == 0) {
                            $('#input_field_table').append(
                                '<tr id="no_product_row"><td colspan="6" class="text-center text-danger text-uppercase">No Product Added!</td></tr>'
                            );
                        }

                        sub_total = parseFloat(sub_total) - parseFloat(total_price);
                        $('#sub_total_amount').val(parseFloat(sub_total))
                        $('.sub_total_amount').text(parseFloat(sub_total).toFixed(2))
                        calculate_net_total()
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

    function edit_product(count) {
        // Retrieve the quantity and retail price using the count
        var qty = document.getElementById('row_qty_' + count).textContent;
        var retail_price = document.querySelector('#row_' + count + ' td:nth-child(4)').textContent;
        var total_price = document.getElementById('row_total_price_' + count).textContent;
        // Parse or manipulate the values as needed
        qty = parseInt(qty);
        retail_price = parseFloat(retail_price);
        total_price = parseFloat(total_price);

        console.log(qty);
        console.log(retail_price);
        console.log(total_price);

        $('#edit_qty').val(qty)
        $('#edit_retail_price').val(retail_price)
        $('#edit_raw_id').val(count)
        $('#edit_total_price').val(total_price)

        $('#updateItemQty').modal('show');
    }

    function save_change() {
        var edit_qty = $('#edit_qty').val()
        var edit_retail_price = $('#edit_retail_price').val()
        var edit_raw_id = $('#edit_raw_id').val()
        var edit_total_price = $('#edit_total_price').val()

        if (edit_qty == '') {
            $('.err_edit_qty').text('The qty field is required.')
        } else if (edit_qty == 0 || edit_qty == '0') {
            $('.err_edit_qty').text('The qty field must be grater than 0.')
        } else {
            $('.err_edit_qty').text('')
            sub_total = parseFloat(sub_total) - parseFloat(edit_total_price);

            edit_qty = parseInt(edit_qty)
            edit_raw_id = parseInt(edit_raw_id)
            edit_retail_price = parseFloat(edit_retail_price)
            edit_total_price = parseFloat(edit_total_price)

            var total_amo = edit_retail_price * edit_qty;

            sub_total = parseFloat(sub_total) + parseFloat(total_amo);

            document.getElementById('row_qty_' + edit_raw_id).textContent = edit_qty
            document.getElementById('row_total_price_' + edit_raw_id).textContent = total_amo.toFixed(2)

            $('#request_qtys_' + edit_raw_id).val(edit_qty)
            $('#sub_total_amount').val(parseFloat(sub_total))
            $('.sub_total_amount').text(parseFloat(sub_total).toFixed(2))

            calculate_net_total()

            $('#updateItemQty').modal('hide');
        }

    }

    function clearError() {
        $('.err_product').text('');
        $('.err_qty').text('');

    }

    function clearInput() {
        $('#product').val('').change();
        $('#qty').val('');
    }
</script>
