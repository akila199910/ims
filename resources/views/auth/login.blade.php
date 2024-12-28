@extends('layouts.auth')

@section('title')
    Login
@endsection

@section('content')
    <div class="col-lg-6 login-wrap">
        <div class="login-sec">
            <div class="log-img">
                <img class="img-fluid" src="{{ asset('layout_style/img/login.jpg') }}" alt="Logo">
            </div>
        </div>
    </div>

    <div class="col-lg-6 login-wrap-bg">
        <div class="login-wrapper">
            <div class="loginbox">
                <div class="login-right">
                    <div class="login-right-wrap">
                        <div class="account-logo">
                            <a href="javascript:;"><img src="{{ asset('layout_style/img/wage_icon.png') }}" alt style="height: 80px"></a>
                            {{-- <h1 class="text-uppercase text-primary-emphasis" style="font-weight: 900">{{env('APP_NAME')}}</h1> --}}
                        </div>
                        <h2>Login</h2>

                        <form method="POST" class="text-left" id="submitForm" enctype="multipart/form-data">
                            @csrf
                            <div class="input-block">
                                <label>Email <span class="login-danger">*</span></label>
                                <input class="form-control email lock-icon-field" name="email" id="email"
                                    placeholder="e.g example@gmail.com" type="text">
                                <span class="user-icon fa fa-user"></span>
                                {{-- <span class="email-icon fa fa-envelope"></span> --}}
                            </div>
                            <div class="input-block">
                                <small class="text-danger err_email"></small>
                            </div>
                            <div class="input-block">
                                <label>Password <span class="login-danger">*</span></label>
                                <input class="form-control pass-input password lock-icon-field" type="password" id="password" name="password"
                                    placeholder="********">
                                <span class="lock-icon feather-lock"></span>
                                {{-- <span class="profile-views feather-eye-off toggle-password"></span> --}}
                            </div>
                            <div class="input-block">
                                <small class="text-danger err_password"></small>
                            </div>
                             <!-- Show Password Toggle -->
                             <div class="remember-me show-password">
                                <label class="custom_check mr-2 mb-0 d-inline-flex remember-me">
                                    Show Password
                                    <input type="checkbox" name="radio"  class= "toggle-password" onclick="togglePassword()">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="forgotpass">
                                <div class="remember-me">
                                    <!--
                                        <label class="custom_check mr-2 mb-0 d-inline-flex remember-me">
                                            Remember me
                                            <input type="checkbox" name="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                    -->
                                </div>
                                <a href="{{ route('buiness.forget_password.index') }}">Forgot Password?</a>
                            </div>
                            <div class="input-block login-btn">
                                <button class="btn btn-primary btn-block" type="submit">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
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
                url: "{{ route('login') }}",
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
                        location.href = response.route;
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
            $('.err_email').text('');
            $('.email').removeClass('border-danger');

            $('.err_password').text('');
            $('.password').removeClass('border-danger');
        }



    </script>
@endsection
