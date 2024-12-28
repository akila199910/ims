@extends('layouts.auth')

@section('title')
    Set New Password
@endsection

@section('content')
    <div class="container">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12 col-12"></div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mt-5">

                    <div class="login-wrapper">
                        <div class="loginbox">
                            <div class="login-right">
                                <div class="login-right-wrap">
                                    <div class="account-logo">
                                        {{-- <a href="javascript:;"><img src="{{ asset('layout_style/img/wage_icon.png') }}" alt style="height: 80px"></a> --}}
                                        {{-- <h1 class="text-uppercase text-primary-emphasis" style="font-weight: 900">{{env('APP_NAME')}}</h1> --}}
                                    </div>
                                    <h2>Set New Password</h2>

                                    <form method="POST" class="text-left" id="submitForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="input-block">
                                            <label>Email <span class="login-danger">*</span></label>
                                            <input class="form-control email lock-icon-field" readonly name="email" id="email"
                                                placeholder="e.g example@gmail.com" value="{{$user->email}}" type="text">
                                            <span class="user-icon fa fa-user"></span>
                                            <span class="email-icon fa fa-envelope"></span>
                                        </div>
                                        <div class="input-block">
                                            <small class="text-danger err_email"></small>
                                        </div>

                                        <div class="input-block">
                                            <label>Password <span class="login-danger">*</span></label>
                                            <input class="form-control pass-input password lock-icon-field" type="password" id="password" name="password"
                                                placeholder="********">
                                            <span class="lock-icon feather-lock"></span>
                                            <span class="profile-views feather-eye-off toggle-new-password"></span>
                                        </div>
                                        <div class="input-block">
                                            <small class="text-danger err_password"></small>
                                        </div>

                                        <div class="input-block">
                                            <label>Confirm Password <span class="login-danger">*</span></label>
                                            <input class="form-control pass-input password lock-icon-field" type="password" id="password_confirmation" name="password_confirmation"
                                                placeholder="********">
                                            <span class="lock-icon feather-lock"></span>
                                            <span class="profile-views feather-eye-off toggle-confirm-password"></span>
                                        </div>
                                        <div class="input-block">
                                            <small class="text-danger err_password_confirmation"></small>
                                        </div>

                                        <div class="input-block login-btn">
                                            <button class="btn btn-primary btn-block" type="submit">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-3 col-sm-12 col-12"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $('#loader').show()
                },
                url: "{{ route('set_password.update') }}",
                data: formData,
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $('#loader').hide()
                    console.log(response);
                    clearError();

                    if (response.status == false) {
                        $.each(response.errors, function(key, item) {
                            if (key) {
                                $('.err_' + key).text(item);
                                $('.' + key).addClass('border-danger');
                            }
                        });
                    } else {
                        successPopup(response.message, response.route)
                    }
                },
                error: function(data) {
                    console.log(data);
                    $('#loader').hide()
                    alert('Something went to wrong')
                }
            });
        });

        function clearError() {
            $('.err_password').text('');
            $('.password').removeClass('border-danger');

            $('.err_password_confirmation').text('');
            $('.password_confirmation').removeClass('border-danger');
        }
    </script>
        <script>
            $(document).ready(function() {
                // Toggle New Password
                $(document).on('click', '.toggle-new-password', function() {
                    $(this).toggleClass("feather-eye-off feather-eye");
                    var input = $("#password");
                    if (input.attr("type") === "password") {
                        input.attr("type", "text");
                    } else {
                        input.attr("type", "password");
                    }
                });

                // Toggle Confirm Password
                $(document).on('click', '.toggle-confirm-password', function() {
                    $(this).toggleClass("feather-eye-off feather-eye");
                    var input = $("#password_confirmation");
                    if (input.attr("type") === "password") {
                        input.attr("type", "text");
                    } else {
                        input.attr("type", "password");
                    }
                });
            });
        </script>
@endsection
