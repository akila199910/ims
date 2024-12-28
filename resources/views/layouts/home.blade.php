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

    <link rel="stylesheet" type="text/css" href="{{ asset('layout_style/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('layout_style/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('layout_style/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('layout_style/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('layout_style/css/feather.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('layout_style/css/style.css') }}"> --}}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="{{ asset('layout_style/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('layout_style/css/style.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('layout_style/jquery_confirm/style.css') }}">
    <link rel="stylesheet" href="{{ asset('layout_style/css/my-style.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('layout_style/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempus-dominus/5.39.0/css/tempus-dominus.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.js"></script>
    <script src="{{ asset('layout_style/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('layout_style/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempus-dominus/5.39.0/js/tempus-dominus.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <script type="text/javascript">
        window.history.forward();

        function noBack() {
            window.history.forward();
            window.menubar.visible = false;
        }
    </script>


</head>

<body onLoad="noBack();" onpageshow="if (event.persisted) noBack();" onUnload="">
    <div class="main-wrapper">
        <div class="header bg-body-secondary  business-dashboard">
            <div class="header-left">
                <a href="javascript:;" class="logo">
                    <img src="{{ asset('layout_style/img/wage_icon.png') }}" width="35" height="35" alt>
                    <span>{{ env('APP_NAME') }}</span>
                </a>
            </div>
            <a id="toggle_btn" href="javascript:void(0);"><img src="{{ asset('layout_style/img/icons/bar.ico') }}"
                    alt></a>
            <a id="mobile_btn" class="mobile_btn float-start" href="#sidebar"><img
                    src="{{ asset('layout_style/img/icons/bar.ico') }}" alt></a>

            <ul class="nav user-menu float-end">

                <li class="nav-item dropdown has-arrow user-profile-list">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-bs-toggle="dropdown">
                        <div class="user-names">
                            <h5>{{ ucfirst(Auth::user()->first_name) . ' ' . ucfirst(Auth::user()->last_name) }} </h5>
                            {{-- <span>Admin</span> --}}
                        </div>
                        <span class="user-img">
                            <img src="{{ config('aws_url.url') . Auth::user()->UserProfile->profile }}"style="border-radius:50%; width: 40px; height: 40px; object-fit: cover;"alt="">
                        </span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{route('admin.profile.index')}}">My Profile</a>
                        <a class="dropdown-item" href="{{ route('admin.profile.index') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    </div>
                </li>

            </ul>
            <div class="dropdown mobile-user-menu float-end">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i
                        class="fa-solid fa-ellipsis-vertical"></i></a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="">My Profile</a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                </div>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>

        @php
            $segment = Request::segment(1);
            $segment2 = Request::segment(2);
        @endphp

        <div class="sidebar bg-body-secondary" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">{{ session()->get('_company_name') }}</li>

                        <li>
                            <a href="{{route('admin.business')}}"
                                class="{{ request()->route()->getName() == 'admin.business' ? 'active' : '' }}"
                                >
                                <span class="menu-side">
                                    <img src="{{ asset('layout_style/img/icons/business.png') }}" style="width: 24px"  alt>
                                </span>
                                <span>Business</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{route('admin.business-users')}}"
                                class="{{ request()->route()->getName() == 'admin.business-users' ? 'active' : '' }}"
                                >
                                <span class="menu-side">
                                    <img src="{{ asset('layout_style/img/icons/user-group.png') }}" style="width: 24px" alt>
                                </span>
                                <span>Business Users</span>
                            </a>
                        </li>

                        @auth
                        @if(Auth::user()->hasRole('super_admin'))
                            <li>
                                <a href="{{ route('admin.admin-users') }}" class="{{ request()->route()->getName() == 'admin.admin-users' ? 'active' : '' }}">
                                    <span class="menu-side">
                                        <img src="{{ asset('layout_style/img/icons/users-icon.png') }}" style="width: 24px" alt>
                                    </span>
                                    <span>Admin Users</span>
                                </a>
                            </li>
                        @endif
                    @endauth

                    </ul>
                    <div class="logout-btn">
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span
                                class="menu-side"><img src="{{ asset('layout_style/img/icons/logout.ico') }}"
                                    alt></span>
                            <span>Logout</span></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="content">

                @yield('content')

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
    </div>
    <div class="sidebar-overlay" data-reff></div>



    <script src="{{ asset('layout_style/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('layout_style/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('layout_style/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('layout_style/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('layout_style/js/app.js') }}"></script>
    <script src="{{ asset('layout_style/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('layout_style/plugins/select2/js/custom-select.js') }}"></script>
    <script src="{{ asset('layout_style/js/validations.js') }}"></script>
    <script src="{{ asset('layout_style/jquery_confirm/script.js') }}"></script>
    <script src="{{ asset('layout_style/jquery_confirm/popup.js') }}"></script>

    <script src="{{ asset('layout_style/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('layout_style/js/jquery.waypoints.js') }}"></script>
    <script src="{{ asset('layout_style/js/jquery.counterup.min.js') }}"></script>

    <script src="{{ asset('layout_style/cdn_scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js') }}"></script>
    <script src="{{ asset('layout_style/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('layout_style/plugins/apexchart/chart-data.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2()

            $('#change_dashboard').change(function(e) {
                e.preventDefault();
                var id = $(this).val()

                if (id != '') {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    var data = {
                        'id': id
                    }
                    $('#loader').show()
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/business/move_to_dashboard') }}",
                        data: data,
                        dataType: "JSON",
                        success: function(response) {
                            $('#loader').hide()

                            location.href = "{{ route('business.dashboard') }}";

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
                            alert('something went to wrong')
                        }
                    });
                }
            });
        });
    </script>



    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>


    @yield('scripts')
</body>

</html>
