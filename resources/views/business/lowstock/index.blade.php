@extends('layouts.business')

@section('title')
  Manage Low Stocks
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;">  Manage Low Stocks</a></li>
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
                                    <h3 class="text-uppercase"> Low Stocks</h3>
                                </div>
                            </div>
                            <div class="col-lx-12 col-lg-12 col-md-12 col-sm-12 col-12 text-end p-3 pr-0">
                                <button type="button" id="btn_export" class="btn btn-lg btn-primary"><i
                                        class="fas fa-file-download"></i>
                                    Download</button>
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
                    url: "{{ route('business.low_stock', ['json' => 1]) }}"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'product_info.image',
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
        }

        $('#btn_export').click(function(e) {
                e.preventDefault();
                var key_word = $('#key_word').val();
                var product = $('#product').val();
                var warehouse = $('#warehouse').val();

                var data = {
                    'key_word': key_word,
                    'product': product,
                    'warehouse': warehouse
                }
                $('#loader').show()
                $.ajax({
                    xhrFields: {
                        responseType: 'blob',
                    },
                    type: "POST",
                    url: "{{ route('business.low_stock.lowStock_export') }}",
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
    </script>
@endsection
