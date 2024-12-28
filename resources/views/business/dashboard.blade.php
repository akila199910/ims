@extends('layouts.business')

@section('title')
    Dashboard
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;">Dashboard </a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Today's Reservations --}}
    <div class="doctor-list-blk col-12">
        <div class="row pb-3">
            <div class="col-xl-6 col-md-6">
                <div class="doctor-table-blk">
                    @php
                        $todayDate = now()->format('Y-F-d');
                    @endphp
                    <h3 class="text-uppercase">Purchase - {{ $todayDate }}</h3>
                </div>
            </div>
        </div>

        <div class="row pb-3">
            @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
                <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_purchase_List('current','')">
                @else
                    <div class="col-xl-4 col-md-6">
            @endif

            <div class="doctor-widget">
                <div class="doctor-box-icon flex-shrink-0">
                    <img src="layout_style/img/icons/purchase.png" style="width: 30px" alt="">
                </div>
                <div class="doctor-content dash-count flex-grow-1">
                    <h4>{{ $todayTotalPurchases }}</h4>
                    <h5>Total Purchase</h5>
                </div>
            </div>
        </div>

        @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
            <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_purchase_List('current',0)">
            @else
                <div class="col-xl-4 col-md-6">
        @endif

        <div class="doctor-widget">
            <div class="doctor-box-icon flex-shrink-0">
                <i class="fa-solid fa-spinner fa-xl" style="color: #ffffff;"></i>
            </div>
            <div class="doctor-content dash-count flex-grow-1">
                <h4>{{ $todayPendingCount }}</h4>
                <h5>Pending </h5>
            </div>


        </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_purchase_List('current',1)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif

    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <i class="fa-solid fa-check fa-xl" style="color: #ffffff;"></i>
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $todayApprovedCount }}
            </h4>
            <h5>Approved</h5>
        </div>
    </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_purchase_List('current',2)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif

    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <img src="layout_style/img/icons/onhold.png" style="width: 30px" alt="">
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4> {{ $todayonholdCount }}
            </h4>
            <h5>On Hold</h5>
        </div>

    </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_purchase_List('current',3)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif

    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <i class="fa-solid fa-xmark fa-xl" style="color: #ffffff;"></i>
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $todaycancelCount }}</h4>
            <h5>Cancelled</h5>
        </div>
    </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_purchase_List('current',4)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif

    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <i class="fa-solid fa-list fa-xl" style="color: #ffffff;"></i>
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $todayfullfilledCount }}
            </h4>
            <h5>Full Filled</h5>
        </div>
    </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_purchase_List('current',5)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif
    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <img src="layout_style/img/icons/received.png" style="width: 30px" alt="">
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $todayreceivedCount }}
            </h4>
            <h5>Received</h5>
        </div>
    </div>
    </div>


    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_purchase_List('current',6)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif
    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <i class="fa-solid fa-ban fa-xl" style="color: #ffffff;"></i>
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $todayclosedCount }}
            </h4>
            <h5>Closed</h5>
        </div>
    </div>
    </div>


    @if (Auth::user()->hasPermissionTo('Read_StockTransfer'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_stockTransfer_List('current')">
        @else
            <div class="col-xl-4 col-md-6">
    @endif
    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <img src="layout_style/img/icons/transfer.png" style="width: 30px" alt="">
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $todayStockTransferCount }}</h4>
            <h5>Stock Transfer</h5>
        </div>
    </div>
    </div>
    </div>

    <div class="row _current_purchase_div">

    </div>
    <div class="row _current_stock_transfer_div">

    </div>
    </div>

    <div class="doctor-list-blk col-12">
        <div class="row pb-3">
            <div class="col-xl-6 col-md-6">
                <div class="doctor-table-blk">
                    <h3 class="text-uppercase">All Purchase</h3>
                </div>
            </div>
        </div>
        <div class="row pb-3">
            @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
                <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_all_purchase_List('all','')">
                @else
                    <div class="col-xl-4 col-md-6">
            @endif
            <div class="doctor-widget">
                <div class="doctor-box-icon flex-shrink-0">
                    <img src="layout_style/img/icons/purchase.png" style="width: 30px" alt="">
                </div>
                <div class="doctor-content dash-count flex-grow-1">
                    <h4>{{ $totalPurchases }}</h4>
                    <h5>Total Purchase</h5>
                </div>
            </div>
        </div>

        @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
            <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_all_purchase_List('all',0)">
            @else
                <div class="col-xl-4 col-md-6">
        @endif
        <div class="doctor-widget">
            <div class="doctor-box-icon flex-shrink-0">
                <i class="fa-solid fa-spinner fa-xl" style="color: #ffffff;"></i>
            </div>
            <div class="doctor-content dash-count flex-grow-1">
                <h4>{{ $pendingCount }}</h4>
                <h5>Total Pending</h5>
            </div>
        </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_all_purchase_List('all',1)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif
    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <i class="fa-solid fa-check fa-xl" style="color: #ffffff;"></i>
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4> {{ $approvedCount }}</h4>
            <h5>Total Approved </h5>
        </div>

    </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_all_purchase_List('all',2)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif
    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <img src="layout_style/img/icons/onhold.png" style="width: 30px" alt="">
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $onHoldCount }}</h4>
            <h5>Total On Hold</h5>
        </div>

    </div>
    </div>
    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_all_purchase_List('all',3)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif
    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <i class="fa-solid fa-xmark fa-xl" style="color: #ffffff;"></i>
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $canceledCount }}</h4>
            <h5>Total Cancelled </h5>
        </div>
    </div>
    </div>
    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_all_purchase_List('all',4)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif
    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <i class="fa-solid fa-list fa-xl" style="color: #ffffff;"></i>
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $fullfilledCount }}
            </h4>
            <h5>Total Full Filled</h5>
        </div>
    </div>
    </div>

    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_all_purchase_List('all',5)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif
    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <img src="layout_style/img/icons/received.png" style="width: 30px" alt="">
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $receivedCount }}
            </h4>
            <h5>Total Received</h5>
        </div>
    </div>
    </div>


    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_all_purchase_List('all',6)">
        @else
            <div class="col-xl-4 col-md-6">
    @endif
    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <i class="fa-solid fa-ban fa-xl" style="color: #ffffff;"></i>
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $closedCount }}
            </h4>
            <h5>Total closed</h5>
        </div>
    </div>
    </div>


    @if (Auth::user()->hasPermissionTo('Read_StockTransfer'))
        <div class="col-xl-4 col-md-4 col-lg-4 col-sm-4" style="cursor: pointer" onclick="get_all_stockTransfer_List('all')">
        @else
            <div class="col-xl-4 col-md-6">
    @endif
    <div class="doctor-widget">
        <div class="doctor-box-icon flex-shrink-0">
            <img src="layout_style/img/icons/transfer.png" style="width: 30px" alt="">
        </div>
        <div class="doctor-content dash-count flex-grow-1">
            <h4>{{ $totalstock_transfer }}
            </h4>
            <h5>Total Stock Transfer</h5>
        </div>
    </div>
    </div>
    </div>



    <div class="row _all_purchase_div">

    </div>
    <div class="row _all_stocktransfer_div">

    </div>
    </div>


    {{-- Upcoming reservations table --}}
    @if (Auth::user()->hasPermissionTo('Read_PurchaseOrder'))
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table show-entire">
                    <div class="card-body">

                        <div class="page-table-header mb-2">
                            <div class="row align-items-center mb-2">
                                <div class="col">
                                    <div class="doctor-table-blk">
                                        <h3 class="text-uppercase">Recent Purchase</h3>
                                    </div>
                                </div>
                                <div class="col-auto text-end float-end ms-auto download-grp">
                                    @if (Auth::user()->hasPermissionTo('Create_PurchaseOrder'))
                                        <a href="{{ route('business.purchaseorder.create.form') }}"
                                            class="btn btn-primary ms-2">
                                            +&nbsp;New Purchase
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-stripped " id="purchase_order_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Purchase No.</th>
                                        <th>Vendor Name</th>
                                        <th>Request Date</th>
                                        <th>Order By</th>
                                        <th>Modify By</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (Auth::user()->hasPermissionTo('Read_StockTransfer'))
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table show-entire">
                    <div class="card-body">

                        <div class="page-table-header mb-2">
                            <div class="row align-items-center mb-2">
                                <div class="col">
                                    <div class="doctor-table-blk">
                                        <h3 class="text-uppercase">Recent Stock Transfer</h3>
                                    </div>
                                </div>
                                <div class="col-auto text-end float-end ms-auto download-grp">
                                    @if (Auth::user()->hasPermissionTo('Create_StockTransfer'))
                                        <a href="{{ route('business.stock_transfer.create.form') }}"
                                            class="btn btn-primary ms-2">
                                            +&nbsp;New Stock Transfer
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-stripped " id="stock_transfer_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>From Warehouse</th>
                                        <th>To Warehouse</th>
                                        <th>Transferred Date</th>
                                        <th>Transferred QTY</th>
                                        <th>Created By</th>
                                        <th>Edited By</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- chart of this months' Purchases --}}
    <div class="card">
        <div class="card-body">
            <div class="doctor-table-blk">
                @php
                    $currentMonthYear = now()->format('F Y');
                @endphp
                <h3 class="text-uppercase">Purchases - {{ $currentMonthYear }}</h3>
            </div>
            <div class="mt-3 mb-0 position-relative">
                <div>
                    <canvas id="myChart" width="600" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        var purchaseOrderTable;
        var stockTransferTable;

        $(document).ready(function() {
            loadPurchaseOrderData();
            loadStockTransferData();

            $('#filter').click(function() {
                if (purchaseOrderTable) {
                    purchaseOrderTable.clear();
                    purchaseOrderTable.ajax.reload();
                    purchaseOrderTable.draw();
                }
                if (stockTransferTable) {
                    stockTransferTable.clear();
                    stockTransferTable.ajax.reload();
                    stockTransferTable.draw();
                }
            });
        });

        function loadPurchaseOrderData() {
            purchaseOrderTable = $('#purchase_order_table').DataTable({
                "stripeClasses": [],
                "lengthMenu": [10, 20, 50],
                "pageLength": 10,
                processing: true,
                serverSide: true,
                orderable: false,
                ajax: {
                    url: "{{ route('business.purchaseorder', ['json' => 1]) }}"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ref_no',
                        name: 'ref_no',
                        orderable: false,
                    },
                    {
                        data: 'supplier',
                        name: 'supplier_Info.name',
                        orderable: false,
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                    },
                    {
                        data: 'order_by',
                        name: 'order_user_info.name',
                        orderable: false,
                    },
                    {
                        data: 'modify_by',
                        name: 'modify_user_info.name',
                        orderable: false,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        }


        function loadStockTransferData() {
            stockTransferTable = $('#stock_transfer_table').DataTable({
                "stripeClasses": [],
                "lengthMenu": [10, 20, 50],
                "pageLength": 10,
                processing: true,
                serverSide: true,
                orderable: false,
                ajax: {
                    url: "{{ route('business.stock_transfer', ['json' => 1]) }}"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                    },
                    {
                        data: 'product_name',
                        name: 'product_info.name',
                        orderable: false,
                    },
                    {
                        data: 'warehouse_from',
                        name: 'warehouse_from',
                        orderable: false,
                    },
                    {
                        data: 'warehouse_to',
                        name: 'warehouse_to',
                        orderable: false,
                    },
                    {
                        data: 'transfer_date',
                        name: 'transfer_date',
                        orderable: false,
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_by',
                        name: 'created_by',
                        orderable: false,
                    },
                    {
                        data: 'edit_by',
                        name: 'edit_by',
                        orderable: false,
                    },
                ]
            });
        }

        function get_purchase_List(day, status) {
            var data = {
                'day': day,
                'status': status,
                "_token": $('input[name=_token]').val(),
            }

            $('#loader').show()

            $.ajax({
                type: "POST",
                url: "{{ route('dashboard.get_purchase') }}",
                data: data,
                success: function(response) {
                    $('#loader').hide()
                    $('._current_stocktransfer_div').hide();
                    $('._current_purchase_div').html('')
                    $('._current_purchase_div').html(response)
                    $('._current_purchase_div').show();
                    // console.log(response)
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

        function get_all_purchase_List(day, status) {
            var data = {
                'day': day,
                'status': status,
                "_token": $('input[name=_token]').val(),
            }

            $('#loader').show()

            $.ajax({
                type: "POST",
                url: "{{ route('dashboard.get_purchase') }}",
                data: data,
                success: function(response) {
                    $('#loader').hide()
                    // console.log( "hide _all_stocktransfer_div")
                    $('._all_stocktransfer_div').hide();
                    $('._all_purchase_div').html('')
                    $('._all_purchase_div').html(response)
                    $('._all_purchase_div').show();
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

        function get_stockTransfer_List(type) {
            var data = {
                'type': type,
                "_token": $('input[name=_token]').val(),
            }

            $('#loader').show()

            $.ajax({
                type: "POST",
                url: "{{ route('dashboard.get_stockTransfer') }}",
                data: data,
                success: function(response) {
                    $('#loader').hide()
                    // console.log( "hide _current_purchase_div")
                    $('._current_purchase_div').hide();

                    $('._current_stock_transfer_div').html('')
                    $('._current_stock_transfer_div').html(response)
                    $('._current_stock_transfer_div').show();

                    console.log(response)

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

        function get_all_stockTransfer_List(day) {
            var data = {
                'day': day,
                "_token": $('input[name=_token]').val(),
            }

            $('#loader').show()

            $.ajax({
                type: "POST",
                url: "{{ route('dashboard.get_stockTransfer') }}",
                data: data,
                success: function(response) {
                    $('#loader').hide()
                    // console.log( "hide _all_purchase_div")
                    $('._all_purchase_div').hide();

                    $('._all_stocktransfer_div').html('')
                    $('._all_stocktransfer_div').html(response)
                    $('._all_stocktransfer_div').show();

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

        function deleteConfirmation(id) {
            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to Delete the Selected Purchase?',
                type: 'red',
                autoClose: 'cancel|10000',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-green',
                        action: function() {
                            $("#loader").show();
                            var data = {
                                "_token": $('input[name=_token]').val(),
                                "id": id,
                            }
                            $.ajax({
                                type: "POST",
                                url: "{{ route('business.purchaseorder.delete') }}",
                                data: data,
                                success: function(response) {
                                    $("#loader").hide();
                                    table.clear();
                                    table.ajax.reload();
                                    table.draw();
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


    {{-- grapg of this months' reservations --}}
    <script>
        $(document).ready(function() {
            $.ajax({
                type: "GET",
                url: "{{ route('dashboard.graph') }}",
                dataType: "JSON",
                success: function(response) {
                    const ctx = document.getElementById('myChart').getContext('2d');

                    // Define labels for each day of the month (1-31)
                    const labels = Array.from({
                        length: 31
                    }, (_, i) => i + 1);

                    // Extract data for each status
                    const pendingData = response.pending;
                    const approvedData = response.approved;
                    const onholdData = response.onhold;
                    const canceledData = response.canceled;
                    const fullfilledData = response.fullfilled;
                    const receivedData = response.received;
                    const closedData = response.closed;


                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'PENDING',
                                    data: pendingData,
                                    backgroundColor: '#FF9B01',
                                    borderColor: '#FF9B01',
                                    borderWidth: 1
                                },
                                {
                                    label: 'APPROVED',
                                    data: approvedData,
                                    backgroundColor: '#03C03C',
                                    borderColor: '#03C03C',
                                    borderWidth: 1
                                },
                                {
                                    label: 'ONHOLD',
                                    data: onholdData,
                                    backgroundColor: '#0000FF',
                                    borderColor: '#0000FF',
                                    borderWidth: 1
                                },
                                {
                                    label: 'CANCELLED',
                                    data: canceledData,
                                    backgroundColor: '#FF0000',
                                    borderColor: '#FF0000',
                                    borderWidth: 1
                                },
                                {
                                    label: 'FULLFILLED',
                                    data: fullfilledData,
                                    backgroundColor: '#FF01A2',
                                    borderColor: '#FF01A2',
                                    borderWidth: 1
                                },
                                {
                                    label: 'RECEIVED',
                                    data: receivedData,
                                    backgroundColor: '#8F13FD',
                                    borderColor: '#8F13FD',
                                    borderWidth: 1
                                },
                                {
                                    label: 'CLOSED',
                                    data: closedData,
                                    backgroundColor: '#800080',
                                    borderColor: '#800080',
                                    borderWidth: 1
                                },
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    barThickness: 20,
                                    maxBarThickness: 30,
                                    grid: {
                                        offset: true
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1, //show only whole numbers
                                        callback: function(value) {
                                            return Number.isInteger(value) ? value : null;
                                        }
                                    },
                                    min: 0
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        font: {
                                            size: 10
                                        },
                                        color: '#333'
                                    }
                                },
                                tooltip: {
                                    enabled: true
                                }
                            },
                            layout: {
                                padding: {
                                    left: 10,
                                    right: 10,
                                    top: 5,
                                    bottom: 5
                                }
                            }
                        }
                    });
                },
                statusCode: {
                    401: function() {
                        window.location.href = '{{ route('login') }}';
                    },
                    419: function() {
                        window.location.href = '{{ route('login') }}';
                    }
                }
            });
        });
    </script>
@endsection
