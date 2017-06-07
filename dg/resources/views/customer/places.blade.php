@section('title', 'Alchemy Wings - Places')
@section('meta_description', '')
@section('meta_keywords', '')
@section('title')
Alchemy Wings - Places
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
	        <h1>Places</h1>
	    </div>
	</section>
	<section class="page-summary">
		<div class="container">
			<p>Your window to London - a showcase of the Alchemy Wings’ team very favourite venues! Eat, drink and be merry and of course, take the party home with us.</p>
		</div>
	</section>
	<div id='event-content'>
            @include('customer.partials.content-places-listing')
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