@extends('layouts.home')

@section('title')
    Business
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.business') }}">Business</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Update Business</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('admin.business') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h4>Update Business</h4>
                                </div>
                            </div>

                            <input type="hidden" name="id" value="{{$business->id}}">

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="name">Business Name<span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{$business->name}}" class="form-control name"
                                        id="name" maxlength="190">
                                    <small class="text-danger font-weight-bold err_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="email">Business Email<span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{$business->email}}" class="form-control email"
                                        id="email" maxlength="190">
                                    <small class="text-danger font-weight-bold err_email"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="contact">Business Contact<span class="text-danger">*</span></label>
                                    <input type="text" name="contact" value="{{$business->contact}}" maxlength="10" class="form-control contact number_only_val"
                                        id="contact">
                                    <small class="text-danger font-weight-bold err_contact"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="address">Business Address<span class="text-danger">*</span></label>
                                    <input type="text" name="address" class="form-control address" value="{{$business->address}}" id="address" maxlength="190">
                                    <small class="text-danger font-weight-bold err_address"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block select-gender">
                                    <label class="gen-label" for="status">Status<span class="text-danger">*</span> Inactive/Active</label>
                                    <div class="status-toggle d-flex justify-content-between align-items-center">
                                        <input type="checkbox" id="status" name="status" {{$business->status == 1 ? 'checked' : ''}} class="check">
                                        <label for="status" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-12">
                            <div class="doctor-submit text-end">
                                <button type="submit"
                                    class="btn btn-primary text-uppercase submit-form me-2">Update</button>
                            </div>
                        </div>
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
            $('#ibson_business').change(function() {
                if (!$(this).is(':checked')) {
                    $('#ibson_id').val(''); // Clear the Ibson's Id input field
                }
            });
        })

        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('admin.business.update') }}",
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
            $('.err_name').text('')
            $('.err_email').text('')
            $('.err_contact').text('')
            $('.err_address').text('')
            $('.err_snap_auth_key').text('')
            $('.err_ibson_id').text('')
        }
    </script>

@endsection
