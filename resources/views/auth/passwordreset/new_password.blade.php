@extends('layouts.auth')

@section('title')
    Password Reset
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
                    </div>
                    <h2>New Password</h2>

                    <form id="submitForm" class="text-left" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="email" value="{{ $user->email }}">

                        {{-- New Password --}}
                        <div class="input-block">
                            <label> New Password <span class="login-danger">*</span></label>
                            <input class="form-control pass-input password lock-icon-field" id="password" name="password" autocomplete="current-password" type="password" placeholder="********">
                            <span class="lock-icon feather-lock"></span>
                            <span class="profile-views feather-eye-off toggle-new-password"></span>
                        </div>
                        <div class="input-block">
                            <small class="text-danger err_password"></small>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="input-block">
                            <label> Confirm Password <span class="login-danger">*</span></label>
                            <input id="confirm_password" name="password_confirmation" autocomplete="current-password" type="password" class="form-control password pass-input login-field lock-icon-field" placeholder="*********" value="">
                            <span class="lock-icon feather-lock"></span>
                            <span class="profile-views feather-eye-off toggle-confirm-password"></span>
                        </div>
                        <div class="input-block">
                            <small class="text-danger err_password_confirmation"></small>
                        </div>

                        {{-- Save Button --}}
                        <div class="text-center" id="submit_button">
                            <button type="submit" class="btn btn-primary w-100 mt-4 mb-0 text-uppercase">Save</button>
                        </div>

                        <div class="text-center" style="display: none" id="disable_button">
                            <button type="button" class="btn btn-primary w-100 mt-4 mb-0"><i class="fas fa-spinner fa-spin"></i></button>
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
        $(document).ready(function() {
            var password_confirmation = document.getElementById("password_confirmation");
            $('#btnConfirm').click(function(e) {
                e.preventDefault();
                if (password_confirmation.type === "password") {
                    password_confirmation.type = "text";
                    $('#btnConfirm').removeClass("fa-eye-slash");
                    $('#btnConfirm').addClass("fa-eye");
                } else {
                    $('#btnConfirm').removeClass("fa-eye");
                    $('#btnConfirm').addClass("fa-eye-slash");
                    password_confirmation.type = "password";
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#submitForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData($('#submitForm')[0]);

                $.ajax({
                    type: "POST",
                    beforeSend: function() {
                        $('#submit_button').css('display', 'none');
                        $('#disable_button').css('display', 'block');
                    },
                    url: "{{ url('/new_password') }}",
                    data: formData,
                    dataType: "JSON",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        $('#submit_button').css('display', 'block');
                        $('#disable_button').css('display', 'none');

                        $('.err_password_confirmation').text('');
                        $('.err_password').text('');

                        $('#password_confirmation').removeClass('is-invalid');
                        $('#password').removeClass('is-invalid');

                        if (response.status == false) {
                            $.each(response.errors, function(key, item) {
                                if (key) {
                                    $('.err_' + key).text(item);
                                    $('#'+key).addClass('is-invalid');
                                } else {
                                    $('.err_' + key).text('');
                                }
                            });
                        } else {
                            $.confirm({
                                theme: 'modern',
                                columnClass: 'col-md-6 col-8 col-md-offset-4',
                                title: 'Success! ',
                                content: response.message,
                                type: 'green',
                                buttons: {
                                    confirm: {
                                        text: 'OK',
                                        btnClass: 'btn-150',
                                        action: function() {
                                            location.href = "{{  route('login') }}";
                                        }
                                    },
                                }
                            });
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
                    }
                });

            });
        });
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
                var input = $("#confirm_password");
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        });
    </script>

@endsection
