@extends('layouts.business')

@section('title')
Manage Products
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.products') }}">Manage Products</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Product Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.products') }}"  class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                            <h3 class="text-uppercase">Product Details</h3>
                                        </div>
                                        <div class="row  align-items">
                                            <div class="col-xl-4 col-md-4 text-center">
                                                <div class="detail-personal">
                                                    <h3>
                                                        @if ($products->image && $products->image != 0)
                                                        <img src="{{ config('awsurl.url') . $products->image }}"
                                                            alt="Product Image" style="width: 100px; height: 100px;border-radius:50%; object-fit:cover; ">
                                                    @else
                                                        <img src="{{ asset('layout_style/img/icons/product_100.png') }}"
                                                            alt="Default Image" style="width: 100px; height: 100px;border-radius:50%; object-fit:cover;">
                                                    @endif
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-8 col-md-8">
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Product Name</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ ucwords($products->name) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Retail Price</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ ucwords($products->retail_price) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Category</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ ucwords($products->category_info->name) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Sub Category</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ ucwords($products->sub_category_info->name ) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Unit</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ ucwords($products->unit_info->name ) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Short Description</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ ucwords($products->sort_description ) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Description</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ ucwords($products->description ) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Qty</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ ucwords($products->qty ) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Status</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>
                                                                @php
                                                                    $status =
                                                                        $products->status == 1
                                                                            ? 'Active'
                                                                            : 'Inactive';
                                                                    $badgeClass =
                                                                        $products->status == 1
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

                                    <div class="card-body">
                                        <hr />
                                        <div class="doctor-table-blk mb-4 pt-2">
                                            <h3 class="text-uppercase">List of Warehouses Inventory</h3>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-stripped " id="data_table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Warehouse Name</th>
                                                        <th>Qty</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                            </table>
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

    <!-- Edit Warehouse Modal -->
    <div class="modal fade" id="editWarehouseModal" tabindex="-1" aria-labelledby="editWarehouseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="editWarehouseModalLabel">Update stock levels</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editWarehouseForm">
                        @csrf
                        <input type="hidden" id="warehouse_id" name="warehouse_id">
                        <div class="mb-3">
                            <label for="warehouse_name" class="form-label">Warehouse Name</label>
                            <input type="text" class="form-control" id="warehouse_name" name="warehouse_name" readonly>
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
            loadData();
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
                    url: "{{ route('business.products.get_wareHouse', ['json' => 1]) }}",
                    data: function(d) {
                        d.product_id = '{{ $products->id }}';
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'warehouse',
                        name: 'warehouse_info.name',
                        orderable: false
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

        $(document).on('click', '.edit-warehouse', function() {
            var warehouseId = $(this).data('id');
            var data = {
                'id': warehouseId
            };

            $('#loader').show();

            $.ajax({
                type: "GET",
                url: "{{ route('business.products.get_details') }}",
                data: data,
                dataType: "JSON",
                success: function(response) {
                    $('#loader').hide();
                    errorClear()
                    if (response.status) {
                        $('#warehouse_id').val(warehouseId);
                        $('#warehouse_name').val(response.data.warehouse);
                        $('#qty').val(response.data.qty);

                        $('#editWarehouseModal').modal({
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

        $('#editWarehouseForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $('#loader').show();
            $.ajax({
                url: '{{ route('business.products.update_wareHouse') }}',
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

                        $('#editWarehouseModal').modal('hide'); // Hide the modal
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
