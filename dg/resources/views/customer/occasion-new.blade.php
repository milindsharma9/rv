@section('title')
Alchemy Wings - Occasion
@endsection
@extends('customer.layouts.customer')
@section('content')
@php
    $bannerImageDir = config('banner.banner_image_dir');
@endphp
<section class="category-banner" style="background-image: url({{ asset('uploads/'.$bannerImageDir.'') . '/'.  $bannerImage }})">
	<h1>What’s the occasion?</h1>
</section>
<section class="category-list">
    <ul class="nav nav-tabs tab-level-1 nav-scroll">
        @php
            $i = 0;
            $class='';
        @endphp
        @foreach ($primaryOccasion as $occasion)
            @if($i==0)
                @php
                    $class='active';
                @endphp
            @endif
            <li class="{{$class}}"><a class='occasion_page_sub_tab' data-occasion-id="{!! $occasion['id'] !!}" data-toggle="tab"><span>{{$occasion['name']}}</span></a></li>
            @php
                $class='';
                $i++;
            @endphp
        @endforeach
    </ul>
    <div id="category-content">
    	<div class="category-wrap" id='explore-sub-occasion'>
            @include('customer.partials.sub_occasion_themes_popup')
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
    getSubOccasionUrl           = getSubOccasionUrl.slice(0, -2);
</script>
@endsection