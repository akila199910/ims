@extends('layouts.business')

@section('title')
Manage Categories
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.category') }}">Manage Categories</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Update Category</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.category') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h4>Update Category</h4>
                                </div>
                            </div>

                            <input type="hidden" name="id" value="{{$categories->id}}">

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="name">Name <span class="text-danger">*</span> </label>
                                    <input type="text" name="name" value="{{$categories->name}}" class="form-control name"
                                        id="name" maxlength="190">
                                    <small class="text-danger font-weight-bold err_name"></small>
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
                                        <input type="checkbox" id="status" name="status" {{$categories->status == 1 ? 'checked' : ''}} class="check">
                                        <label for="status" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </div>
                        <div
                            class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 text-center form-group mb-md-4 pb-3">

                          <img src="{{ $categories->image ? config('aws_url.url') . $categories->image : asset('layout_style/img/category.jpg') }}"
                          style="width:100px; height:100px; border-radius:50%;object-fit: cover; " class="stylist-image" alt="">

                        </div>

                        </div>
                        @if (Auth::user()->hasPermissionTo('Update_Category'))
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
                url: "{{ route('business.category.update') }}",
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

        function errorClear()
        {
            $('#name').removeClass('is-invalid')
            $('.err_name').text('')


        }
    </script>
@endsection
