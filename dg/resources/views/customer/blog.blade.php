@section('title', 'Alchemy Wings - Blog')
@section('meta_description', '')
@section('meta_keywords', '')
@section('title')
Alchemy Wings - Blog
@endsection
@section('body-class')
blog-listing-type-pages
@endsection
@extends('customer.layouts.customer')
@section('content')
        @php
            $bannerImageDir = config('banner.banner_image_dir');
        @endphp
	<section class="siteBanner-section-title font-large" style="background-image:url({{ asset('uploads/'.$bannerImageDir.'') . '/'.  $bannerImage }})">
    	<img src="{{ asset('uploads/'.$bannerImageDir.'') . '/'.  $bannerImage }}"/>
	    <div class="banner-title">
	        <h1>Blog</h1>
	    </div>
	</section>
	<section class="page-summary">
		<div class="container">
			<p>Get inspired by regular features & one-on-one interviews with some of the city's best hospitality talent. 
                          Stay informed with our up-to-date '<a href="{{route('common.events')}}">Events</a>' page showcasing the hottest things to do in London. Get yourself educated with our regular '<a href="{{route('common.places')}}">Places</a>' features - the BEST bars, restaurants, pop-ups, cafes and go-to's this side of the channel!</p>
		</div>
	</section>
	<div id='event-content'>
            @include('customer.partials.content-blog-listing')
        </div>
@endsection
@section('javascript')
<script src="{{ url('external') }}/jquery.infinitescroll.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/infinitescroll-custom.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
var eventURL = "{{ route('event.month.details')}}";
var paginationCustom = '0';
</script>
@endsection