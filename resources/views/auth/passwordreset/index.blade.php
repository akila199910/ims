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
                        <div class="mt-0">
                            <h2>Forgot Password</h2>
                            <h5 class="mb-2">Can't log in?</h5>
                            <h6 class="text-sm mb-2">Restore access to your account</h6>
                        </div>

                        <div class="d-flex">
                                <div class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                    <i class="ni ni-circle-08 text-white text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                        </div>

                        <form class="text-left" method="POST" id="submitForm">
                            @csrf
                            <form role="form">
                                <h5 class="mt-4 text-center">We will send a recovery link to</h5>

                                <div class="input-block mt-3">
                                    <label>Email <span class="login-danger">*</span></label>
                                    <input class="form-control email lock-icon-field" name="email" id="email"
                                        placeholder="e.g example@gmail.com" type="text">
                                    <span class="user-icon fa fa-user"></span>
                                    <span class="email-icon fa fa-envelope"></span>
                                </div>
                                <div class="input-block">
                                    <small class="text-danger err_email"></small>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary w-100 mt-4 mb-0 text-uppercase" id="submit_button"
                                        value="">Reset password</button>

                                    <button type="button" class="btn btn-primary w-100 mt-4 mb-0 text-uppercase" id="disable_button"
                                        style="display: none" value="">Sending ...</button>
                                </div>
                            </form>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function () {
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
                       $('#loader').show()
                    },
                    url: "{{ url('/forget_password') }}",
                    data: formData,
                    dataType: "JSON",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        $('#loader').hide()

                        $('.err_email').text('');
                        if (response.status == false) {
                            $.each(response.errors, function(key, item) {
                                if (key) {
                                    $('.err_' + key).text(item);
                                } else {
                                    $('.err_' + key).text('');
                                }
                            });
                        }else {
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
                                            location.href = "{{ route('login') }}";
                                        }
                                    },
                                }
                            });
                        }
                    },
                    statusCode: {
                        401: function() {
                            window.location.href = '{{route('login')}}'; //or what ever is your login URI
                        },
                        419: function() {
                            window.location.href = '{{route('login')}}'; //or what ever is your login URI
                        },
                    },
                    error : function (data) {
                        $('#loader').hide()
                        alert('Something went to wrong')
                      }
                });

            });
        });
    </script>

    @if (session('success'))
        <script>
            $(document).ready(function() {
                var locId = $('.locationId').val();
                $.confirm({
                    theme: 'modern',
                    columnClass: 'col-md-6 col-8 col-md-offset-4',
                    title: 'Success! ',
                    content: '{{ session('success') }}',
                    type: 'theme',
                    buttons: {
                        confirm: {
                            text: 'OK',
                            btnClass: 'btn-150',
                            action: function() {
                                location.href = "{{ url('login') }}";
                            }
                        },
                    }
                });
            });
        </script>
    @endif
@endsection
