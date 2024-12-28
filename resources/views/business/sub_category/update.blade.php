@extends('layouts.business')

@section('title')
Manage Sub Categories
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.sub_category') }}">Manage Sub Categories</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Update Sub Category</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.sub_category') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h4>Update Sub Category</h4>
                                </div>
                            </div>

                            <input type="hidden" name="id" value="{{ $sub_categories->id }}">

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="name">Name <span class="text-danger">*</span> </label>
                                    <input type="text" name="name" value="{{ $sub_categories->name }}"
                                        class="form-control name" id="name" maxlength="190">
                                    <small class="text-danger font-weight-bold err_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="category">Category Name<span class="text-danger">*</span> </label>
                                    <select name="category" class="form-control category select2" id="category">
                                        <option value="">Select Category Name</option>
                                        @foreach ($categories as $item)
                                            <option
                                                value="{{ $item->id }}"{{ $item->id == $sub_categories->category_id ? ' selected' : '' }}>
                                                {{ Str::limit($item->name,30) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_category"></small>
                                </div>
                            </div>

                            {{-- <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label>Image</label>
                                    <input type="file" name="image" class="form-control image" id="image"
                                        maxlength="190">
                                    <small class="text-danger font-weight-bold err_image"></small>
                                </div>
                            </div> --}}
                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label>Image <small class="text-primary">(Height : 500px X Width : 500px)</small></label>
                                    <input class="upload-path form-control" disabled />
                                    <div class="upload">
                                        <input type="file" name="image" accept=".jpg, .jpeg, .png" class="form-control image" id="image"
                                            maxlength="190">
                                        <span class="custom-file-label" id="file-label">Choose File...</span>
                                    </div>
                                    <small class="text-danger font-weight-bold err_image"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block select-gender">
                                    <label class="gen-label" for="status">Status Inactive/Active</label>
                                    <div class="status-toggle d-flex justify-content-between align-items-center">
                                        <input type="checkbox" id="status" name="status"
                                            {{ $sub_categories->status == 1 ? 'checked' : '' }} class="check">
                                        <label for="status" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 text-center form-group mb-md-4 pb-3">
                                <img src="{{ $sub_categories->image ? config('aws_url.url') . $sub_categories->image : asset('layout_style/img/subcategory.jpg') }}"
                                    style="width: 100px; height: 100px;border-radius:50%; object-fit:cover;" alt="">
                            </div>

                        </div>
                        @if (Auth::user()->hasPermissionTo('Update_Sub_Category'))
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

        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('business.sub_category.update') }}",
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
                                $('#' + key).addClass('is-invalid');
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
            $('#name').removeClass('is-invalid')
            $('.err_name').text('')


        }
    </script>
@endsection
