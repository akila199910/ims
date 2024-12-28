@extends('layouts.business')

@section('title')
  Manage Purchase Payment Reports
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.payment_rep.list') }}">Manage Purchase Payment Reports</a>
                    </li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Payment Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.payment_rep.list') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h3 class="text-uppercase">Payment details Report </h3>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="staff-search-table">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 ml-2 mr-2">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms ">
                                        <label>Pur.Invoice ID</label>
                                        <select class="form-control select2 purchased_id" name="purchased_id" id="purchased_id">
                                            <option value="">All Pur.Invoice ID</option>
                                            @foreach ($pur_orders as $item)
                                                <option value="{{ $item->id }}">{{ $item->invoice_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms ">
                                        <label>Payment Type</label>
                                        <select class="form-control select2 payment_type" name="payment_type" id="payment_type">
                                            <option value="">All Payment Type</option>
                                            @foreach ($payment_type as $item)
                                                <option value="{{ $item->id }}">{{ $item->payment_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms">
                                        <label for="">Start Date</label>
                                        <input type="date" name="start_date" id="start_date"
                                            class="form-control start_date" max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>


                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3">
                                    <div class="input-block local-forms">
                                        <label for="">End Date </label>
                                        <input type="date" name="end_date" id="end_date" class="form-control end_date"
                                            max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3"></div> <!-- Empty column for spacing -->
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3"></div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3"></div>
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
                                        <th>Pur.Invoice ID</th>
                                        <th>Payment Ref No.</th>
                                        <th>Payment Type</th>
                                        <th>Paid Date</th>
                                        <th>Paid Amount</th>
                                        <th>Scan Doc</th>
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
                    url: "{{ route('business.payment_rep.list', ['json' => 1]) }}",
                    data: function(d) {
                        d.purchased_id = $('#purchased_id').val(),
                            d.start_date = $('#start_date').val(),
                            d.end_date = $('#end_date').val(),
                            d.payment_type = $('#payment_type').val()
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
                        name: 'invoice_id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'payment_reference',
                        name: 'payment_reference',
                        orderable: false,
                    },
                    {
                        data: 'payment_type',
                        name: 'payment_type_info.payment_type',
                        orderable: false
                    },
                    {
                        data: 'payment_date',
                        name: 'payment_date',
                        orderable: false
                    },
                    {
                        data: 'paid_amount',
                        name: 'paid_amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'scan_doc',
                        name: 'scan_doc',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#purchased_id').change(function(e) {
                e.preventDefault();

                table.clear();
                table.ajax.reload();
                table.draw();
                $('.rest_button').css('display', 'block')
            });

            $('#payment_type').change(function(e) {
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

            $('#reset_button').click(function(e) {
                e.preventDefault();

                $('#end_date').val('').change()
                $('#start_date').val('').change()
                $('#purchased_id').val('').change()
                $('#payment_type').val('').change()

            });


            $('#btn_export').click(function(e) {
                e.preventDefault();
                var purchased_id = $('#purchased_id').val();
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var payment_type = $('#payment_type').val();

                var data = {
                    'purchased_id': purchased_id,
                    'end_date': end_date,
                    'start_date': start_date,
                    'payment_type' : payment_type
                }
                $('#loader').show()
                $.ajax({
                    xhrFields: {
                        responseType: 'blob',
                    },
                    type: "POST",
                    url: "{{ route('business.payment_rep.export') }}",
                    data: data,
                    success: function(data) {
                        $('#loader').hide()
                        var name = Date.now();
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(data);
                        link.download = `purchase_` + name + `.xlsx`;
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
