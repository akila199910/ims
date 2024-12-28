@extends('layouts.home')

@section('title')
    Business Users
@endsection

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-8">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.business-users') }}">Manage Business Users </a></li>
                <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                <li class="breadcrumb-item active">Update Business Users</li>
            </ul>
        </div>
        <div class="col-sm-4 text-end">
            <a href="{{ route('admin.business-users') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                <h4>Update Business Users</h4>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="{{$find_business_user->id}}">

                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="input-block local-forms">
                                <label>First Name <span class="login-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control first_name " id="first_name"
                                    maxlength="190" value="{{ $find_business_user->first_name }}">
                                <small class="text-danger font-weight-bold err_first_name"></small>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="input-block local-forms">
                                <label>Last Name <span class="login-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control last_name" id="last_name"
                                    maxlength="190" value="{{ $find_business_user->last_name }}">
                                <small class="text-danger font-weight-bold err_last_name"></small>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="input-block local-forms">
                                <label>Email <span class="login-danger">*</span></label>
                                <input type="text" name="email" class="form-control email" id="email"
                                    maxlength="190" value="{{ $find_business_user->email }}">
                                <small class="text-danger font-weight-bold err_email"></small>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="input-block local-forms">
                                <label>Contact <span class="login-danger">*</span></label>
                                <input type="text" name="contact" class="form-control contact number_only_val"
                                    id="contact" maxlength="10" value="{{ $find_business_user->contact }}">
                                <small class="text-danger font-weight-bold err_contact"></small>
                            </div>
                        </div>

                        {{-- <div class="col-12 col-md-6 col-xl-6">
                            <div class="input-block local-forms">
                                <label>Business<span class="login-danger">*</span></label>
                                <select class="form-control" name="companies[]" id="companies" title="companies" multiple>
                                    <option value="">Select Business</option>
                                    @foreach ($all_business as $item)
                                        <option value="{{ $item->id }}"
                                            @if (in_array($item->id, $business_user_companies))
                                            selected
                                        @endif>
                                        {{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger font-weight-bold err_companies"></small>
                            </div>
                        </div> --}}
                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="input-block local-forms">
                                <label>Select Business <span class="login-danger">*</span></label>
                                <select name="companies[]" class="form-control companies select2" id="companies" multiple>
                                    <option value="">Select Business</option>
                                    @foreach ($all_business as $item)
                                    <option value="{{ $item->id }}"
                                        @if (in_array($item->id, $business_user_companies))
                                        selected
                                    @endif>
                                    {{ $item->name }}</option>
                                @endforeach
                                </select>
                                <small class="text-danger font-weight-bold err_companies"></small>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="input-block select-gender">
                                <label class="gen-label">Status Inactive/Active</label>
                                <div class="status-toggle d-flex justify-content-between align-items-center">
                                    <input type="checkbox" id="status" name="status" {{$find_business_user->status == 1 ? 'checked' : ''}} class="check">
                                    <label for="status" class="checktoggle">checkbox</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="doctor-submit text-end">
                                    <button type="submit"
                                        class="btn btn-primary text-uppercase submit-form me-2">Update</button>
                                </div>
                            </div>
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
        })

        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('admin.business-users.update') }}",
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
            $('.err_first_name').text('')
            $('.err_last_name').text('')
            $('.err_email').text('')
            $('.err_contact').text('')
            $('.err_companies').text('')
        }
    </script>
@endsection
