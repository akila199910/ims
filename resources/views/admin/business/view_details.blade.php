@extends('layouts.home')

@section('title')
Manage Businesses
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.business') }}">Manage Businesses</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Business Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('admin.business') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                            <h3 class="text-uppercase">Business Details</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Name</h2>
                                                    <h3>{{ ucwords($business->name) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Email</h2>
                                                    <h3>{{ ucwords($business->email) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Contact</h2>
                                                    <h3>{{ ucwords($business->contact) }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Address</h2>
                                                    <h3>{{ ucwords($business->address) }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Status</h2>
                                                    <h3>
                                                        @php
                                                        $status = $business->status == 1 ? 'Active' : 'Inactive';
                                                        $badgeClass =
                                                            $business->status == 1
                                                                ? 'custom-badge status-green'
                                                                : 'custom-badge status-red';
                                                    @endphp
                                                    <span class="{{ $badgeClass }}">{{ $status }}</span>
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
