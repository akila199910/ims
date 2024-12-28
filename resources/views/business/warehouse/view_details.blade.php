@extends('layouts.business')

@section('title')
    Manage Warehouses
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.warehouse') }}">Warehouses</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Warehouse Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.warehouse') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="doctor-personals-grp">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="doctor-table-blk mb-4 pt-2">
                                            <h3 class="text-uppercase">Warehouse Details</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Name</h2>
                                                    <h3>{{ Str::limit(ucwords($ware_houses->name),30) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Address</h2>
                                                    <h3>{{ Str::limit(ucwords($ware_houses->address),30) }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Contact</h2>
                                                    <h3>{{ ucwords($ware_houses->contact) }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Status</h2>
                                                    <h3>
                                                        @php
                                                            $status =
                                                                $ware_houses->status == 1
                                                                    ? 'Active'
                                                                    : 'Inactive';
                                                            $badgeClass =
                                                                $ware_houses->status == 1
                                                                    ? 'custom-badge status-green'
                                                                    : 'custom-badge status-red';
                                                        @endphp
                                                        <span class="{{ $badgeClass }}">{{ $status }}</span>
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                    <h3 class="text-uppercase">Warehouses - <span
                                            class="text-primary-emphasis">{{ $ware_houses->name }}</span>'s Product List
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-xl-12 col-md-12">

                    </div>

                    <div class="table-responsive">
                        <table class="table table-stripped " id="data_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Warehouse Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="editProductModalLabel">Update stock levels</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        @csrf
                        <input type="hidden" id="product_id" name="product_id">
                        <div class="mb-3">
                            <label for="product" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product" name="product" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="qty" class="form-label">Out Stock Qty </label>
                            <input type="number" class="form-control" id="qty" name="qty">
                            <small class="text-danger font-weight-bold err_qty"></small>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
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
                    url: "{{ route('business.warehouse.get_products', ['json' => 1]) }}",
                    data: function(d) {
                        d.warehouse_id = '{{ $ware_houses->id }}'
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
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'product_info.name',
                        orderable: false,
                    },
                    {
                        data: 'category',
                        name: 'category',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sub_category',
                        name: 'sub_category',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'unit',
                        name: 'unit',
                        orderable: false,
                        searchable: false
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
                    },
                ]
            });
        }

        function deleteConfirmation(id) {
            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to Delete the Selected Product from warehouse?',
                type: 'red',
                autoClose: 'cancel|10000',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-green',
                        action: function() {
                            $("#loader").show();
                            var data = {
                                "_token": $('input[name=_token]').val(),
                                "id": id,
                            }
                            $.ajax({
                                type: "POST",
                                url: "{{ route('business.warehouse.products.delete') }}",
                                data: data,
                                success: function(response) {
                                    $("#loader").hide();
                                    table.clear();
                                    table.ajax.reload();
                                    table.draw();
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
                    },

                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-red',
                        action: function() {

                        }
                    },
                }
            });
        }

        $(document).on('click', '.edit-warehouse', function() {
            var productId = $(this).data('id');
            var data = {
                'id': productId
            };

            $('#loader').show();

            $.ajax({
                type: "GET",
                url: "{{ route('business.warehouse.get_details_product') }}",
                data: data,
                dataType: "JSON",
                success: function(response) {
                    $('#loader').hide();
                    errorClear()
                    if (response.status) {
                        $('#product_id').val(productId);
                        $('#product').val(response.data.product);
                        $('#qty').val(response.data.qty);

                        $('#editProductModal').modal({
                            backdrop: 'static',
                            keyboard: false
                        }).modal('show');
                    }
                },
                statusCode: {
                    401: function() {
                        window.location.href = '{{ route('login') }}';
                    },
                    419: function() {
                        window.location.href = '{{ route('login') }}';
                    },
                },
                error: function(data) {
                    someThingWrong();
                }
            });
        });

        $('#editProductForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $('#loader').show();
            $.ajax({
                url: '{{ route('business.warehouse.update_product') }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#loader').hide();
                    table.clear();
                    table.ajax.reload();
                    table.draw();
                    errorClear()
                    if (response.status == false) {
                        $.each(response.message, function(key, item) {
                            if (key) {
                                $('.err_' + key).text(item)
                            }
                        });
                    } else {

                        $('#editProductModal').modal('hide'); // Hide the modal
                        $('#data_table').DataTable().ajax
                    }
                },
                error: function(xhr) {
                    console.log(xhr);

                    someThingWrong();
                }
            });
        });

        function errorClear() {
            $('#qty').removeClass('is-invalid')
            $('.err_qty').text('')

        }
    </script>
@endsection
