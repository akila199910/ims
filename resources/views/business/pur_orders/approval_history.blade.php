@extends('layouts.business')

@section('title')
   Manage Approval History
@endsection

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-8">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('business.purchaseorder') }}">Manage Approval History</a></li>
                <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                <li class="breadcrumb-item active"> Approval History</li>
            </ul>
        </div>
        <div class="col-sm-4 text-end">
            <a href="{{ route('business.purchaseorder') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h3 class="text-uppercase"> Approval History</h3>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-stripped " id="data_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purchase No.</th>
                                    <th>Modify By</th>
                                    <th>Created at</th>
                                    <th>Status</th>
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

        function loadData() {
            table = $('#data_table').DataTable({
                "stripeClasses": [],
                "lengthMenu": [10, 20, 50],
                "pageLength": 10,
                processing: true,
                serverSide: true,
                orderable: false,
                ajax: {
                    url: "{{ route('business.purchaseorder.approval_history_list', ['json' => 1]) }}",
                    data: function(d) {
                        d.order_id = "{{ $purchase['id'] }}"
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'invoice_id',
                        name: 'pur_order_Info.invoice_id',
                        orderable: false,
                    },
                    {
                        data: 'modify_by',
                        name: 'modify_user_info.name',
                        orderable: false,
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
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
@endsection
