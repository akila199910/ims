<div class="row">
    <div class="form-heading">
        <h4>Add Stock Adjusted Items</h4>
    </div>

    <div class="col-xl-4 col-lg-3 col-md-4 col-sm-12 col-12">
        <div class="input-block local-forms">
            <label>Select Product<span class="text-danger">*</span></label>
            <select class="form-control my_select2 product" name="product" id="product">
                <option value="">Select Product</option>
                @foreach ($products as $item)
                    <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                @endforeach
            </select>

            <small class="text-danger font-weight-bold err_product"></small>
            <small class="text-danger err_product_id"></small>
        </div>
    </div>

    <div class="col-xl-4 col-lg-3 col-md-4 col-sm-12 col-12">
        <div class="input-block local-forms">
            <label>Select Warehouse<span class="text-danger">*</span></label>
            <select class="form-control my_select2 warehouse" name="warehouse" id="warehouse">
                <option value="">Select Warehouse</option>
            </select>

            <small class="text-danger font-weight-bold err_warehouse"></small>
        </div>
    </div>

    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12 col-12">
        <div class="input-block local-forms ">
            <label>Qty<span class="text-danger">*</span></label>
            <input type="hidden" name="av_qty" id="av_qty">
            <input type="hidden" name="order_item_id" id="order_item_id">
            <input type="text" name="qty" class="form-control qty" value="{{ old('qty') }}" id="qty"
                placeholder="Enter the Qty">
            <small class="text-danger font-weight-bold err_qty"></small>
        </div>
    </div>

    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 mb-4">
        <button class="btn btn-lg btn-secondary text-uppercase btn-bottom" type="button" id="add_button">+</button>
    </div>

    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
        <small class="text-danger font-weight-bold err_product_ids"></small>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary">Product</th>
                        <th class="text-uppercase text-secondary">WareHouse</th>
                        <th class="text-uppercase text-secondary">Qty</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="input_field_table">

                </tbody>
            </table>
        </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Create_StockAdjustment'))
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
    var product_warehouse = [];

    $('#product').change(function(e) {
        e.preventDefault();

        if ($(this).val() != '') {
            var data = {
                'product_id': $(this).val(),
                'order_id': $('#purchase_ref').val(),
                'product_array': product_array,
                'product_warehouse': product_warehouse
            }

            $('#loader').show()
            $.ajax({
                type: "POST",
                url: "{{ route('stock_adjusted.get_ware_house') }}",
                data: data,
                dataType: "JSON",
                success: function(response) {
                    console.log(response);
                    $('#loader').hide()
                    $('.warehouse').html('')
                    $('.warehouse').append('<option value="">Select Warehouse</option>');
                    $.each(response.data.ware_house, function(key, item) {
                        $('.warehouse').append('<option value="' + item.id + '">' + item.name +
                            '</option>');
                    });
                    $('#qty').val(0)
                    $('#qty').val(response.data.qty)

                    $('#av_qty').val(0)
                    $('#av_qty').val(response.data.qty)

                    $('#order_item_id').val('')
                    $('#order_item_id').val(response.data.order_item_id)

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
            'warehouse': $('#warehouse').val(),
            'qty': $('#qty').val(),
            'av_qty': $('#av_qty').val(),
            'order_item_id' : $('#order_item_id').val(),
            'product_array': product_array,
            'product_warehouse': product_warehouse
        }

        $('#loader').show()
        $.ajax({
            type: "POST",
            url: "{{ route('stock_adjusted.add_update_item') }}",
            data: data,
            dataType: "JSON",
            success: function(response) {
                $('#loader').hide()
                clear_error()
                if (response.status == false) {
                    $.each(response.message, function(key, item) {
                        if (key) {
                            $('.err_' + key).text(item)
                        }
                    });
                } else {
                    clear_input()
                    append_content(response.data)
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

    function append_content(data) {
        count++;
        var product_id = data.product_id;
        var warehouse_id = data.warehouse_id;
        var qty = data.qty;
        var prod_warehouse = data.product_warehouse_ids;
        var order_item_id = data.order_item_id;

        var html = '<tr id="row_' + count + '">'
        html += '<td>'
        html += '<input type="hidden" name="product_ids[]" value="' + data.product_id + '">'
        html += '<input type="hidden" name="order_item_ids[]" value="' + data.order_item_id + '">'
        html += data.product_name
        html += '</td>'
        html += '<td>'
        html += '<input type="hidden" name="warehouse_ids[]" value="' + data.warehouse_id + '">'
        html += data.warehouse_name
        html += '</td>'
        html += '<td>'
        html += '<input type="hidden" name="qtys[]" value="' + data.qty + '">'
        html += data.qty
        html += '</td>'
        html += "<td>";
        // Button to remove the contact using count as row ID
        html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="remove_product(' + count +
            ', \'' + product_id + '\', \'' + warehouse_id + '\', \'' + prod_warehouse + '\')" id="btn_' + count +
            '"><i class="fa fa-minus"></i></button>';
        html += "</td>";
        html += '</tr>'

        $('#input_field_table').append(html);
        // Create a product object
        var product_object = {
            'product_id': parseInt(product_id),
            'warehouse_id': parseInt(warehouse_id),
            'qty': parseInt(qty)
        };

        // Push the product object directly to add_product
        product_array.push(product_object);
        product_warehouse.push(prod_warehouse)
        console.log(product_array);

    }

    function remove_product(count, product_id, warehouse_id, prod_warehouse) {
        var productToRemove = {
            product_id: parseInt(product_id),
            warehouse_id: parseInt(warehouse_id)
        };

        $.confirm({
            theme: 'modern',
            columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
            icon: 'far fa-question-circle text-danger',
            title: 'Are you Sure!',
            content: 'Do you want to Delete the Selected Stock Item?',
            type: 'red',
            autoClose: 'cancel|10000',
            buttons: {
                confirm: {
                    text: 'Yes',
                    btnClass: 'btn-green',
                    action: function() {
                        $('#row_' + count).remove();

                        // Filter out the object you want to remove
                        product_array = product_array.filter(product =>
                            product.product_id !== productToRemove.product_id ||
                            product.warehouse_id !== productToRemove.warehouse_id
                        );

                         // Filter out the object you want to remove
                        product_warehouse = product_warehouse.filter(pro_id => pro_id !== prod_warehouse);
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

    function clear_error() {
        $('.err_product').text('')
        $('.err_warehouse').text('')
        $('.err_qty').text('')
    }

    function clear_input() {
        $('#product').val('').change()
        $('#warehouse').val('').change()
        $('#qty').val('')
    }
</script>
