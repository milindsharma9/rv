@extends('store.layouts.products')
@section('title')
Alchemy Store - Change Password
@endsection
@section('header')
Change Password
@endsection
@section('content')
<section class="store-content-section store-profile-edit-section">
    <div class="container">
        @include('partials.change-password')
    </div>
</section>
@endsection