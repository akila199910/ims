<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('layout_style/img/wage_icon.png') }}">
    <title>
        @yield('title') | {{ env('APP_NAME') }}
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
    <script src="{{ asset('layout_style/js/fileupload.js') }}"></script>

    <script type="text/javascript">
        window.history.forward();

        function noBack() {
            window.history.forward();
            window.menubar.visible = false;
        }
    </script>


    @yield('style')

</head>

<body onLoad="noBack();" onpageshow="if (event.persisted) noBack();" onUnload="">
    <div class="main-wrapper">
        <div class="header admin-dashboard">
            <div class="header-left">
                <a href="javascript:;" class="logo">
                    <img src="{{ asset('layout_style/img/wage_icon.png') }}" width="35" height="35" alt>
                    <span>{{ env('APP_NAME') }}</span>
                </a>
            </div>
            <a id="toggle_btn" href="javascript:void(0);"><img src="{{ asset('layout_style/img/icons/menu-bar.svg') }}"
                    style="width: 40px;" alt></a>
            <a id="mobile_btn" class="mobile_btn float-start" href="#sidebar"><img
                    src="{{ asset('layout_style/img/icons/menu-bar.svg') }}" style="width:24px" alt></a>

            @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin')||Auth::user()->hasRole('business_user'))
                <div class="top-nav-search mob-view">
                    <form>
                        {{-- <input type="text" class="form-control" placeholder="Search here"> --}}
                        <select class="form-control js-example-basic-single select2" id="change_dashboard"
                            placeholder="Search here">
                            <option value="">-- Select the Business --</option>
                            @foreach ($businesses as $item)
                                <option value="{{ $item->id }}"
                                    {{ $item->id == session()->get('_business_id') ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        {{-- <a class="btn"><img src="{{ asset('ems_style/img/icons/search-normal.svg') }}" alt></a> --}}
                    </form>
                </div>
            @endif

            <ul class="nav user-menu float-end">
                <li class="nav-item dropdown d-none d-md-block">
                    <!--span class="notification-label"></span-->
                    <div class="dropdown-container nav-link">
                        <a href="#" data-dropdown="notificationMenu" class="menu-link has-notifications circle">
                            <img src="{{ asset('layout_style/img/icons/bell-icon.png') }}" style="width: 32px" alt>
                            @if (count($lowStocks))
                                <span class="pulse"></span>
                            @endif

                        </a>
                        <div class="dropdown-menu notifications show dropdown" name="notificationMenu"
                            style="position: absolute; inset:-35px -30px auto auto; margin: 0px; transform: translate3d(-72px, 72px, 0px);"
                            data-popper-placement="bottom-start">
                            <div class="topnav-dropdown-header">
                                <span style="color: #2072AF">Low Stocks Details</span>
                            </div>
                            <div class="drop-scroll">
                                <ul class="notification-list">
                                    @forelse($lowStocks as $item)
                                        <li class="notification-list-item p-2">
                                            <div class="">
                                                <p class="message">Product Names : {{ $item->product_info->name }}
                                                    (Stock:
                                                    {{ $item->qty }})
                                                </p>
                                                <p class="message">Warehouse : {{ $item->warehouse_info->name }}</p>
                                            </div>

                                        </li>
                                    @empty
                                        <li class="notification-list-item" style="text-align: center">
                                            <h4 class="message">No low-stock items.</h4>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                            @if (count($lowStocks))
                                <div class="topnav-dropdown-footer">
                                    <a href="{{ route('business.low_stock') }}">View all Notifications</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </li>
                {{-- <li class="nav-item dropdown d-none d-md-block">
                    <a href="{{ route('business.purchaseorder.create.form') }}" title="Create a new Reservation"
                        class="dropdown-toggle nav-link">
                        <img src="{{ asset('layout_style/img/icons/plus.png') }}" style="width: 40px" alt></a>
                </li> --}}

                <li class="nav-item dropdown has-arrow user-profile-list">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-bs-toggle="dropdown">
                        <div class="user-names">
                            <h5>{{ ucfirst(Auth::user()->first_name) . ' ' . ucfirst(Auth::user()->last_name) }} </h5>
                            {{-- <span>Admin</span> --}}
                        </div>
                        <span class="user-img">
                            <img src="{{ config('aws_url.url') . Auth::user()->UserProfile->profile }}" style="border-radius:50%; width: 40px; height: 40px; object-fit: cover;" alt="">
                        </span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('business.profile') }}">My Profile</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    </div>
                </li>

            </ul>
            <div class="dropdown mobile-user-menu float-end">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                        class="fa-solid fa-ellipsis-vertical"></i></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="">My Profile</a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                </div>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>

        @php
            $segment = Request::segment(1);
            $segment2 = Request::segment(2);
        @endphp

        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">{{ session()->get('_business_name') }}</li>

                        <li>
                            <a href="{{ route('business.dashboard') }}"
                                class="{{ request()->route()->getName() == 'admin.business' ? 'active' : '' }}">
                                <span class="menu-side">
                                    <img src="{{ asset('layout_style/img/icons/dashboard_admin.png') }}"
                                        style="width: 24px" alt>
                                </span>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        @if (Auth::user()->hasPermissionTo('Read_Supplier'))
                            @php
                                $vendor_route_name = [
                                    'business.suppliers',
                                    'business.suppliers.create.form',
                                    'business.suppliers.update.form',
                                ];
                            @endphp

                            <li>
                                <a href="{{ route('business.suppliers') }}"
                                    class="{{ in_array(request()->route()->getName(), $vendor_route_name) ? 'active' : '' }}">
                                    <span class="menu-side">
                                        <img src="{{ asset('layout_style/img/icons/user.png') }}" style="width: 24px"
                                            alt>
                                    </span>
                                    <span>Vendors</span>
                                </a>
                            </li>
                        @endif



                        <li class="submenu">
                            @php

                                $products_route_name = [
                                    'business.products',
                                    'business.products.create.form',
                                    'business.products.update.form',
                                ];

                                $pur_order_route_name = [
                                    'business.purchaseorder',
                                    'business.purchaseorder.create.form',
                                    'business.purchaseorder.update.form',
                                    'business.purchases.view_details',
                                    'business.purchaseorder.detail.view',
                                    'business.purchaseorder.order.receive',
                                    'business.purchaseorder.approval_histories',
                                    'business.purchaseorder.payments',
                                ];

                                $pur_route_name = [
                                    'business.purchases',
                                    'business.purchases.create.form',
                                    'business.purchases.update.form',
                                    'business.purchase.view_details',
                                ];

                                $stock_ad_route_name = [
                                    'business.stock_adjusted',
                                    'business.stock_adjusted.create.form',
                                    'business.stock_adjusted.update.form',
                                ];

                                $stock_route_name = [
                                    'business.stock_transfer',
                                    'business.stock_transfer.create.form',
                                    'business.stock_transfer.update.form',
                                ];

                                $lowstock_route_name = ['business.low_stock'];

                                $pur_return_route_name = [
                                    'business.purchase_return',
                                    'business.purchase_return.create.form',
                                    'business.purchase_return.update.form',
                                ];

                                $writeoff_route_name = [
                                    'business.writeoff',
                                    'business.writeoff.create.form',
                                    'business.writeoff.update.form',
                                ];

                            @endphp

                            <a href="javascript:;"><span class="menu-side">
                                    <img src="{{ asset('layout_style/img/icons/inventory.png') }}"
                                        style="width: 24px" alt></span>
                                <span> Inventories </span> <span class="menu-arrow"></span></a>

                            <ul style="display: none;">
                                @if (Auth::user()->hasPermissionTo('Read_Product'))
                                    <li>
                                        <a href="{{ route('business.products') }}"
                                            class="{{ in_array(request()->route()->getName(), $products_route_name) ? 'active' : '' }}">
                                            <span>Products</span>
                                        </a>
                                    </li>
                                @endif

                                @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
                                    <li>
                                        <a href="{{ route('business.purchaseorder') }}"
                                            class="{{ in_array(request()->route()->getName(), $pur_order_route_name) ? 'active' : '' }}">
                                            <span>Purchase Orders</span>
                                        </a>
                                    </li>
                                @endif

                                {{-- @if (Auth::user()->hasPermissionTo('Read_Purchase'))
                                    <li>
                                        <a href="{{ route('business.purchases') }}"
                                            class="{{ in_array(request()->route()->getName(), $pur_route_name) ? 'active' : '' }}">
                                            <span>Purchases</span>
                                        </a>
                                    </li>
                                @endif --}}

                                @if (Auth::user()->hasPermissionTo('Read_StockAdjustment'))
                                    <li>
                                        <a href="{{ route('business.stock_adjusted') }}"
                                            class="{{ in_array(request()->route()->getName(), $stock_ad_route_name) ? 'active' : '' }}">
                                            <span>Stock Adjusted</span>
                                        </a>
                                    </li>
                                @endif

                                @if (Auth::user()->hasPermissionTo('Read_StockTransfer'))
                                    <li>
                                        <a href="{{ route('business.stock_transfer') }}"
                                            class="{{ in_array(request()->route()->getName(), $stock_route_name) ? 'active' : '' }}">
                                            <span>Stock Transfers</span>
                                        </a>
                                    </li>
                                @endif
                                {{--
                                @if (Auth::user()->hasPermissionTo('Read_LowStock'))
                                    <li>
                                        <a href="{{ route('business.low_stock') }}"
                                            class="{{ in_array(request()->route()->getName(), $lowstock_route_name) ? 'active' : '' }}">
                                            <span>Low Stock</span>
                                        </a>
                                    </li>
                                @endif --}}

                                @if (Auth::user()->hasPermissionTo('Read_PurchaseReturn'))
                                    <li>
                                        <a href="{{ route('business.purchase_return') }}"
                                            class="{{ in_array(request()->route()->getName(), $pur_return_route_name) ? 'active' : '' }}">
                                            <span>Purchase Returns</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Auth::user()->hasPermissionTo('Read_WriteOff'))
                                    <li>
                                        <a href="{{ route('business.writeoff') }}"
                                            class="{{ in_array(request()->route()->getName(), $writeoff_route_name) ? 'active' : '' }}">
                                            <span>Write Offs</span>
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </li>

                        @if (Auth::user()->hasPermissionTo('Purchase_Report') ||
                                Auth::user()->hasPermissionTo('StockTransfer_Report') ||
                                Auth::user()->hasPermissionTo('LowStock_Report') ||
                                Auth::user()->hasPermissionTo('Payment_Report'))

                            <li class="submenu">
                                @php

                                    $purchase_rep_route_name = [
                                        'business.purchase_report',
                                        'business.purchase_report.export',
                                    ];

                                    $stockTransfer_rep_route_name = [
                                        'business.stockTransfer_rep',
                                        'business.stockTransfer_rep.export',
                                    ];
                                    $lowStock_rep_route_name = [
                                        'business.lowStock_rep.list',
                                        'business.lowStock_rep.export',
                                    ];

                                    $payment_rep_route_name = [
                                        'business.payment_rep.list',
                                        'business.payment_rep.export',
                                    ];

                                    $writeoff_rep_route_name = [
                                        'business.writeoff_rep.list',
                                        'business.writeoff_rep.export'
                                    ]

                                @endphp

                                <a href="javascript:;"><span class="menu-side">
                                        <img src="{{ asset('layout_style/img/icons/report.png') }}"
                                            style="width: 24px" alt></span>
                                    <span> Reports </span> <span class="menu-arrow"></span></a>

                                <ul style="display: none;">

                                    {{-- Purchase --}}
                                    @if (Auth::user()->hasPermissionTo('Purchase_Report'))
                                        <li>
                                            <a href="{{ route('business.purchase_report') }}"
                                                class="{{ in_array(request()->route()->getName(), $purchase_rep_route_name) ? 'active' : '' }}">
                                                <span>Purchases</span>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- stockTransfer_rep --}}
                                    @if (Auth::user()->hasPermissionTo('StockTransfer_Report'))
                                        <li>
                                            <a href="{{ route('business.stockTransfer_rep') }}"
                                                class="{{ in_array(request()->route()->getName(), $stockTransfer_rep_route_name) ? 'active' : '' }}">
                                                <span>Stock Transfers</span>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- lowStock --}}
                                    @if (Auth::user()->hasPermissionTo('LowStock_Report'))
                                        <li>
                                            <a href="{{ route('business.lowStock_rep.list') }}"
                                                class="{{ in_array(request()->route()->getName(), $lowStock_rep_route_name) ? 'active' : '' }}">
                                                <span>Low stocks</span>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Payment --}}
                                    @if (Auth::user()->hasPermissionTo('Payment_Report'))
                                        <li>
                                            <a href="{{ route('business.payment_rep.list') }}"
                                                class="{{ in_array(request()->route()->getName(), $payment_rep_route_name) ? 'active' : '' }}">
                                                <span>Payments</span>
                                            </a>
                                        </li>
                                    @endif

                                     {{-- write Off --}}
                                     @if (Auth::user()->hasPermissionTo('Writeoff_Report'))
                                     <li>
                                         <a href="{{ route('business.writeoff_rep.list') }}"
                                             class="{{ in_array(request()->route()->getName(), $writeoff_rep_route_name) ? 'active' : '' }}">
                                             <span>Write Offs</span>
                                         </a>
                                     </li>
                                 @endif

                                </ul>
                            </li>
                        @endif

                        <li class="submenu">
                            @php

                                $category_route_name = [
                                    'business.category',
                                    'business.category.create.form',
                                    'business.category.update.form',
                                ];

                                $warehouse_route_name = [
                                    'business.warehouse',
                                    'business.warehouse.create.form',
                                    'business.warehouse.update.form',
                                    'business.warehouse.view.form',
                                ];

                                $sub_category_route_name = [
                                    'business.sub_category',
                                    'business.sub_category.create.form',
                                    'business.sub_category.update.form',
                                ];
                                $unit_route_name = [
                                    'business.units',
                                    'business.units.create.form',
                                    'business.units.update.form',
                                ];

                                $users_route_name = [
                                    'business.users',
                                    'business.users.create.form',
                                    'business.users.update.form',
                                ];

                            @endphp

                            <a href="javascript:;"><span class="menu-side">
                                    <img src="{{ asset('layout_style/img/icons/settings.png') }}" style="width: 24px"
                                        alt></span>
                                <span> Settings </span> <span class="menu-arrow"></span></a>

                            <ul style="display: none;">

                                {{-- Category --}}
                                @if (Auth::user()->hasPermissionTo('Read_Category'))
                                    <li>
                                        <a href="{{ route('business.category') }}"
                                            class="{{ in_array(request()->route()->getName(), $category_route_name) ? 'active' : '' }}">
                                            <span>Categories</span>
                                        </a>
                                    </li>
                                @endif

                                {{-- Sub Category --}}
                                @if (Auth::user()->hasPermissionTo('Read_Sub_Category'))
                                    <li>
                                        <a href="{{ route('business.sub_category') }}"
                                            class="{{ in_array(request()->route()->getName(), $sub_category_route_name) ? 'active' : '' }}">
                                            <span>Sub Categories</span>
                                        </a>
                                    </li>
                                @endif

                                {{-- Units --}}
                                @if (Auth::user()->hasPermissionTo('Read_Unit'))
                                    <li>
                                        <a href="{{ route('business.units') }}"
                                            class="{{ in_array(request()->route()->getName(), $unit_route_name) ? 'active' : '' }}">
                                            <span>Units</span>
                                        </a>
                                    </li>
                                @endif

                                {{-- Warehouse --}}
                                @if (Auth::user()->hasPermissionTo('Read_Warehouse'))
                                    <li>
                                        <a href="{{ route('business.warehouse') }}"
                                            class="{{ in_array(request()->route()->getName(), $warehouse_route_name) ? 'active' : '' }}">
                                            <span>Warehouses</span>
                                        </a>
                                    </li>
                                @endif

                                @if (Auth::user()->hasPermissionTo('Read_Users'))
                                    <li>
                                        <a href="{{ route('business.users') }}"
                                            class="{{ in_array(request()->route()->getName(), $users_route_name) ? 'active' : '' }}">
                                            <span>Users</span>
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('cost_calculator') }}" target="_blank"
                                class="">
                                <span class="menu-side">
                                    <img src="{{ asset('layout_style/img/icons/calculator.png') }}" style="width: 24px"
                                        alt>
                                </span>
                                <span>Cost Calculator</span>
                            </a>
                        </li>


                        @php
                            $profile_route_name = [
                                'business.profile',
                                'business.profile_update',
                                'business.password_update',
                            ];
                        @endphp

                        <li>
                            <a href="{{ route('business.profile') }}"
                                class="{{ in_array(request()->route()->getName(), $profile_route_name) ? 'active' : '' }}">
                                <span class="menu-side">
                                    <img src="{{ asset('layout_style/img/icons/profile.png') }}" style="width: 24px"
                                        alt>
                                </span>
                                <span>Profile</span>
                            </a>
                        </li>



                    </ul>

                    <div class="logout-btn">
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span
                                class="menu-side"><img src="{{ asset('layout_style/img/icons/logout.ico') }}"
                                    alt></span>
                            <span>Logout</span></a>
                    </div>

                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="content">

                @yield('content')

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
    </div>
    <div class="sidebar-overlay" data-reff></div>



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


    <script>
        //Open dropdown when clicking on element
        $(document).on("click", "a[data-dropdown='notificationMenu']", function(e) {
            e.preventDefault();

            var el = $(e.currentTarget);

            $("body").prepend(
                '<div id="dropdownOverlay" style="background: transparent; height:100%;width:100%;position:fixed;"></div>'
            );

            var container = $(e.currentTarget).parent();
            var dropdown = container.find(".dropdown");
            var containerWidth = container.width();
            var containerHeight = container.height();

            var anchorOffset = $(e.currentTarget).offset();

            dropdown.css({
                right: containerWidth / 2 + "px"
            });

            container.toggleClass("expanded");
        });

        //Close dropdowns on document click

        $(document).on("click", "#dropdownOverlay", function(e) {
            var el = $(e.currentTarget)[0].activeElement;

            if (typeof $(el).attr("data-dropdown") === "undefined") {
                $("#dropdownOverlay").remove();
                $(".dropdown-container.expanded").removeClass("expanded");
            }
        });

        //Dropdown collapsile tabs
        $(".notification-tab").click(function(e) {
            if ($(e.currentTarget).parent().hasClass("expanded")) {
                $(".notification-group").removeClass("expanded");
            } else {
                $(".notification-group").removeClass("expanded");
                $(e.currentTarget).parent().toggleClass("expanded");
            }
        });

        $(document).ready(function() {
            $('.select2').select2()

            $('#change_dashboard').change(function(e) {
                e.preventDefault();
                var id = $(this).val()

                if (id != '') {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    var data = {
                        'id': id
                    }
                    $('#loader').show()
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.business.move_dashboard') }}",
                        data: data,
                        dataType: "JSON",
                        success: function(response) {
                            $('#loader').hide()
                            location.href = "{{ route('business.dashboard') }}";

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
                            alert('something went to wrong')
                        }
                    });
                }
            });
        });
    </script>



    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>


    @yield('scripts')
</body>

</html>
