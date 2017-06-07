@section('title')
Alchemy Wings - Themes
@endsection
@extends('customer.layouts.customer')
@section('content')
@php
    $bannerImageDir = config('banner.banner_image_dir');
@endphp
<section class="category-banner" style="background-image: url({{ asset('uploads/'.$bannerImageDir.'') . '/'.  $bannerImage }})">
	<h1>What’s the theme?</h1>
</section>
<section class="category-list">
    <ul class="nav nav-tabs tab-level-1 nav-scroll">
        @php
            $i = 0;
            $class='';
        @endphp
        @foreach ($primaryEvents as $event)
            @if($i==0)
                @php
                    $class='active';
                @endphp
            @endif
            <li class="{{$class}}"><a class='theme_page_sub_tab' data-event-id="{!! $event['id'] !!}" data-toggle="tab"><span>{{$event['name']}}</span></a></li>
            @php
                $class='';
                $i++;
            @endphp
        @endforeach
<!--        <li class="active"><a data-toggle="tab"><span>Let’s have a party!</span></a></li>
        <li><a data-toggle="tab"><span>Time with friends</span></a></li>-->
    </ul>
    <div id="category-content">
    	<div class="category-wrap" id='explore-sub-occasion'>
    		@include('customer.partials.sub_occasion_themes_popup', ['isTheme' => true])
    	</div>
    </div>
</section>
@include('customer.partials.cart-modal')
@endsection
@section('footer-scripts')
<script>
    var moodUrl                 = "{!! route('search.mood')!!}";
    var loadingImgUrl           = "{!! url('alchemy/images/loadingstock.gif')!!}";
    var occasionUrl             = "{!! route('search.occasion')!!}";
    var getSubOccasionUrl       = "{!! route('get.occasion', 1)!!}";
    var getSubEventUrl       = "{!! route('get.submood', 1)!!}";
    getSubOccasionUrl           = getSubOccasionUrl.slice(0, -2);
    getSubEventUrl           = getSubEventUrl.slice(0, -2);
</script>
@endsection