@section('title', $cmsData['meta_title'])
@section('meta_description', $cmsData['meta_description'])
@section('meta_keywords', $cmsData['meta_keywords'])
@extends('store.layouts.products')
@section('header')
Seller Agreement
@endsection
@section('content')
<section class="store-content-section store-agreement-section">
	@include('store.partials.seller_agreement')
</section>
@endsection