@section('title', 'Alchemy Wings - Events')
@section('meta_description', '')
@section('meta_keywords', '')
@section('title')
Alchemy Wings - Events
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
        <h1>Events</h1>
    </div>
</section>
<section class="page-summary">
    <div class="container">
        <p>Stay informed with our up-to-date events calendar showcasing the hottest 
            things to do in London. Bar and nightclub events, cultural, family-friendly, 
            late night, it’s all here! You're welcome.</p>
    </div>
</section>
<div class="section-title sub-title-menus">
    <span><span><span id="event-type">Upcoming events</span>  <a id="selected-month" data-month-id='{{$currentMonth}}' data-year-id='{{$currentYear}}'>{{$currentMonthLabel}}</a></span>
        <div class="month-category-list">
            <ul class="sub-category">
                @php
                    $allMonth = CommonHelper::getMonthYear();
                @endphp
                <li class="current-year">
                    <a class="sub-category-title"><span>All events</span></a>
                    <ul>
                        @foreach($allMonth['future'] as $monthId => $monthName)
                        @php
                            $yearMonth = explode('|', $monthId);
                        @endphp
                        <li><a data-archive="0" id="upcoming-events-{{$yearMonth[1]}}" data-month-type="upcoming" data-month="{{$monthName}}" data-year="{{$yearMonth[0]}}">{{$monthName}}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li class="past-year">
                    <a class="sub-category-title"><span>Past events</span></a>
                    <ul>
                        @foreach($allMonth['past'] as $monthId => $monthName)
                        @php
                            $yearMonth = explode('|', $monthId);
                        @endphp
                        <li><a data-archive="1" id="upcoming-events-{{$yearMonth[1]}}" data-month-type="past" data-month="{{$monthName}}" data-year="{{$yearMonth[0]}}">{{$monthName}}</a></li>
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