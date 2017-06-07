@section('title', 'Alchemy Wings - Archive')
@section('meta_description', '')
@section('meta_keywords', '')
@section('title')
Alchemy Wings - Archive
@endsection
@section('body-class')
blog-listing-type-pages
@endsection
@extends('customer.layouts.customer')
@section('content')
        @php
            $bannerImageDir = config('banner.banner_image_dir');
        @endphp
	<section class="siteBanner-section-title font-large" style="background-image:url({{ asset('alchemy/images/keywords-banner.png') }})">
    	<a href="{{ url()->previous() }}" class="btn-red visible-xs">< Back</a>
    	<img src="{{ asset('alchemy/images/keywords-banner.png') }}"/>
	    <div class="banner-title">
	        <h1>Archive</h1>
	    </div>
	</section>
	<section class="page-summary">
		<a href="{{ url()->previous() }}" class="btn-red hidden-xs">< Back</a>
		<div class="container">
			<p>Get inspired by regular features & one-on-one interviews with some of the city's best hospitality talent. 
                          Stay informed with our up-to-date '<a href="{{route('common.events')}}">Events</a>' page showcasing the hottest things to do in London. Get yourself educated with our regular '<a href="{{route('common.places')}}">Places</a>' features - the BEST bars, restaurants, pop-ups, cafes and go-to's this side of the channel!</p>
		</div>
	</section>
	<div class="section-title sub-title-menus">
		<span><span>Posted on <a id="selected-month" data-month-id='{{$monthId}}' data-year-id='{{$year}}'>{{$currentMonthLabel}}</a></span>
			<div class="month-category-list">
                            @php
                                $allMonth = CommonHelper::getArchiveYears($year);
                            @endphp
				<ul class="sub-category">
				    <li class="current-year">
				        <a class="sub-category-title" href=""><span>All Archive Events</span></a>
				        <ul>
                                            
                                            @foreach($allMonth as $monthId => $monthName)
                                            @php
                                            @endphp
                                            <li><a data-archive="1" id="upcoming-events-{{$monthId}}" data-month="{{$monthName}}" data-year="{{$year}}">{{$monthName}}</a></li>
                                            @endforeach
				        </ul>
				    </li>
				</ul>
			</div>
		</span>
	</div>
        <div id='event-content'>
            @include('customer.partials.event-listing')
        </div>
@endsection
@section('javascript')
<script src="{{ url('external') }}/jquery.infinitescroll.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/infinitescroll-custom.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
var eventURL = "{{ route('event.month.details')}}";
var paginationCustom = '1';
</script>
@endsection