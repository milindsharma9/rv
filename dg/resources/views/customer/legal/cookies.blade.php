@section('title', $cmsData['meta_title'])
@section('meta_description', $cmsData['meta_description'])
@section('meta_keywords', $cmsData['meta_keywords'])
@extends('customer.layouts.customer')
@section('content')
    @include('partials.cookies')
@endsection