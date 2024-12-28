@extends('layouts.business')

@section('title')
 Manage Categories
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.category') }}">Manage Categories</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Category Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.category') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                            <h3 class="text-uppercase">Category Details</h3>
                                        </div>
                                        <div class="row  align-items-center">
                                            <div class="col-xl-4 col-md-4 text-center">
                                                <div class="detail-personal">
                                                    <h3>
                                                        @if ($categories->image && $categories->image != 0)
                                                        <img src="{{ config('awsurl.url') . $categories->image }}"
                                                            alt="Product Image" height="100px" width="100px"
                                                            style="border-radius:50%;object-fit: cover; align:center" class="stylist-image">
                                                    @else
                                                        <img src="{{ asset('layout_style/img/category.jpg') }}"
                                                            alt="Default Image" height="100px" width="100px"
                                                            style="border-radius:50%;object-fit: cover; align:center" class="stylist-image" >
                                                    @endif

                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-8 col-md-8">
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Name</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ Str::limit(ucwords($categories->name), 30) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Status</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>
                                                                @php
                                                                    $status =
                                                                        $categories->status == 1
                                                                            ? 'Active'
                                                                            : 'Inactive';
                                                                    $badgeClass =
                                                                        $categories->status == 1
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
        </div>
    </div>
@endsection

