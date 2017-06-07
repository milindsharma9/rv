@section('title', $contentData->meta_title)
@section('meta_description', $contentData->meta_description)
@section('meta_keywords', $contentData->meta_keywords)
@section('title')
Alchemy Wings - Places Details
@endsection
@extends('customer.layouts.customer')
@section('content')

	<section class="siteBanner-section-title font-large" style="background-image:url({{ asset('uploads/blog') . '/'.  $contentData->image }});">
		<a href="{{ url()->previous() }}" class="btn-red visible-xs">< Back</a>
    	<img src="{{ asset('uploads/blog') . '/'.  $contentData->image }}"/>
	    <div class="banner-title">
	        <h1>{{$contentData->location}}</h1>
	    </div>
	</section>
	<section class="post-details-wrap post-type-event post-type-places">
		<div class="post-content-wrap">
			<a href="{{ url()->previous() }}" class="btn-red hidden-xs">< Back</a>
			<ul class="post-info">
                                <?php
                                $mapAddress = $mapAddressShow = "";
                                if (!empty($contentData->address)) {
                                    $mapAddress .= $contentData->address . ",";
                                    $mapAddressShow .= $contentData->address . ",";
                                }
                                if (!empty($contentData->city)) {
                                    $mapAddress .= $contentData->city . ",";
                                    $mapAddressShow .= $contentData->city . ",";
                                }
                                if (!empty($contentData->state)) {
                                    $mapAddress .= $contentData->state . ",";
                                    $mapAddressShow .= $contentData->state . ",";
                                }
                                if (!empty($contentData->pin)) {
                                    $mapAddress .= $contentData->pin . ",";
                                }
                                if (!empty($mapAddress)) {
                                    $mapAddress     = rtrim($mapAddress, ",");
                                    $mapAddressShow = rtrim($mapAddressShow, ",");
                                }
                            ?>
				<li class="location-detail">
					<span class="location-address">
                        <span class="address">{{$mapAddressShow}}</span>
                        <span class="zip">{{$contentData->pin}}</span>
                    </span>
				</li>
                <li class="contact-detail">
                    <span class="location-contact">
                        <span class="phone">{{$contentData->places_drink_text}}</span>
                        <span class="email"><a>{{$contentData->places_drink_url}}</a></span>
                    </span>
                </li>
                    @if(!empty($contentData->places_food_text))
                        <li class="summary-content">
                            <span>{{$contentData->places_food_text}}</span>
                        </li>
                    @endif
                    @if(!empty($contentData->places_food_url))
                        <li class="drink-content">
                            <span>{{$contentData->places_food_url}}</span>
                        </li>
                    @endif
                </ul>
                        @if (!empty($contentData->pin))
                            <div class="map" id='map'>
                                <?php
                                if (empty($mapAddress)) {
                                    $mapAddress = '332 Ladbroke Grove, London, W10 5AS';
                                }
                                //$mapAddress = urlencode($mapAddress);
                                ?>
                                <!--<iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.it/maps?q=<?php echo $mapAddress; ?>&output=embed"></iframe>-->
                            </div>
                        @endif
			<div class="post-content">
				{!!$contentData->description!!}
			</div>
		</div>
		@include('customer.partials.blog_sidebar')
	</section>
@endsection
@section('javascript')
    <script type="text/javascript">
        var map_address = '<?php echo $mapAddress ?>';
        var map_marker_image = "{{ url('alchemy/images/map_marker.png') }}";
    </script>
    <script type="text/javascript" src="{{ url('alchemy/js') }}/map.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src='https://maps.googleapis.com/maps/api/js?key=AIzaSyCBGx3A0dv7cs5isfLJNXeINT9UxeVcKQQ&callback=initMap'></script>
@endsection