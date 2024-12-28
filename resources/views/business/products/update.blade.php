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
                    <li class="breadcrumb-item active">Add new Product</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.products') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h4>Product Information</h4>
                                </div>
                            </div>

                            <!-- Product Id -->
                            <input type="hidden" name="id" id="id" value="{{ $product['id'] }}">
                            <!-- END -->

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="product_name">Product Name <span class="text-danger">*</span> </label>
                                    <input type="text" name="product_name" class="form-control product_name"
                                        id="product_name" maxlength="190" value="{{ $product['name'] }}">
                                    <small class="text-danger font-weight-bold err_product_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="retail_price">Retail Price <span class="text-danger">*</span> </label>
                                    <input type="text" name="retail_price" class="form-control retail_price decimal_val"
                                        maxlength="9" id="retail_price" maxlength="190"
                                        value="{{ $product['retail_price'] }}">
                                    <small class="text-danger font-weight-bold err_retail_price"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="units">Select Units <span class="text-danger">*</span></label>
                                    <select name="units" id="units" class="form-control select2">
                                        <option value="">Select Units</option>
                                        @foreach ($units as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $product['unit_id'] == $item->id ? 'selected' : '' }}>{{ Str::limit($item->name,30) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_units"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="category">Select Category <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control category select2">
                                        <option value="">Select Category</option>
                                        @foreach ($category as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $product['category_id'] == $item->id ? 'selected' : '' }}>
                                                {{ Str::limit($item->name,30) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_category"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="sub_category">Select Sub Category <span class="text-danger">*</span></label>
                                    <select name="sub_category" id="sub_category" class="form-control sub_category select2">
                                        <option value="">Select Sub Category</option>
                                        @foreach ($sub_category as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $product['subcategory_id'] == $item->id ? 'selected' : '' }}>
                                                {{ Str::limit($item->name,30) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_sub_category"></small>
                                </div>
                            </div>

                            {{-- <div class="col-12 col-md-6 col-xl-6 mb-4">
                                <div class="input-block local-forms">
                                    <label for="image">Image</label>
                                    <input type="file" name="image" accept=".jpg, .jpeg, .png"
                                        class="form-control image" id="image">
                                    <small class="text-danger font-weight-bold err_image"></small>
                                </div>
                                <div class="text-center">
                                    <img src="{{$product['image']}}" style="height: 100px; width: 100px">
                                </div>
                            </div> --}}

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label>Image <small class="text-primary">(Height : 500px X Width : 500px)</small></label>
                                    <input class="upload-path form-control" disabled />
                                    <div class="upload">
                                        <input type="file" name="image" accept=".jpg, .jpeg, .png"
                                            class="form-control image" id="image" maxlength="190">
                                        <span class="custom-file-label" id="file-label">Choose File...</span>
                                    </div>
                                    <small class="text-danger font-weight-bold err_image"></small>
                                </div>
                                <div class="text-center">
                                    <img src="{{ $product['image'] }}" style="height: 100px; width: 100px; border-radius:50%; object-fit:cover;">
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block select-gender">
                                    <label class="gen-label" for="status">Status Inactive/Active</label>
                                    <div class="status-toggle d-flex justify-content-between align-items-center">
                                        <input type="checkbox" id="status" name="status"
                                            {{ $product['status'] == 1 ? 'checked' : '' }} class="check">
                                        <label for="status" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="alert_qty">Low Stock Alert Qty value <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="alert_qty" class="form-control alert_qty number_only_val"
                                        id="alert_qty" maxlength="10" value="{{ $product['qty_alert'] }}">
                                    <small class="text-danger font-weight-bold err_alert_qty"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-12 col-xl-12">
                                <div class="input-block local-forms">
                                    <label for="sort_description">Short Description <span
                                            class="text-danger">*</span></label>
                                    <textarea name="sort_description" id="sort_description" class="form-control sort_description" rows="2">{{ $product['sort_description'] }}</textarea>
                                    <small class="text-danger font-weight-bold err_sort_description"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-12 col-xl-12">
                                <div class="input-block local-forms">
                                    <label for="full_description">Full Description <span
                                            class="text-danger">*</span></label>
                                    <textarea name="full_description" id="full_description" class="form-control full_description" rows="4">{{ $product['description'] }}</textarea>
                                    <small class="text-danger font-weight-bold err_full_description"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-12 col-xl-12">
                                <div class="input-block local-forms">
                                    <label for="warehouses">Select Warehouses <span class="text-danger">*</span></label>
                                    <select name="warehouses[]" id="warehouses"
                                        class="form-control tagging select2 warehouses" multiple="multiple">
                                        <option value="">Select Warehouse</option>
                                        @foreach ($warehouse as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, $product['ware_house_ids']) ? 'selected' : '' }}>
                                                {{ Str::limit($item->name,30) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_warehouses"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-12 col-xl-12">
                                <div class="input-block local-forms">
                                    <label for="vendors">Select Vendors<span class="text-danger">*</span></label>
                                    <select name="vendors[]" id="vendors" class="form-control tagging select2 vendors"
                                        multiple="multiple">
                                        <option value="">Select Vendor</option>
                                        @foreach ($supplier as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, $product['supplier_ids']) ? 'selected' : '' }}>
                                                {{ Str::limit($item->name,30) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_vendors"></small>
                                </div>
                            </div>

                        </div>
                        @if (Auth::user()->hasPermissionTo('Update_Product'))
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
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
        })

        $('#category').change(function(e) {
            e.preventDefault();

            var data = {
                'category': $(this).val()
            }

            $("#loader").show();

            $.ajax({
                type: "GET",
                url: "{{ route('business.products.get_subcategory') }}",
                data: data,
                dataType: "JSON",
                success: function(response) {

                    $("#loader").hide();

                    const subCategoryDropdown = $('.sub_category');
                    subCategoryDropdown.empty().append('<option value="">Select Sub Category</option>');

                    response.data.forEach(item => {
                        const truncatedName = item.name.length > 20 ? item.name.substring(0,
                            20) + '...' : item.name;
                        subCategoryDropdown.append(
                            `<option value="${item.id}">${truncatedName}</option>`);
                    });
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

        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('business.products.update') }}",
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
                                // $('#' + key).addClass('is-invalid');
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
            $('#product_name').removeClass('is-invalid')
            $('.err_product_name').text('')

            $('#units').removeClass('is-invalid')
            $('.err_units').text('')

            $('#category').removeClass('is-invalid')
            $('.err_category').text('')

            $('#sub_category').removeClass('is-invalid')
            $('.err_sub_category').text('')

            $('#image').removeClass('is-invalid')
            $('.err_image').text('')

            $('#sort_description').removeClass('is-invalid')
            $('.err_sort_description').text('')

            $('#full_description').removeClass('is-invalid')
            $('.err_full_description').text('')

            $('#warehouses').removeClass('is-invalid')
            $('.err_warehouses').text('')

            $('#alert_qty').removeClass('is-invalid')
            $('.err_alert_qty').text('')

            $('#retail_price').removeClass('is-invalid')
            $('.err_retail_price').text('')

            $('#suppliers').removeClass('is-invalid')
            $('.err_suppliers').text('')

            $('.err_person_contact').text('')

            $('#vendors').removeClass('is-invalid')
            $('.err_vendors').text('')
        }
    </script>
@endsection
