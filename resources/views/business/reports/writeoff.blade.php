@extends('layouts.business')

@section('title')
  Manage Write Off Reports
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.writeoff_rep.list') }}">Manage Write Off Reports</a>
                    </li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Write Off Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.writeoff_rep.list') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="page-table-header mb-2">
                        <div class="row align-items-center mb-2">
                            <div class="col">
                                <div class="doctor-table-blk">
                                    <h3 class="text-uppercase">Write Off details Report </h3>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="staff-search-table">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ml-2 mr-2">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms ">
                                        <label>Product</label>
                                        <select class="form-control select2 product_id" name="product_id" id="product_id">
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
                                        <select class="form-control select2 warehouse_id" name="warehouse_id"
                                            id="warehouse_id">
                                            <option value="">All Warehouses</option>
                                            @foreach ($warehouses as $item)
                                                <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3"></div> <!-- Empty column for spacing -->
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 text-end">
                                    <button type="button" class="btn btn-primary btn-lg" style="width: 100px;" id="reset_button">RESET</button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-12 col-xl-12 col-lg-12 mt-4">
                        <div class="table-responsive">
                            <table class="table table-stripped " id="data_table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Retail Price</th>
                                        <th>Warehouse</th>
                                        <th>Qty</th>

                                    </tr>
                                </thead>
                            </table>
                        </div>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            loadData()
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
                    url: "{{ route('business.writeoff_rep.list', ['json' => 1]) }}",
                    data: function(d) {
                        d.product_id = $('#product_id').val(),
                            d.warehouse_id = $('#warehouse_id').val()
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product',
                        name: 'product',
                        orderable: false,
                    },
                    {
                        data: 'retail_price',
                        name: 'Product_info.retail_price',
                        orderable: false,
                    },
                    {
                        data: 'warehouse',
                        name: 'warehouse',
                        orderable: false,
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        orderable: false,
                    }
                ]
            });

            $('#product_id').change(function(e) {
                e.preventDefault();

                table.clear();
                table.ajax.reload();
                table.draw();
                $('.rest_button').css('display', 'block')
            });

            $('#warehouse_id').change(function(e) {
                e.preventDefault();

                table.clear();
                table.ajax.reload();
                table.draw();
                $('.rest_button').css('display', 'block')
            });

            $('#reset_button').click(function(e) {
                e.preventDefault();

                $('#product_id').val('').change()
                $('#warehouse_id').val('').change()

            });


            $('#btn_export').click(function(e) {
                e.preventDefault();
                var product_id = $('#product_id').val();
                var warehouse_id = $('#warehouse_id').val();

                var data = {
                    'product_id': product_id,
                    'warehouse_id': warehouse_id
                }
                $('#loader').show()
                $.ajax({
                    xhrFields: {
                        responseType: 'blob',
                    },
                    type: "POST",
                    url: "{{ route('business.writeoff_rep.export') }}",
                    data: data,
                    success: function(data) {
                        $('#loader').hide()
                        var name = Date.now();
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(data);
                        link.download = `writeoff_` + name + `.xlsx`;
                        link.click();
                    },
                    error: function(data) {
                        $('#loader').hide()
                        alert('Something went to wrong')
                    }
                });
            });
        }
    </script>
@endsection
