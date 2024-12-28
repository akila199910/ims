@extends('layouts.business')

@section('title')
  Manage Purchase Reports
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"> Manage Purchase Reports </a></li>
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
                                    <h3 class="text-uppercase"> Purchase Reports</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="staff-search-table">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ml-2 mr-2">
                            <div class="row">

                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms ">
                                        <label>Vendors</label>
                                        <select class="form-control select2 supplier" name="supplier" id="supplier">
                                            <option value="">All Vendors</option>
                                            @foreach ($suppliers as $item)
                                                <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms">
                                        <label for="">Start Date<span class="login-danger">*</span></label>
                                        <input type="date" name="start_date" id="start_date" class="form-control start_date" max="{{date('Y-m-d')}}">
                                    </div>
                                </div>


                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms">
                                        <label for="">End Date<span class="login-danger">*</span></label>
                                        <input type="date" name="end_date" id="end_date" class="form-control end_date" max="{{date('Y-m-d')}}">
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
                                    <th>Vendor Name</th>
                                    <th>Request Date</th>
                                    <th>Order By</th>
                                    <th>Modify By</th>
                                    <th>Status</th>
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
                    url: "{{ route('business.purchase_report', ['json' => 1]) }}",
                    data: function(d) {
                        d.supplier_id = $('#supplier').val(),
                        d.start_date = $('#start_date').val(),
                        d.end_date = $('#end_date').val()
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'supplier',
                        name: 'supplier_Info.name',
                        orderable: false,
                    },
                    {
                        data: 'purchased_date',
                        name: 'purchased_date',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'order_by',
                        name: 'order_by',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'modify_by',
                        name: 'modify_by',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    }

                ]
        });

        $('#supplier').change(function(e) {
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
            $('.end_date').attr('min', $(this).val());
            $('.end_date').val($(this).val());
        });

        $('#end_date').change(function(e) {
            e.preventDefault();

            table.clear();
            table.ajax.reload();
            table.draw();
            $('.rest_button').css('display', 'block')
        });

        $('#reset_button').click(function (e) {
            e.preventDefault();

            $('#end_date').val('').change()
            $('#start_date').val('').change()
            $('#supplier').val('').change()

        });


        $('#btn_export').click(function(e) {
            e.preventDefault();
            var supplier = $('#supplier').val();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            var data = {
                'supplier_id': supplier,
                'end_date': end_date,
                'start_date': start_date
            }
            $('#loader').show()
            $.ajax({
                xhrFields: {
                    responseType: 'blob',
                },
                type: "POST",
                url: "{{ route('business.purchase_report.export') }}",
                data: data,
                success: function(data) {
                    $('#loader').hide()
                    var name = Date.now();
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(data);
                    link.download = `purchase_` + name + `.xlsx`;
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
