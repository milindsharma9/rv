@section('title')
Seshaashai - Change Password
@endsection
@extends('customer.layouts.customer')
@section('header')
<a href="{{ route('customer.dashboard') }}">Change Password</a>
@endsection
@section('content')
<section class="customer-content-section store-profile-edit-section change-password-section">
    <div class="container">        
        @include('partials.change-password')
    </div>
</section>
@endsection