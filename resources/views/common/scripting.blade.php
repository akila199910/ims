<script>
    var data_series = [100];
    var data_labels = ['-'];

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

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

        loadChart(data_series, data_labels)
    });

    let count = 0;

    function create_new_content() {
        count++;
        let inHtml = '';
        inHtml += '<div class="row mt-2 pricing_details row_count_' + count + '">';
        inHtml += '<input type="hidden" name="closed_value[]" id="closed_value" class="closed_value" value="' + count +
            '"  data-id="' + count + '">';
        inHtml += '<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">';
        inHtml += '<div class="form-group">';
        inHtml += '<label for="ingredients_name_' + count + '">Ingredients Name</label>';
        inHtml += '<input type="text" name="ingredients_name_' + count + '" id="ingredients_name_' + count +
            '" class="form-control" placeholder="Ex: Baking Powder">';
        inHtml += '<small class="err_class text-danger err_ingredients_name_' + count + '"></small>';
        inHtml += '</div>';
        inHtml += '</div>';

        inHtml += '<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">';
        inHtml += '<div class="form-group">';
        inHtml += '<label for="purchase_price_' + count + '">Purchase Price</label>';
        inHtml += '<input type="text" name="purchase_price_' + count + '" id="purchase_price_' + count +
            '" class="form-control purchase_price decimal_val" placeholder="0">';
        inHtml += '<small class="err_class text-danger err_purchase_price_' + count + '"></small>';
        inHtml += '</div>';
        inHtml += '</div>';

        inHtml += '<div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">';
        inHtml += '<div class="row">';
        inHtml += '<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">';
        inHtml += '<div class="form-group">';
        inHtml += '<label for="quantity_purchased_' + count + '">Quantity Purchased</label>';
        inHtml += '<div class="input-group">';
        inHtml +=
            '<input type="text" class="form-control quantity_purchased decimal_val" name="quantity_purchased_' + count +
            '" placeholder="0" id="quantity_purchased_' +
            count + '">';
        inHtml += '<div class="custom-radio-section-cost quantity_purchased_radios" data-id="' + count + '">';

        inHtml += '<input type="radio" id="unit_' + count + '" value="unit" name="radio_quantity_purchased_' + count +
            '" checked class="purchase_qty">';
        inHtml += '<label for="unit_' + count + '">UNIT</label>';
        inHtml += '<input type="radio" id="g_' + count + '" value="g" name="radio_quantity_purchased_' + count +
            '" class="purchase_qty">';
        inHtml += '<label for="g_' + count + '">G</label>';
        inHtml += '<input type="radio" id="kg_' + count + '" value="kg" name="radio_quantity_purchased_' + count +
            '" class="purchase_qty">';
        inHtml += '<label for="kg_' + count + '">KG</label>';
        inHtml += '<input type="radio" id="ml_' + count + '" value="ml" name="radio_quantity_purchased_' + count +
            '" class="purchase_qty">';
        inHtml += '<label for="ml_' + count + '">ML</label>';
        inHtml += '<input type="radio" id="l_' + count + '" value="l" name="radio_quantity_purchased_' + count +
            '" class="purchase_qty">';
        inHtml += '<label for="l_' + count + '">L</label>';
        inHtml += '</div>';
        inHtml += '</div>';
        inHtml += '<small class="err_class text-danger err_quantity_purchased_' + count + '"></small>';
        inHtml += '</div>';
        inHtml += '</div>';

        inHtml += '<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">';
        inHtml += '<div class="form-group">';
        inHtml += '<label for="quantity_used_' + count + '">Quantity Used</label>';
        inHtml += '<div class="input-group">';
        inHtml +=
            '<input type="text" class="form-control decimal_val quantity_used" name="quantity_used_' + count +
            '" placeholder="0"  id="quantity_used_' +
            count + '">';
        inHtml += '<div class="custom-radio-section-cost quantity_used_radios" data-id="' + count + '">';
        inHtml += '<input type="radio" id="unit_used_' + count + '" value="unit" name="radio_quantity_used_' + count +
            '" class="used_qty">';
        inHtml += '<label for="unit_used_' + count + '">UNIT</label>';
        inHtml += '<input type="radio" id="g_used_' + count + '" value="g" name="radio_quantity_used_' + count +
            '" disabled class="used_qty">';
        inHtml += '<label for="g_used_' + count + '">G</label>';
        inHtml += '<input type="radio" id="kg_used_' + count + '" value="kg" name="radio_quantity_used_' + count +
            '" disabled class="used_qty">';
        inHtml += '<label for="kg_used_' + count + '">KG</label>';
        inHtml += '<input type="radio" id="ml_used_' + count + '" value="ml" name="radio_quantity_used_' + count +
            '" disabled class="used_qty">';
        inHtml += '<label for="ml_used_' + count + '">ML</label>';
        inHtml += '<input type="radio" id="l_used_' + count + '" value="l" name="radio_quantity_used_' + count +
            '" disabled class="used_qty">';
        inHtml += '<label for="l_used_' + count + '">L</label>';
        inHtml += '</div>';
        inHtml += '</div>';
        inHtml += '<small class="err_class text-danger err_quantity_used_' + count + '"></small>';
        inHtml += '</div>';
        inHtml += '</div>';
        inHtml += '</div>';
        inHtml += '</div>';

        inHtml += '<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">';
        inHtml += '<div class="form-group">';
        inHtml += '<label for="ingredient_cost_' + count + '">Cost</label>';
        inHtml += '<div class="input-group">';
        inHtml += '<input type="text" name="ingredient_cost_' + count + '" value="0.00" id="ingredient_cost_' + count +
            '" class="form-control text-center ingredient_cost" readonly>';
        inHtml += '<button class="btn btn-outline-danger" onclick="remove_row(' + count +
            ')"><i class="fa fa-trash"></i></button>';
        inHtml += '</div>';
        inHtml += '</div>';
        inHtml += '</div>';
        inHtml += '</div>';

        // Append the new HTML to the container
        document.querySelector('.add_dynamic_content_div').insertAdjacentHTML('beforeend', inHtml);
    }

    // Function to remove a row
    function remove_row(rowCount) {
        const row = document.querySelector('.row_count_' + rowCount);
        if (row) row.remove();
    }

    $(document).on('change', '.purchase_qty', function(e) {
        e.preventDefault();

        // Get the value of the selected radio button
        var selectedval = $(this).val();

        // Find the closest ingredient name or identifier
        var closed_value = $(this).closest('.pricing_details').find('.closed_value').attr('data-id');

        // Check if closed_value exists to avoid errors
        if (!closed_value) {
            console.error('Ingredient Name not found!');
            return;
        }

        // Handle enabling and disabling other inputs based on the selected radio button
        if (selectedval == 'unit') {
            $('#unit_used_' + closed_value).removeAttr('disabled');

            $('#g_used_' + closed_value).attr('disabled', true);
            $('#kg_used_' + closed_value).attr('disabled', true);
            $('#ml_used_' + closed_value).attr('disabled', true);
            $('#l_used_' + closed_value).attr('disabled', true);
        } else if (selectedval == 'g') {
            $('#g_used_' + closed_value).removeAttr('disabled');

            $('#unit_used_' + closed_value).attr('disabled', true);
            $('#kg_used_' + closed_value).attr('disabled', true);
            $('#ml_used_' + closed_value).attr('disabled', true);
            $('#l_used_' + closed_value).attr('disabled', true);
        } else if (selectedval == 'kg') {
            $('#g_used_' + closed_value).removeAttr('disabled');
            $('#kg_used_' + closed_value).removeAttr('disabled');

            $('#unit_used_' + closed_value).attr('disabled', true);
            $('#ml_used_' + closed_value).attr('disabled', true);
            $('#l_used_' + closed_value).attr('disabled', true);
        } else if (selectedval == 'l') {
            $('#l_used_' + closed_value).removeAttr('disabled');
            $('#ml_used_' + closed_value).removeAttr('disabled');

            $('#unit_used_' + closed_value).attr('disabled', true);
            $('#kg_used_' + closed_value).attr('disabled', true);
            $('#g_used_' + closed_value).attr('disabled', true);
        } else if (selectedval == 'ml') {
            $('#ml_used_' + closed_value).removeAttr('disabled');

            $('#l_used_' + closed_value).attr('disabled', true);
            $('#unit_used_' + closed_value).attr('disabled', true);
            $('#kg_used_' + closed_value).attr('disabled', true);
            $('#g_used_' + closed_value).attr('disabled', true);
        }

        // Retrieve all associated field values
        const fieldValues = get_field_values(closed_value);

        var purchase_price = fieldValues['purchase_price']
        var quantity_purchased = fieldValues['quantity_purchased']
        var select_qty_purchased = fieldValues['select_qty_purchased']
        var quantity_used = fieldValues['quantity_used']
        var select_qty_used = fieldValues['select_qty_used']

        const oneCost = single_cost_calculation(purchase_price, quantity_purchased, select_qty_purchased,
            quantity_used,
            select_qty_used)

        var one_cost = parseFloat(oneCost.toFixed(2))

        $('#ingredient_cost_' + closed_value).val(oneCost.toFixed(2))
    });

    $(document).on('keyup', '.purchase_price', function(e) {
        e.preventDefault()

        // Find the closest ingredient name or identifier
        var closed_value = $(this).closest('.pricing_details').find('.closed_value').attr('data-id');

        // Check if closed_value exists to avoid errors
        if (!closed_value) {
            console.error('Ingredient Name not found!');
            return;
        }

        // Retrieve all associated field values
        const fieldValues = get_field_values(closed_value);
        console.log(fieldValues);

        var purchase_price = fieldValues['purchase_price']
        var quantity_purchased = fieldValues['quantity_purchased']
        var select_qty_purchased = fieldValues['select_qty_purchased']
        var quantity_used = fieldValues['quantity_used']
        var select_qty_used = fieldValues['select_qty_used']

        const oneCost = single_cost_calculation(purchase_price, quantity_purchased, select_qty_purchased,
            quantity_used,
            select_qty_used)

        var one_cost = parseFloat(oneCost.toFixed(2))

        $('#ingredient_cost_' + closed_value).val(oneCost.toFixed(2))

    });

    $(document).on('keyup', '.quantity_purchased', function(e) {
        e.preventDefault()

        // Find the closest ingredient name or identifier
        var closed_value = $(this).closest('.pricing_details').find('.closed_value').attr('data-id');

        // Check if closed_value exists to avoid errors
        if (!closed_value) {
            console.error('Ingredient Name not found!');
            return;
        }

        // Retrieve all associated field values
        const fieldValues = get_field_values(closed_value);

        var purchase_price = fieldValues['purchase_price']
        var quantity_purchased = fieldValues['quantity_purchased']
        var select_qty_purchased = fieldValues['select_qty_purchased']
        var quantity_used = fieldValues['quantity_used']
        var select_qty_used = fieldValues['select_qty_used']

        const oneCost = single_cost_calculation(purchase_price, quantity_purchased, select_qty_purchased,
            quantity_used,
            select_qty_used)

        var one_cost = parseFloat(oneCost.toFixed(2))

        $('#ingredient_cost_' + closed_value).val(oneCost.toFixed(2))

    });

    $(document).on('keyup', '.quantity_used', function(e) {
        e.preventDefault()

        // Find the closest ingredient name or identifier
        var closed_value = $(this).closest('.pricing_details').find('.closed_value').attr('data-id');

        // Check if closed_value exists to avoid errors
        if (!closed_value) {
            console.error('Ingredient Name not found!');
            return;
        }

        // Retrieve all associated field values
        const fieldValues = get_field_values(closed_value);

        var purchase_price = fieldValues['purchase_price']
        var quantity_purchased = fieldValues['quantity_purchased']
        var select_qty_purchased = fieldValues['select_qty_purchased']
        var quantity_used = fieldValues['quantity_used']
        var select_qty_used = fieldValues['select_qty_used']

        const oneCost = single_cost_calculation(purchase_price, quantity_purchased, select_qty_purchased,
            quantity_used,
            select_qty_used)

        var one_cost = parseFloat(oneCost.toFixed(2))

        $('#ingredient_cost_' + closed_value).val(oneCost.toFixed(2))

    });

    $(document).on('change', '.used_qty', function(e) {
        e.preventDefault();

        // Get the value of the selected radio button
        var selectedval = $(this).val();

        // Find the closest ingredient name or identifier
        var closed_value = $(this).closest('.pricing_details').find('.closed_value').attr('data-id');

        // Check if closed_value exists to avoid errors
        if (!closed_value) {
            console.error('Ingredient Name not found!');
            return;
        }

        // Retrieve all associated field values
        const fieldValues = get_field_values(closed_value);

        var purchase_price = fieldValues['purchase_price']
        var quantity_purchased = fieldValues['quantity_purchased']
        var select_qty_purchased = fieldValues['select_qty_purchased']
        var quantity_used = fieldValues['quantity_used']
        var select_qty_used = fieldValues['select_qty_used']

        const oneCost = single_cost_calculation(purchase_price, quantity_purchased, select_qty_purchased,
            quantity_used,
            select_qty_used)

        var one_cost = parseFloat(oneCost.toFixed(2))

        $('#ingredient_cost_' + closed_value).val(oneCost.toFixed(2))

    });

    function get_field_values(closed_value) {
        var purchase_price = $('#purchase_price_' + closed_value).val()

        if (purchase_price == "" || purchase_price == 0) {
            var purchase_price = parseFloat(0);
        } else {
            var purchase_price = parseFloat(purchase_price);
        }

        // Quantity Purchased
        var quantity_purchased = $('#quantity_purchased_' + closed_value).val()

        if (quantity_purchased == "" || quantity_purchased == 0) {
            var quantity_purchased = parseFloat(0);
        } else {
            var quantity_purchased = parseFloat(quantity_purchased);
        }

        var select_qty_purchased = $('input[name="radio_quantity_purchased_' + closed_value + '"]:checked').val();
        // End

        // Quantity User
        var quantity_used = $('#quantity_used_' + closed_value).val()

        if (quantity_used == "" || quantity_used == 0) {
            var quantity_used = parseFloat(0);
        } else {
            var quantity_used = parseFloat(quantity_used);
        }

        var select_qty_used = $('input[name="radio_quantity_used_' + closed_value + '"]:checked').val();

        if (select_qty_used) {
            select_qty_used = select_qty_used;
        } else {
            select_qty_used = null;
        }
        // End

        return {
            purchase_price: purchase_price,
            quantity_purchased: quantity_purchased,
            select_qty_purchased: select_qty_purchased,
            quantity_used: quantity_used,
            select_qty_used: select_qty_used
        }
    }

    function single_cost_calculation(purchase_price, quantity_purchased, select_qty_purchased, quantity_used,
        select_qty_used) {
        /*
            Set variable for geting 1 item price
            if select_qty_purchased = unit it for 1 unit price
            if select_qty_purchased = g || kg then it for 1g price
            if select_qty_purchased = l || ml then it for 1ml price
        */
        var one_volume_price = 0;
        if (select_qty_purchased == 'unit') {
            // getting one_unit price
            one_volume_price = parseFloat(purchase_price / quantity_purchased);

        } else if (select_qty_purchased == 'g') {
            one_volume_price = parseFloat(purchase_price / quantity_purchased);

        } else if (select_qty_purchased == 'kg') {
            quantity_purchased = parseFloat(quantity_purchased * 1000);
            one_volume_price = parseFloat(purchase_price / quantity_purchased);

        } else if (select_qty_purchased == 'l') {
            quantity_purchased = parseFloat(quantity_purchased * 1000);
            one_volume_price = parseFloat(purchase_price / quantity_purchased);

        } else if (select_qty_purchased == 'ml') {
            one_volume_price = parseFloat(purchase_price / quantity_purchased);

        }

        var one_cost = 0;

        if (select_qty_used != null) {
            if (select_qty_used == 'unit') {
                // getting one_unit price
                one_cost = parseFloat(one_volume_price * quantity_used);

            } else if (select_qty_used == 'g') {
                one_cost = parseFloat(one_volume_price * quantity_used);

            } else if (select_qty_used == 'kg') {
                quantity_used = quantity_used * 1000;
                one_cost = parseFloat(one_volume_price * quantity_used);

            } else if (select_qty_used == 'l') {
                quantity_used = quantity_used * 1000;
                one_cost = parseFloat(one_volume_price * quantity_used);

            } else if (select_qty_used == 'ml') {
                one_cost = parseFloat(one_volume_price * quantity_used);

            }
        }

        one_cost = one_cost
        if (!isFinite(one_cost)) { // `isFinite()` checks if a value is not Infinity or NaN
            one_cost = 0; // Set to 0 if the value is Infinity
        }

        return one_cost;
    }

    $('#submitForm').submit(function(e) {
        e.preventDefault();
        let formData = new FormData($('#submitForm')[0]);

        $.ajax({
            type: "POST",
            beforeSend: function() {
                $("#loader").show();
            },
            url: "{{ route('cost_calculator.calculation') }}",
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
                    $('.actual_price').text(response.data.total_cost)
                    $('.actual_cost_div').show()
                    $('.actual_food_cost').text(response.data.actual_food_cost_percentage)
                    $('.expected_profit').text(response.data.profit)
                    $('.expected_selling_price').text(response.data.selling_price)
                    $('.expected_food_cost').text(response.data.expected_food_cost)

                    var data_series = [];
                    data_series = response.data.item_cost_list;

                    var data_labels = [];
                    data_labels = response.data.item_name_list

                    loadChart(data_series, data_labels)

                    $('._download_btn').show()
                }
            },
            statusCode: {
                401: function() {
                    window.location.href =
                        '{{ route('login') }}'; //or what ever is your login URI
                },
                419: function() {
                    errorPopup('Session Time out. Login to the system back','{{ route('login') }}')
                    window.location.href =
                        '{{ route('login') }}'; //or what ever is your login URI
                },
            },
            error: function(data) {
                someThingWrong()
            }
        });
    });

    function errorClear() {
        $('#menu_item').removeClass('is-invalid')
        $('.err_menu_item').text('')

        $('#menu_price').removeClass('is-invalid')
        $('.err_menu_price').text('')

        $('#food_cost').removeClass('is-invalid')
        $('.err_food_cost').text('')

        var closedValues = $('.closed_value').map(function() {
            return $(this).val(); // Get the value of each input
        }).get(); // Convert the jQuery object to a standard array

        $.each(closedValues, function(key, item) {
            var filed_id = parseInt(item)

            $('#ingredients_name_' + filed_id).removeClass('is-invalid')
            $('.err_ingredients_name_' + filed_id).text('')

            $('#purchase_price_' + filed_id).removeClass('is-invalid')
            $('.err_purchase_price_' + filed_id).text('')

            $('#quantity_purchased_' + filed_id).removeClass('is-invalid')
            $('.err_quantity_purchased_' + filed_id).text('')

            $('#radio_quantity_purchased_' + filed_id).removeClass('is-invalid')
            $('.err_radio_quantity_purchased_' + filed_id).text('')

            $('#quantity_used_' + filed_id).removeClass('is-invalid')
            $('.err_quantity_used_' + filed_id).text('')

            $('#radio_quantity_used_' + filed_id).removeClass('is-invalid')
            $('.err_radio_quantity_used_' + filed_id).text('')
        });

    }

    var donut; // Declare a global variable to keep track of the chart instance

    function loadChart(data_series, data_labels) {
        if ($('#food_cost_chart').length > 0) {
            // Check if a chart instance already exists and destroy it
            if (donut) {
                donut.destroy();
            }

            var donutChart = {
                chart: {
                    height: 250,
                    type: 'donut',
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '5%'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                series: data_series,
                labels: data_labels,
                responsive: [{
                    breakpoint: 450,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                legend: {
                    position: 'bottom',
                }
            };

            // Create a new chart instance
            donut = new ApexCharts(document.querySelector("#food_cost_chart"), donutChart);
            donut.render();
        }
    }

    function download_pdf() {
        $('#loader').show()
        donut.dataURI().then((uri) => {

            fetch('/export_calculation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: JSON.stringify({
                        chart_image: uri.imgURI
                    }),
                })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.blob();
                })
                .then((blob) => {
                    $('#loader').hide()
                    var name = Date.now()
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = `calculation` + name + `.pdf`;;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch((error) => {
                    $('#loader').hide()
                    console.error("An error occurred:", error.message);
                    alert("Failed to download the chart. Please try again.");
                });
        });
    }
</script>
