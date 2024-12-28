@extends('layouts.home')

@section('title')
Manage Admin Users
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.admin-users') }}">Manage Admin Users</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Admin User Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('admin.admin-users') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="doctor-personals-grp">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="doctor-table-blk mb-4 pt-2">
                                            <h3 class="text-uppercase">Admin User Details</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>First Name</h2>
                                                    <h3>{{ ucwords($admin_users->first_name) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Last Name</h2>
                                                    <h3>{{ ucwords($admin_users->last_name) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Full Name</h2>
                                                    <h3>{{ ucwords($admin_users->name) }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Email Address</h2>
                                                    <h3>{{ $admin_users->email }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Contact</h2>
                                                    <h3>{{ $admin_users->contact }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-12 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Status </h2>
                                                    <h3>
                                                        @if ($admin_users->status == 1)
                                                            <span class="custom-badge status-green ">Active</span>
                                                        @else
                                                            <span class="custom-badge status-red ">Inactive</span>
                                                        @endif
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
