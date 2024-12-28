@extends('layouts.business')

@section('title')
   Manage Stock Transfers
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;">  Manage Stock Transfers </a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">

                    <div class="page-table-header mb-2">
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <div class="doctor-table-blk">
                                    <h3 class="text-uppercase">Stock Transfers</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="staff-search-table">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ml-2 mr-2">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms ">
                                        <label>Products</label>
                                        <select class="form-control select2 product" name="product" id="product">
                                            <option value="">All Products</option>
                                            @foreach ($products as $item)
                                                <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms ">
                                        <label>From Warehouse</label>
                                        <select class="form-control select2 from_warehouse" name="from_warehouse" id="from_warehouse">
                                            <option value="">All From Warehouses</option>
                                            @foreach ($warehouses as $item)
                                                <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms">
                                        <label>To Warehouse</label>
                                        <select class="form-control select2  to_warehouse" name="to_warehouse" id="to_warehouse">
                                            <option value="">All To Warehouses</option>
                                            @foreach ($warehouses as $item)
                                                <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms">
                                        <label for="">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control start_date" max="{{date('Y-m-d')}}">
                                    </div>
                                </div>


                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms">
                                        <label for="">To Date</label>
                                        <input type="date" name="to_date" id="to_date" class="form-control to_date" max="{{date('Y-m-d')}}">
                                    </div>
                                </div>


                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3"></div> <!-- Empty column for spacing -->
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3"></div> 
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 text-end">
                                    <button type="button" class="btn btn-primary btn-lg" style="width: 100px;" id="reset_button">RESET</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-stripped " id="data_table">
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

                    <div class="col-lx-12 col-lg-12 col-md-12 col-sm-12 col-12 text-end p-3 pr-0">
                        <button type="button" id="btn_export" class="btn btn-lg btn-primary"><i
                                class="fas fa-file-download"></i>
                            Download</button>
                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')
    <script>
        var table;

        $(document).ready(function() {
            loadData()
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function loadData() {
            table = $('#data_table').DataTable({
                "stripeClasses": [],
                "lengthMenu": [10, 20, 50],
                "pageLength": 10,
                processing: true,
                serverSide: true,
                orderable: false,
                ajax: {
                    url: "{{ route('business.stockTransfer_rep', ['json' => 1]) }}",
                    data: function(d) {
                        d.product = $('#product').val(),
                        d.start_date = $('#start_date').val(),
                        d.to_date = $('#to_date').val(),
                        d.from_warehouse = $('#from_warehouse').val(),
                        d.to_warehouse = $('#to_warehouse').val()
                    }
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
                        data: 'product',
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
                    }
                ]
        });

        $('#product').change(function(e) {
            e.preventDefault();

            table.clear();
            table.ajax.reload();
            table.draw();
            $('.rest_button').css('display', 'block')
        });

        $('#start_date').change(function(e) {
            e.preventDefault();

            table.clear();
            table.ajax.reload();
            table.draw();
            $('.rest_button').css('display', 'block');
            $('.to_date').attr('min', $(this).val());
            $('.to_date').val($(this).val());
        });

        $('#to_date').change(function(e) {
            e.preventDefault();

            table.clear();
            table.ajax.reload();
            table.draw();
            $('.rest_button').css('display', 'block')
        });

        $('#from_warehouse').change(function(e) {
            e.preventDefault();

            table.clear();
            table.ajax.reload();
            table.draw();
            $('.rest_button').css('display', 'block')
        });

        $('#to_warehouse').change(function(e) {
            e.preventDefault();

            table.clear();
            table.ajax.reload();
            table.draw();
            $('.rest_button').css('display', 'block')
        });

        $('#reset_button').click(function (e) {
            e.preventDefault();

            $('#to_date').val('').change()
            $('#start_date').val('').change()
            $('#product').val('').change()
            $('#from_warehouse').val('').change()
            $('#to_warehouse').val('').change()

        });


        $('#btn_export').click(function(e) {
            e.preventDefault();
            var product = $('#product').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            var data = {
                'product': product,
                'end_date': end_date,
                'start_date': start_date,
                'from_warehouse' : $('#from_warehouse').val(),
                'to_warehouse' : $('#to_warehouse').val()
            }
            $('#loader').show()
            $.ajax({
                xhrFields: {
                    responseType: 'blob',
                },
                type: "POST",
                url: "{{ route('business.stockTransfer_rep.export') }}",
                data: data,
                success: function(data) {
                    $('#loader').hide()
                    var name = Date.now();
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(data);
                    link.download = `stockTransfer_` + name + `.xlsx`;
                    link.click();
                },
                error:function(data)
                {
                    $('#loader').hide()
                    alert('Something went to wrong')
                }
            });
        });
    }


    </script>
@endsection
