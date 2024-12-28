@extends('layouts.business')

@section('title')
Manage Purchase Returns
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.purchase_return') }}"> Manage Purchase Returns</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Add/Delete Purchase Return Item</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.purchase_return') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <form id="submitForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Add/Delete Purchase Return Item </h4>
                                </div>
                            </div>
                            <input type="hidden" name="id" id="return_id" value="{{ $pur_return['id'] }}">
                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="purchase_ref">Purchase Invoice Id</label>
                                    <input type="text" name="purchase_ref" class="form-control" id="purchase_ref"
                                        readonly value="{{ $pur_return['purchase_info']['id'] }}">
                                    <small class="text-danger font-weight-bold err_purchase_ref"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="return_date">Return Date </label>
                                    <input type="text" name="return_date" class="form-control" id="return_date" readonly
                                        value="{{ $pur_return['return_date'] }}">
                                    <small class="text-danger font-weight-bold err_return_date"></small>
                                </div>
                            </div>
                            <div class="col-sm-12 _product_item_content">

                            </div>
                        </div>

                        @if (Auth::user()->hasPermissionTo('Update_PurchaseReturn'))
                            <div class="col-12">
                                <div class="doctor-submit text-end">
                                    <button type="submit"
                                        class="btn btn-primary text-uppercase submit-form me-2">Update</button>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </form>
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

            $(document).ready(function() {
                $('.my_select2').select2()
            });

            loadData()
            loadTableContent()
        })

        function loadTableContent() {
            var data = {
                'return_id': '{{ $pur_return['id'] }}'
            }

            $("#loader").show();
            $.ajax({
                type: "GET",
                url: "{{ route('business.purchase_return.get_order_items.form') }}",
                data: data,
                success: function(response) {
                    $("#loader").hide();
                    $('._product_item_content').html('')
                    $('._product_item_content').html(response)
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


        function loadData() {
            table = $('#data_table').DataTable({
                "stripeClasses": [],
                "lengthMenu": [25, 50, 100],
                "pageLength": 25,
                processing: true,
                serverSide: true,
                orderable: false,
                ajax: {
                    url: "{{ route('business.purchase_return.get_item_list', ['json' => 1]) }}",
                    data: function(d) {
                        d.return_id = '{{ $pur_return['id'] }}'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product_name',
                        name: 'Product_info.name',
                        orderable: false,
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                rowId: 'id'
            });
        }


        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('business.purchase_return.update') }}",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $("#loader").hide();
                    errorClear()
                    if (response.status == false) {
                        $.each(response.message, function(key, item) {
                            if (key) {
                                $('.err_' + key).text(item)
                            }
                        });
                    } else {
                        successPopup(response.message, response.route)
                    }
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
        });

        function errorClear() {
            $('.err_product').text('')
            $('.err_qty').text('')

        }


    </script>
@endsection
