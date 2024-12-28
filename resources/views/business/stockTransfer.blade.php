<div class="col-lg-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-5">
    <div class="card card-table show-entire">
        <div class="card-body">
            <div class="page-table-header mb-2">
                <div class="row align-items-center mb-2">
                    <div class="col">
                        <div class="doctor-table-blk">
                            <h3 class="text-uppercase">
                                Today Stock Transfer Details
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-stripped " id="stockTransfer_data_table_today">
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

<script>
    var stock_table_today;
        $(document).ready(function() {
            loadStockTransferData();
        });

    function loadStockTransferData() {
        var stock_table_today  = $('#stockTransfer_data_table_today').DataTable({
            "stripeClasses": [],
            "lengthMenu": [10, 20, 50],
            "pageLength": 10,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard.get_stockTransfer_list') }}",
                data: function(d) {
                    d.json = 1,
                        d.start_date = '{{ date('Y-m-d') }}',
                        d.to_date = '{{ date('Y-m-d') }}'
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
                }
            ]
        });
    }
</script>

