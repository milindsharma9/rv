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
	<section class="siteBanner-section-title font-large" style="background-image:url({{ asset('alchemy/images/keywords-banner.png') }})">
    	<a href="{{ url()->previous() }}" class="btn-red visible-xs">< Back</a>
    	<img src="{{ asset('alchemy/images/keywords-banner.png')  }}"/>
	    <div class="banner-title">
	        <h1>{{$selectedKeyword}}</h1>
	    </div>
	</section>
	<section class="page-summary hidden-xs">
		<a href="{{ url()->previous() }}" class="btn-red">< Back</a>
	</section>
	<div class="section-title sub-title-menus">
            <span>
                <span>Showing <a id="selected_content_type">Events</a></span>
                <div class="month-category-list">
                    <ul class="sub-category">
                        <li class="current-year">
                            <a class="sub-category-title"><span>All Events</span></a>
                            <ul>
                                <li class='keyword_event_type' data-type="{{config('blog.type_event')}}"><a >Events</a></li>
                                <li class='keyword_event_type' data-type="{{config('blog.type_place')}}"><a >Places</a></li>
                                <li class='keyword_event_type' data-type="{{config('blog.type_blog')}}"><a >Posts</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </span>
	</div>
        <input type="hidden" id='contentId' value="{{config('blog.type_event')}}">
        <input type="hidden" id='contentKeyword' value="{{$selectedKeyword}}">
	<div id='event-content'>
            @include('customer.partials.keyword-partial')
        </div>
@endsection
@section('javascript')
<script src="{{ url('external') }}/jquery.infinitescroll.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/infinitescroll-custom.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
var keywordRelatedContentUrl = "{{ route('common.keyword.content.type.get')}}";
var selectedKeyword = "<?php echo $selectedKeyword; ?>";
var paginationCustom = 'keyword';
$('.keyword_event_type').click(function () {
        contentTypeId = $(this).data('type');
        var selectedContentType = $(this).find('a').html();
        $.ajax({
            url: keywordRelatedContentUrl,
            type: "POST",
            data: {'keyword': selectedKeyword, 'content': contentTypeId},
            headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
            dataType: 'json',
            success: function (data) {
                $('#event-content').html(data.html_content);
                $('#selected_content_type').html(selectedContentType);
                $('#contentId').val(contentTypeId);
                $('#contentKeyword').val(selectedKeyword);
                pagination(paginationCustom);
            },
            error: function (e) {
                alert("Some error. Please try refreshing page.");
            }
        });
    });
</script>
@endsection
