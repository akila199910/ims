<div class="row">
    <div class="form-heading">
        <h4>Add Stock Transfer Items</h4>
    </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
        <div class="input-block local-forms">
            <label>Select Product<span class="text-danger">*</span></label>
            <select class="form-control my_select2 product" name="product" id="product">
                <option value="">Select Product</option>
                @foreach ($products as $item)
                    <option value="{{ $item['id'] }}">{{ Str::limit($item['name'],30) }}</option>
                @endforeach
            </select>

            <small class="text-danger font-weight-bold err_product"></small>
            <small class="text-danger err_product_ids"></small>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12">
        <div class="input-block local-forms ">
            <label>Qty<span class="text-danger">*</span></label>
            <input type="hidden" name="av_qty" id="av_qty">
            <input type="text" name="qty" class="form-control qty" id="qty" placeholder="Enter the Qty">
            <small class="text-danger font-weight-bold err_qty"></small>
        </div>
    </div>

    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
        <button class="btn btn-lg btn-secondary text-uppercase btn-bottom" type="button" id="add_button">+</button>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary">Product</th>
                        <th class="text-uppercase text-secondary">Qty</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="input_field_table">

                </tbody>
            </table>
        </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Create_StockTransfer'))
        <div class="col-12 mb-4">
            <div class="doctor-submit text-end">
                <button type="submit" class="btn btn-primary text-uppercase submit-form me-2">Create</button>
            </div>
        </div>
    @endif

</div>

<script>
    $(document).ready(function() {
        $('.my_select2').select2()
    });

    var count = 0;
    var product_array = [];

    $('#product').change(function(e) {
        e.preventDefault();

        if ($(this).val() != '') {
            var data = {
                'product_id': $(this).val(),
                'warehouse_from': $('#warehouse_from').val()
            }

            $('#loader').show()
            $.ajax({
                type: "POST",
                url: "{{ route('business.stock_transfer.get_product') }}",
                data: data,
                dataType: "JSON",
                success: function(response) {

                    $('#loader').hide()
                    $('#qty').val(0)
                    $('#qty').val(response.data.qty)

                    $('#av_qty').val(0)
                    $('#av_qty').val(response.data.qty)

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
    });

    $('#add_button').click(function(e) {
        e.preventDefault();

        var data = {
            'product': $('#product').val(),
            'qty': $('#qty').val(),
            'av_qty': $('#av_qty').val(),
            'product_array': product_array
        }

        $("#loader").show();

        $.ajax({
            type: "POST",
            url: "{{ route('business.stock_transfer.add_update_item') }}",
            data: data,
            dataType: "JSON",
            success: function(response) {

                $("#loader").hide();
                error_input()
                if (response.status == false) {
                    $.each(response.message, function(key, item) {
                        if (key) {
                            $('.err_' + key).text(item)
                        }
                    });
                } else {
                    append_table(response.data)
                    clear_input()
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

    function append_table(data) {
        count++;
        var product_id = data.product_id
        var product_name = data.product_name
        var qty = data.qty

        // Build the HTML string
        var html = "<tr id='row_" + count + "'>";
        html += "<td>";
        html += '<input type="hidden" name="product_ids[]" value="' + product_id + '" >';
        html += product_name;
        html += "</td>";
        html += "<td>";
        html += '<input type="hidden" name="qtys[]" value="' + qty + '" >';
        html += qty;
        html += "</td>";
        html += "<td>";
        // Button to remove the contact using count as row ID
        html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="remove_product(' + count +
            ', \'' + product_id + '\')" id="btn_' + count +
            '"><i class="fa fa-minus"></i></button>';
        html += "</td>";
        html += "</tr>"; // Correct closing tag

        // Append the HTML row to the table
        $('#input_field_table').append(html);

        // Add the email and contact to the arrays
        product_array.push(product_id);
    }

    function clear_input() {
        $('#product').val('').change()
        $('#qty').val('')
    }

    function remove_product(count, product_id) {
        // Remove the row with the specific ID
        var product_id = parseInt(product_id)

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
                        product_array = product_array.filter(product => product !== product_id);

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
