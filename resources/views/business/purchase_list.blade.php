<div class="col-lg-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-5">
    <div class="card card-table show-entire">
        <div class="card-body">


            @php

$status_name = 'All';
                if ($status == 0 && $status != null) {
                    $status_name = 'Pending';
                }

                if ($status == 1 && $status != null) {
                    $status_name = 'Approved';
                }

                if ($status == 2 && $status != null) {
                    $status_name = 'On Hold';
                }

                if ($status == 3 && $status != null) {
                    $status_name = 'Cancelled';
                }

                if ($status == 4 && $status != null) {
                    $status_name = 'Full Filled';
                }

                if ($status == 5 && $status != null) {
                    $status_name = 'Received';
                }

                if($status == 6 && $status != null){
                    $status_name = 'Closed';
                }
            @endphp

            <div class="page-table-header mb-2">
                <div class="row align-items-center mb-2">
                    <div class="col">
                        <div class="doctor-table-blk">
                            <h3 class="text-uppercase">
                                {{ $current == true ? 'Today ' . $status_name . ' Purchases' :  $status_name . ' Purchases' }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-stripped " id="purchase_data_table_today">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ref No</th>
                            <th>Vendor Name</th>
                            <th>Supplier Contact</th>
                            <th>Purchased Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    var purchase_table_today;

    $(document).ready(function() {
        loadPurchaseData();
    });

    function loadPurchaseData() {
        purchase_table_today = $('#purchase_data_table_today').DataTable({
            "stripeClasses": [],
            "lengthMenu": [10, 20, 50],
            "pageLength": 10,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard.get_purchase_list') }}",
                data: function(d) {
                    d.json = 1,
                        d.current = '{{ $current }}',
                        d.status = '{{ $status }}'
                }
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
                    orderable: false
                },
                {
                    data: 'supplier_name',
                    name: 'supplier_Info.name',
                    orderable: false,
                },
                {
                    data: 'supplier_contact',
                    name: 'supplier_Info.contact',
                    orderable: false,
                },
                {
                    data: 'purchased_date',
                    name: 'purchased_date',
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
</script>
