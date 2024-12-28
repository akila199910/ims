@extends('layouts.business')

@section('title')
    Manage Purchase Returns
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.purchase_return') }}">Manage Purchase Returns</a>
                    </li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Purchase Return Details</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.purchase_return') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                            <h3 class="text-uppercase">Purchase Return Details</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Returned Pur. ID</h2>
                                                    <h3>{{ $pur_return->purchase_info->invoice_id }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Created At</h2>
                                                    <h3>{{ date('Y-m-d', strtotime($pur_return->created_at)) }}</h3>
                                                </div>
                                            </div>

                                            <div class="col-xl-6 col-md-6 mb-3">
                                                <div class="detail-personal">
                                                    <h2>Status</h2>
                                                    <h3>
                                                        @php
                                                            $statusMap = [
                                                                6 => [
                                                                    'text' => 'Closed',
                                                                    'badge' => 'badge badge-soft-info badge-borders',
                                                                ],
                                                                5 => [
                                                                    'text' => 'Returned',
                                                                    'badge' => 'badge badge-soft-info badge-borders',
                                                                ],
                                                                4 => [
                                                                    'text' => 'Full Filled',
                                                                    'badge' => 'badge badge-soft-warning badge-borders',
                                                                ],
                                                                3 => [
                                                                    'text' => 'Cancelled',
                                                                    'badge' => 'badge badge-soft-danger badge-borders',
                                                                ],
                                                                2 => [
                                                                    'text' => 'On Hold',
                                                                    'badge' => 'badge badge-soft-primary badge-borders',
                                                                ],
                                                                1 => [
                                                                    'text' => 'Approved',
                                                                    'badge' => 'badge badge-soft-success badge-borders',
                                                                ],
                                                                0 => [
                                                                    'text' => 'Pending',
                                                                    'badge' => 'badge badge-soft-primary badge-border',
                                                                    'style' => 'color: purple;',
                                                                ],
                                                            ];
                                                            $statusInfo = $statusMap[$pur_return->status] ?? [
                                                                'text' => 'Unknown',
                                                                'badge' => 'custom-badge status-default',
                                                            ];
                                                        @endphp
                                                        <span class="{{ $statusInfo['badge'] }}"
                                                            style="{{ $statusInfo['style'] ?? '' }}">{{ $statusInfo['text'] }}</span>
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
