@section('title', $cmsData['meta_title'])
@section('meta_description', $cmsData['meta_description'])
@section('meta_keywords', $cmsData['meta_keywords'])
@extends('layouts.api')
@section('content')
    @include('partials.privacy')
@endsection