@extends('layouts.business')

@section('title')
   Manage Low Stock Reports
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;">  Manage Low Stock Reports</a></li>
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
                                    <h3 class="text-uppercase">Low Stocks Details</h3>
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
                                        <label>Warehouses</label>
                                        <select class="form-control select2 warehouse" name="warehouse" id="warehouse">
                                            <option value="">All Warehouses</option>
                                            @foreach ($warehouses as $item)
                                                <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms">
                                        <label class="form-label text-overlapped">Available Quantity</label>
                                        <input type="number" class="form-control" name="select_qty" id="select_qty" min="0" placeholder="Enter quantity">
                                    </div>
                                </div>

                                
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
                                    <th>Product</th>
                                    <th>Warehouse</th>
                                    <th>Available Qty</th>
                                    <th>Qty</th>
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
                    url: "{{ route('business.lowStock_rep.list', ['json' => 1]) }}",
                    data: function(d) {
                        d.product = $('#product').val(),
                        d.warehouse = $('#warehouse').val()
                        d.select_qty = $('#select_qty').val()
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
                        data: 'warehouse',
                        name: 'warehouse_info.name',
                        orderable: false,
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        orderable: false,
                    },
                    {
                        data: 'qty_alert',
                        name: 'qty_alert',
                        orderable: false,
                    },
                ]
        });

        $('#product').change(function(e) {
            e.preventDefault();

            table.clear();
            table.ajax.reload();
            table.draw();
            $('.rest_button').css('display', 'block')
        });

        $('#warehouse').change(function(e) {
            e.preventDefault();

            table.clear();
            table.ajax.reload();
            table.draw();
            $('.rest_button').css('display', 'block')
        });

        $('#select_qty').on('input', function() {

            table.clear();
            table.ajax.reload();
            table.draw();
            $('.rest_button').css('display', 'block')
        });


        $('#reset_button').click(function (e) {
            e.preventDefault();

            $('#product').val('').change()
            $('#warehouse').val('').change()
            $('#select_qty').val('').change()
        });


        $('#btn_export').click(function(e) {
                e.preventDefault();
                var product = $('#product').val();
                var warehouse = $('#warehouse').val();
                var select_qty = $('select_qty').val();

                var data = {
                    'product': product,
                    'warehouse': warehouse,
                    'select_qty' : select_qty
                }
                $('#loader').show()
                $.ajax({
                    xhrFields: {
                        responseType: 'blob',
                    },
                    type: "POST",
                    url: "{{ route('business.lowStock_rep.export') }}",
                    data: data,
                    success: function(data) {
                        $('#loader').hide()
                        var name = Date.now();
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(data);
                        link.download = `lowstock_` + name + `.xlsx`;
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
