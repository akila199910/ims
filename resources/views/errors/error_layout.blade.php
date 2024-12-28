<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('layout_style/img/wage_icon.png') }}">
    <title>
        @yield('title') | {{ env('APP_NAME') }}
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="{{asset('layout_style/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('layout_style/plugins/fontawesome/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('layout_style/plugins/fontawesome/css/all.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('layout_style/css/style.css')}}">
</head>

<body class="error-pages">
    <div class="main-wrapper error-wrapper">
        <div class="error-box">
            @yield('content')
        </div>
    </div>
    <script src="{{asset('layout_style/js/jquery-3.7.1.min.js')}}" ></script>
    <script src="{{asset('layout_style/js/bootstrap.bundle.min.js')}}" ></script>
</body>
</html>
