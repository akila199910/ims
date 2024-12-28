@extends('errors.error_layout')

@section('title')
    Not Found
@endsection

@section('content')
    <img class="img-fluid" src="{{ asset('layout_style/img/error-01.png') }}" alt="Logo">
    <h3><img class="img-fluid mb-0" src="{{ asset('layout_style/img/icons/danger.svg') }}" alt="Logo"> Service Unavailable
    </h3>
    <p>You may have mistyped the address or the page may have moved.</p>
    <a href="{{ route('login') }}" class="btn btn-primary go-home">Back to Home</a>
@endsection
