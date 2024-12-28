@extends('layouts.business')

@section('title')
    Manage Vendors
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.suppliers') }}">Manage Vendors</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Vendors Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.suppliers') }}"  class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                            <h3 class="text-uppercase">Vendor Details</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Vendor Name</h2>
                                                    <h3>{{ ucwords($supplier->name) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Address</h2>
                                                    <h3>{{ $supplier->address }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Email</h2>
                                                    <h3>{{ $supplier->email }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Contact </h2>
                                                    <h3>{{ ucwords($supplier->contact ) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Date Of Agreement </h2>
                                                    <h3>{{ ucwords($supplier->date_of_agree ) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Date Of Exp</h2>
                                                    <h3>{{ ucwords($supplier->date_agree_exp ) }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Payment Terms</h2>
                                                    <h3>{{ ucwords($supplier->payment_information->PaymentTermsInfo->payement_term ) }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Status</h2>
                                                    <h3>
                                                        @php
                                                            $status =
                                                                $supplier->status == 1
                                                                    ? 'Active'
                                                                    : 'Inactive';
                                                            $badgeClass =
                                                                $supplier->status == 1
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
