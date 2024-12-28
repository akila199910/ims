@extends('layouts.business')

@section('title')
 Manage Stock Transfer
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.stock_transfer') }}">Manage Stock Transfer</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Stock Transfer Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.stock_transfer') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                            <h3 class="text-uppercase">Stock Transfer Details</h3>
                                        </div>
                                        <div class="row  align-items-center">
                                            <div class="col-xl-4 col-md-4 text-center">
                                                <div class="detail-personal">
                                                    <h3>
                                                        <div class="col-sm-12 mt-3 text-center">
                                                            <img src="{{ $stock_transfer->product_info->image == '' || $stock_transfer->product_info->image == 0 ? asset('layout_style/img/icons/product_100.png') : config('aws_url.url') . $stock_transfer->product_info->image }}"
                                                                style="width: 75px; height: 75px;" alt="">
                                                        </div>
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-8 col-md-8">
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Product Name</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ Str::limit(ucwords($stock_transfer->product_info->name),30) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>From Warehouse</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ Str::limit(ucwords($stock_transfer->from_warehouse->name),30) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>To Warehouse</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ Str::limit(ucwords($stock_transfer->to_warehouse->name),30) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Transferred Date</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ $stock_transfer->transfer_date }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Transferred QTY</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ $stock_transfer->qty  }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Created By</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ Str::limit(ucwords($stock_transfer->creator_info->name,30) ) }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h2>Edited By</h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4 col-md-4">
                                                        <div class="detail-personal">
                                                            <h3>{{ Str::limit(ucwords($stock_transfer->editor_info->name,30) ) }}</h3>
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

