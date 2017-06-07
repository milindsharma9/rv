@section('title', $contentData->meta_title)
@section('meta_description', $contentData->meta_description)
@section('meta_keywords', $contentData->meta_keywords)
@section('title')
Alchemy Wings - Events
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
	<section class="post-details-wrap post-type-event">
		<div class="post-content-wrap">
			<a href="{{ url()->previous() }}" class="btn-red hidden-xs">< Back</a>
			<ul class="post-info">
				<li class="date-time">
                                    <?php
                                    
                                        $eventStartDate         = $contentData->start_date;
                                        $eventEndDate           = $contentData->end_date;
                                        $aEventStartDate        = explode(" ", $eventStartDate);
                                        $aEventEndDate          = explode(" ", $eventEndDate);
                                        $timeStartPrefix        = substr($aEventStartDate[1], 0, 2);
                                        $timeStartMinPrefix        = substr($aEventStartDate[1], 3, 2);
                                        $timeEndPrefix          = substr($aEventEndDate[1], 0, 2);
                                        $timeEndMinPrefix          = substr($aEventEndDate[1], 3, 2);
                                        $contentData->eventDay  = ucfirst(strtolower($contentData->eventDay));
                                        $eventDayPrefix = "";
                                        $eventDuration  = "";
                                        if ($timeStartPrefix == '00') {
                                            $eventDayPrefix = "All day ";
                                        } else {
                                            $eventDuration = $timeStartPrefix . ":" . $timeStartMinPrefix . 'h' . " - " .
                                                $timeEndPrefix . ":" . $timeEndMinPrefix . 'h' ;
                                        }
                                    ?>
					<span class="date">{{ $contentData->eventDate}}</span>
					<span class="month">{{substr($contentData->eventMonth, 0, 3)}}</span>
					<strong class="day">{{$eventDayPrefix . $contentData->eventDay}}</strong>
                                        @if (!empty($eventDuration))
                                            <span class="time">{{$eventDuration}}</span>
                                        @endif
                                        <span class="time">&nbsp;</span>
				</li>
				<li class="entrance-detail">
                                    <a href="{{$contentData->event_ticket_url}}" target="_blank"><strong>{{$contentData->event_ticket_text}}</strong></a>
				</li>
                                <?php
                                $mapAddress = "";
                                if (!empty($contentData->address)) {
                                    $mapAddress .= $contentData->address . ",";
                                }
                                if (!empty($contentData->city)) {
                                    $mapAddress .= $contentData->city . ",";
                                }
                                if (!empty($contentData->state)) {
                                    $mapAddress .= $contentData->state . ",";
                                }
                                if (!empty($contentData->pin)) {
                                    $mapAddress .= $contentData->pin . ",";
                                }
                                if (!empty($mapAddress)) {
                                    $mapAddress = rtrim($mapAddress, ",");
                                }
                            ?>
				<li class="location-detail">
					<strong class="location-name">{{$contentData->location}}</strong>
					<span class="location-address">{{$mapAddress}}</span>
				</li>
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