<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('layout_style/img/wage_icon.png')}}">
    <title>
        @yield('title') | {{env('APP_NAME')}}
    </title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" type="text/css" href="{{asset('layout_style/css/bootstrap.min.css')}}">

    <link rel="stylesheet" href="{{asset('layout_style/css/feather.css')}}">

    <link rel="stylesheet" href="{{asset('layout_style/plugins/fontawesome/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('layout_style/plugins/fontawesome/css/all.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('layout_style/css/style.css?v=').time()}}">

    <link rel="stylesheet" href="{{ asset('layout_style/jquery_confirm/style.css') }}">
    <link rel="stylesheet" href="{{ asset('layout_style/css/my-style.css?v=').time() }}">
</head>
<style>
    .invalid-feedback {
    display:block;
}
</style>
<body>

<body>

    <div class="main-wrapper login-body">
        <div class="container-fluid px-0">
            <div class="row">

                @yield('content')

            </div>
        </div>
    </div>

    <!--loader-->
    <div class="ajax-loader" id="loader" style="display: none">
        <div class="max-loader">
            <div class="loader-inner">
                <div class="spinner-border text-white" role="status"></div>
                <p>Please Wait........</p>
            </div>
        </div>
    </div>
    <!--end loader-->

    <script src="https://code.jquery.com/jquery-3.7.1.js" ></script>

    {{-- <script src="{{asset('layout_style/js/jquery-3.7.1.min.js')}}" ></script> --}}

    <script src="{{asset('layout_style/js/bootstrap.bundle.min.js')}}" ></script>

    <script src="{{asset('layout_style/js/feather.min.js')}}" ></script>

    <script src="{{asset('layout_style/js/app.js')}}" ></script>

    <script src="{{asset('layout_style/cdn_scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js')}}" data-cf-settings="4bc371b55987d973bf81a02d-|49" defer></script></body>

    <script src="{{ asset('layout_style/js/validations.js') }}"></script>
    <script src="{{ asset('layout_style/jquery_confirm/script.js') }}"></script>
    <script src="{{ asset('layout_style/jquery_confirm/popup.js') }}"></script>
    <script src="{{asset('layout_style/plugins/raty/jquery.raty.js')}}" ></script>
    <script src="{{asset('layout_style/plugins/raty/custom.raty.js')}}"></script>

    @yield('scripts')

</body>

</html>
