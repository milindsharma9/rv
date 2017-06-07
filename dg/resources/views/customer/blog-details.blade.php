@section('title', $contentData->meta_title)
@section('meta_description', $contentData->meta_description)
@section('meta_keywords', $contentData->meta_keywords)
@section('title')
Alchemy Wings - Blog Details
@endsection
@extends('customer.layouts.customer')
@section('content')
	<section class="siteBanner-section-title font-large" style="background-image:url({{ asset('uploads/blog') . '/'.  $contentData->image }});">
		<a href="{{ url()->previous() }}" class="btn-red visible-xs">< Back</a>
    	<img src="{{ asset('uploads/blog') . '/'.  $contentData->image }}"/>
	    <div class="banner-title">
	        <h1>{{$contentData->title}}</h1>
	    </div>
	</section>
    <section class="post-subtitle-date">
        <a href="{{ url()->previous() }}" class="btn-red hidden-xs">< Back</a>
        <div class="container">
            <h3 class="post-subtitle">{{$contentData->sub_title}}</h3>
            @php
            $date = date_create($contentData->created_at);
            @endphp
            <span class="post-date">Posted on {{date_format($date,"d/m/Y")}}</span>
        </div>
    </section>
	<section class="post-details-wrap post-type-blog">
		<div class="post-content-wrap">
			<div class="post-content">
				{!!$contentData->description!!}
			</div>
		</div>
		@include('customer.partials.blog_sidebar')
	</section>
@endsection