<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('layout_style/img/wage_icon.png') }}">
    <title>
        Food Cost Calculator | {{ env('APP_NAME') }}
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="stylesheet" type="text/css" href="{{ asset('layout_style/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('layout_style/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('layout_style/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('layout_style/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('layout_style/css/feather.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('layout_style/css/style.css') }}"> --}}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="{{ asset('layout_style/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('layout_style/css/style.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('layout_style/jquery_confirm/style.css') }}">
    <link rel="stylesheet" href="{{ asset('layout_style/css/my-style.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('layout_style/css/bootstrap-datetimepicker.min.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.js"></script>
    <script src="{{ asset('layout_style/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('layout_style/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="{{ asset('layout_style/js/validations.js') }}"></script>
    <style>
        .form-control {
            height: 40px !important;
        }

        .form-group label {
            font-size: 12px !important;
        }

        /* Custom Radio Section/Preference */
        .custom-radio-section-cost {
            display: flex;
            align-items: center;
        }

        .custom-radio-section-cost input[type="radio"] {
            display: none;
            /* Hide the default radio button */
        }

        .custom-radio-section-cost label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3px 5px;
            /* Consistent padding for the buttons */
            font-size: 9px !important;
            /* Adjust font size */
            font-weight: 500;
            /* Bold text */
            border: 1px solid #ddd;
            /* Light border */
            border-radius: 0;
            /* Remove border radius for square buttons */
            cursor: pointer;
            background-color: #f8f9fa;
            /* Light background for default state */
            color: #6c757d;
            /* Default text color (gray) */
            transition: all 0.3s ease;
            /* Smooth transition for hover and active state */
            margin: 0;
            /* Remove any default margin */
            height: 45px !important;
        }

        /* Add left border for the first button */
        .custom-radio-section-cost label:first-of-type {
            border-left: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
            /* Rounded corners for the first label */
        }

        /* Add right border for the last button */
        .custom-radio-section-cost label:last-of-type {
            border-radius: 0 5px 5px 0;
            /* Rounded corners for the last label */
        }

        /* Style for the selected radio button */
        .custom-radio-section-cost input[type="radio"]:checked+label {
            background-color: #f5f3fe;
            /* Light purple background for selected */
            color: #6c30ff;
            /* Purple text color */
            border-color: #6c30ff;
        }

        /* Hover effect for labels */
        .custom-radio-section-cost label:hover {
            background-color: #e9ecef;
            /* Slightly darker background on hover */
            border-color: #bbb;
            /* Subtle border change on hover */
        }

        /* Style for disabled radio buttons */
        .custom-radio-section-cost input[type="radio"]:disabled+label {
            background-color: #e4e4e4;
            /* Gray background for disabled state */
            color: #A39EB3;
            /* Gray text for disabled state */
            border: 1px solid #f4f2fa;
            /* Match border with background */
            cursor: not-allowed;
            /* Change cursor to indicate disabled state */
        }

        .readonly_class input[type="input"]:read-only {
            background-color: #6c30ff;
        }

        .err_class {
            font-size: 11px !important
        }
    </style>
</head>

<body>
    <div class="col-12 text-center bg-body-secondary">
        <h1 class="p-5">Food Cost Calculator</h1>
    </div>

    <div class="container mt-5">
        <div class="col-12">
            <div class="row">
                <div class="col-xl-10 col-lg-10 col-md-12 col-sm-12">
                    <form id="submitForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <h5 class="text-primary">Recipe Details</h5>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-2">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="menu_item">Menu Item Name</label>
                                            <input type="text" name="menu_item" id="menu_item" class="form-control"
                                                placeholder="Ex: Lava Cake">
                                            <small class="err_class text-danger err_menu_item"></small>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="menu_price">Menu Price excl. taxes</label>
                                            <input type="text" name="menu_price" id="menu_price"
                                                class="form-control decimal_val" placeholder="0">
                                            <small class="err_class text-danger err_menu_price"></small>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label for="food_cost">Target Food Cost (%)</label>
                                            <input type="text" name="food_cost" id="food_cost"
                                                class="form-control decimal_val" placeholder="0">
                                            <small class="err_class text-danger err_food_cost"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4">
                                <h5 class="text-primary">Pricing Details</h5>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 add_dynamic_content_div">
                                <div class="row pricing_details">
                                    <input type="hidden" name="closed_value[]" data-id="0" id="closed_value"
                                        class="closed_value" value="0">
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                        <div class="form-group">
                                            <label for="ingredients_name_0">Ingredients Name</label>
                                            <input type="text" name="ingredients_name_0" id="ingredients_name_0"
                                                class="form-control" placeholder="Ex: Baking Powder">
                                            <small class="err_class text-danger err_ingredients_name_0"></small>
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                                        <div class="form-group">
                                            <label for="purchase_price_0">Purchase Price</label>
                                            <input type="text" name="purchase_price_0" id="purchase_price_0"
                                                class="form-control purchase_price decimal_val" placeholder="0">
                                            <small class="err_class text-danger err_purchase_price_0"></small>
                                        </div>
                                    </div>

                                    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                                        <div class="row">
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label for="quantity_purchased_0">Quantity Purchased</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control quantity_purchased"
                                                            name="quantity_purchased_0" placeholder="0"
                                                            id="quantity_purchased_0">

                                                        <!-- Custom radio section -->
                                                        <div class="custom-radio-section-cost quantity_purchased_radios"
                                                            data-id="0">

                                                            <input type="radio" id="unit_0"
                                                                name="radio_quantity_purchased_0" value="unit"
                                                                checked class="purchase_qty">
                                                            <label for="unit_0">UNIT</label>

                                                            <input type="radio" id="g_0"
                                                                name="radio_quantity_purchased_0" value="g"
                                                                class="purchase_qty">
                                                            <label for="g_0">G</label>

                                                            <input type="radio" id="kg_0"
                                                                name="radio_quantity_purchased_0" value="kg"
                                                                class="purchase_qty">
                                                            <label for="kg_0">KG</label>

                                                            <input type="radio" id="ml_0"
                                                                name="radio_quantity_purchased_0" value="ml"
                                                                class="purchase_qty">
                                                            <label for="ml_0">ML</label>

                                                            <input type="radio" id="l_0"
                                                                name="radio_quantity_purchased_0" value="l"
                                                                class="purchase_qty">
                                                            <label for="l_0">L</label>
                                                        </div>
                                                    </div>
                                                    <small
                                                        class="err_class text-danger err_quantity_purchased_0"></small>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group">
                                                    <label for="quantity_used_0">Quantity Used</label>
                                                    <div class="input-group">
                                                        <input type="text"
                                                            class="form-control decimal_val quantity_used"
                                                            name="quantity_used_0" placeholder="0"
                                                            id="quantity_used_0">

                                                        <!-- Custom radio section -->
                                                        <div class="custom-radio-section-cost quantity_used_radios"
                                                            data-id="0">
                                                            <input type="radio" id="unit_used_0" value="unit"
                                                                name="radio_quantity_used_0" class="used_qty">
                                                            <label for="unit_used_0">UNIT</label>

                                                            <input type="radio" id="g_used_0" value="g"
                                                                name="radio_quantity_used_0" class="used_qty"
                                                                disabled>
                                                            <label for="g_used_0">G</label>

                                                            <input type="radio" id="kg_used_0" value="kg"
                                                                name="radio_quantity_used_0" class="used_qty"
                                                                disabled>
                                                            <label for="kg_used_0">KG</label>

                                                            <input type="radio" id="ml_used_0" value="ml"
                                                                name="radio_quantity_used_0" class="used_qty"
                                                                disabled>
                                                            <label for="ml_used_0">ML</label>

                                                            <input type="radio" id="l_used_0" value="l"
                                                                name="radio_quantity_used_0" class="used_qty"
                                                                disabled>
                                                            <label for="l_used_0">L</label>
                                                        </div>
                                                    </div>
                                                    <small class="err_class text-danger err_quantity_used_0"></small>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                                        <div class="form-group readonly_class">
                                            <label for="ingredient_cost_0">Cost</label>
                                            <input type="text" name="ingredient_cost_0" value="0.00" readonly
                                                id="ingredient_cost_0"
                                                class="form-control ingredient_cost text-center">
                                            <small class="err_class text-danger err_ingredient_cost"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4">
                                <div class="row">
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12"></div>
                                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                        <button type="button" onclick="create_new_content()"
                                            class="btn btn-outline-primary btn-lg btn-block">
                                            <span>Click here to add one more ingredient</span>
                                            <i class="fa fa-plus-circle"></i>
                                        </button>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12"></div>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4">
                                <h5 class="text-primary">Additional</h5>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="comments">Comments</label>
                                    <input type="text" name="comments" id="comments" class="form-control"
                                        placeholder="Enter the Comments">
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3 actual_cost_div"
                                style="display: none">
                                <div class="row">
                                    <div class="col-xl-8 col-lg-8 col-md-7 col-sm-12">
                                        <h4 class="text-primary">Actual Food Cost</h4>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 text-end">
                                        <h4 class="text-dark actual_price"></h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4 mb-5">
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-calculator"></i>
                                        Calculator</button>
                                    <button class="btn btn-outline-primary" onclick="location.reload()" type="button"><i
                                            class="fa fa-refresh"></i>
                                        Clear all</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-xl-2 col-lg-2 col-md-12 col-sm-12">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <h5 class="text-primary">Results</h5>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4 d-flex">
                            <div id="food_cost_chart" class="chart-user-icon">
                                <img src="{{asset('layout_style/img/icons/food.png')}}" style="height: 50px; width: 50px" alt>
                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                    <img src="{{asset('layout_style/img/icons/food_cost.png')}}" style="height: 50px; width: 50px" alt="">
                                </div>
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                    <h6>Actual Food Cost (%)</h6>
                                    <span class="actual_food_cost">---</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                    <img src="{{asset('layout_style/img/icons/profit.png')}}" style="height: 50px; width: 50px" alt="">
                                </div>
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                    <h6>Expected Profit</h6>
                                    <span class="expected_profit">---</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                    <img src="{{asset('layout_style/img/icons/selling_price.png')}}" style="height: 50px; width: 50px" alt="">
                                </div>
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                    <h6>Expected Selling Price</h6>
                                    <span class="expected_selling_price">---</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                    <img src="{{asset('layout_style/img/icons/food_cost_.png')}}" style="height: 50px; width: 50px" alt="">
                                </div>
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                    <h6>Expected Food Cost</h6>
                                    <span class="expected_food_cost">---</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-4 mb-5 _download_btn" style="display: none">
                            <div class="form-group text-end">
                                <button class="btn btn-outline-primary" onclick="download_pdf()" type="button"><i
                                        class="fa fa-download"></i>
                                    Download</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--loader-->
    <div class="ajax-loader" id="loader" style="display: none">
        <div class="max-loader">
            <div class="loader-inner">
                <div class="spinner-border text-white" role="status"></div>
                <p>Please Wait........</p>
            </div>
        </div>
    </div>
    <!--end loader-->

    <script src="{{ asset('layout_style/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('layout_style/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('layout_style/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('layout_style/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('layout_style/js/app.js') }}"></script>
    <script src="{{ asset('layout_style/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('layout_style/plugins/select2/js/custom-select.js') }}"></script>
    <script src="{{ asset('layout_style/jquery_confirm/script.js') }}"></script>
    <script src="{{ asset('layout_style/jquery_confirm/popup.js') }}"></script>

    <script src="{{ asset('layout_style/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('layout_style/js/jquery.waypoints.js') }}"></script>
    <script src="{{ asset('layout_style/js/jquery.counterup.min.js') }}"></script>

    <script src="{{ asset('layout_style/cdn_scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js') }}"></script>
    <script src="{{ asset('layout_style/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('layout_style/plugins/apexchart/chart-data.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @include('common.scripting')
</body>

</html>
