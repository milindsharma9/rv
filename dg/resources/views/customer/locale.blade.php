@section('title', $localeData->meta_title)
@section('meta_description', $localeData->meta_description)
@section('meta_keywords', $localeData->meta_keywords)
@section('body-class')
locale-listing-type-pages
@endsection
@extends('customer.layouts.customer')
@section('content')
@php
$bannerImageDir = config('banner.banner_image_dir');
@endphp
<section class="siteBanner-section-title font-large" style="background-image:url({{ asset('uploads/locale').'/'. $localeData->image }});">
    <img src="{{ asset('uploads/locale').'/'. $localeData->image }}"/>
    <div class="banner-title">
        <h1>{{$localeData->title}}</h1>
    </div>
</section>
<section class="page-summary">
    <div class="container">
        <p>{{$localeData->sub_title}}</p>
    </div>
</section>
<div class="section-title sub-title-menus">
    <span><span><span class="hidden-xs">Show</span> <a id="selected_content_type">Places, events & posts</a></span>
        <div class="month-category-list">
            <ul class="sub-category">
                @php
                $aBlogType = config('blog.type');
                @endphp
                <li ><label class="check-option"><input checked="" class='keyword_event_type' data-type="{{$aBlogType[config('blog.type_place')]}}" value="{{config('blog.type_place')}}" type="checkbox" name="locale_select" >Places</label></li>
                <li ><label class="check-option"><input checked="" class='keyword_event_type' data-type="{{$aBlogType[config('blog.type_event')]}}" value="{{config('blog.type_event')}}" type="checkbox" name="locale_select" >Events</label></li>
                <li ><label class="check-option"><input checked="" class='keyword_event_type' data-type="Posts" value="{{config('blog.type_blog')}}" type="checkbox" name="locale_select" >Posts</label></li>
            </ul>
        </div>
    </span>
</div>
<section class="post-listing post-type-blog">
    @if (!empty($contentData->count()))
    <div class="three-column-group">
        <div class="three-column-group-inner" id='event-content'>
            <!--
            Partial
            -->
            @include('customer.partials.locale-content-listing')
        </div>
        @if($nextPage != NULL)
        <div class="more-link load-more">
            <div class="container">
                <a href="#" id="locale-data-listing">Load More</a>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="post-listing-empty">
        <img src="{{ url('alchemy/images') }}/broken-cycle.svg">
        <h1>No data available.</h1>
    </div>
    @endif
</section>
<section class="page-info">
    <h3 class="section-title visible-xs"><span>More about {{$localeData->title}}</span></h3>
    <div class="container">
        <h3 class="section-title hidden-xs"><span>More about {{$localeData->title}}</span></h3>
        <div class="content">
            <p>{{$localeData->description}}</p>
        </div>
        <div class="more-link">
            <div class="container">
                <div class="row">
                    <a href="">See more</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="product-list customer-create-listing-section customer-product-description-section">
    <h3 class="section-title visible-xs"><span>Related products</span></h3>
    <div class="container hidden-xs">
        <h3 class="section-title"><span>Related products</span></h3>
    </div>
    <div class="event-listing-wrap">
        @if(isset($recipeMapping) && count($recipeMapping) > 0)
        <div class="sub-category-list-wrap">
            <div class="container">
                <div class="product-list-group">
                    <div class="category-name">Recipes</div>
                    <ul>
                        @foreach($recipeMapping AS $key => $value)
                        @php $image = $value->image_thumb ;@endphp
                        @if($value->image_thumb == '')
                        @php $image = $value->image ;@endphp
                        @endif
                        <li>
                            <div class="product-img">
                                <a href="{!!route('customer.bundleDetail',['productId' => $value->id ])!!}">
                                    <img src="{!! CommonHelper::getBundleImage($image, true) !!}" >
                                </a>
                            </div>
                            <div class="product-info">
                                <a href="{!!route('customer.bundleDetail',['productId' => $value->id ])!!}">
                                    <h3 class="product-name">{!! $value->name !!}</h3>
                                    @if(isset($value->serves))
                                    <p class="product-serve">{!! CommonHelper::formatProductDescription($value->serves) !!}</p>
                                    @endif
                                </a>
                                <a data-name="{{ $value->name }}" data-id="{{ $value->id }}" class="product-price bundle-price">{{Config::get('appConstants.currency_sign')}}{!! CommonHelper::formatPrice($value->price)!!}</a>
                            </div>
                            <div class="product-cart">
                                <a data-name="{{ $value->name }}" data-id="{{ $value->id }}" class="product-add-cart-bundle">
                                    <span data-item-id-bundle="{{ $value->id }}" class="product_cart_count">{!! CommonHelper::getProductCartCount($value->id, true) !!}</span>
                                </a>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
        @if(isset($bundleMapping) && count($bundleMapping) > 0)
        <div class="sub-category-list-wrap">
            <div class="container">
                <div class="product-list-group">
                    <div class="category-name">Bundles</div>
                    <ul>
                        @foreach($bundleMapping AS $key => $value)
                        @php $image = $value->image_thumb ;@endphp
                        @if($value->image_thumb == '')
                        @php $image = $value->image ;@endphp
                        @endif
                        <li>
                            <div class="product-img">
                                <a href="{!!route('customer.bundleDetail',['productId' => $value->id ])!!}">
                                    <img src="{!! CommonHelper::getBundleImage($image, true) !!}" >
                                </a>
                            </div>
                            <div class="product-info">
                                <a href="{!!route('customer.bundleDetail',['productId' => $value->id ])!!}">
                                    <h3 class="product-name">{!! $value->name !!}</h3>
                                    @if(isset($value->serves))
                                    <p class="product-serve">{!! CommonHelper::formatProductDescription($value->serves) !!}</p>
                                    @endif
                                </a>
                                <a data-name="{{ $value->name }}" data-id="{{ $value->id }}" class="product-price bundle-price">{{Config::get('appConstants.currency_sign')}}{!! CommonHelper::formatPrice($value->price)!!}</a>
                            </div>
                            <div class="product-cart">
                                <a data-name="{{ $value->name }}" data-id="{{ $value->id }}" class="product-add-cart-bundle">
                                    <span data-item-id-bundle="{{ $value->id }}" class="product_cart_count">{!! CommonHelper::getProductCartCount($value->id, true) !!}</span>
                                </a>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
        @if($localeProducts['hasProducts'])
        <?php
        unset($localeProducts['hasProducts']);
        ?>
        @foreach($localeProducts AS $catName => $aProducts)
        @if (!empty($aProducts))
        <div class="sub-category-list-wrap">
            <div class="container">
                <div class="product-list-group">
                    <div class="category-name">{{$catName}}</div>
                    <ul>
                        @foreach($aProducts AS $key => $value)
                        <li>
                            <div class="product-img">
                                <a href="{!!route('products.detail',['productId' => $value->id ]) !!}">
                                    <img src="{!! CommonHelper::getProductImage($value->id, true) !!}">
                                </a>
                            </div>
                            <div class="product-info">
                                <a href="{!!route('products.detail',['productId' => $value->id ]) !!}">
                                    <h3 class="product-name">{!! CommonHelper::formatProductDescription($value->description) !!}</h3>
                                </a>
                                <a data-alcohol="{!! CommonHelper::formatProductDescription($value->description) !!}" data-price="{{ $value->price }}" data-name="{{ $value->name }}" data-id="{{ $value->id }}" class="product-price">{{Config::get('appConstants.currency_sign')}}{!! CommonHelper::formatPrice($value->price)!!}</a>
                            </div>
                            <div class="product-cart">
                                <a data-alcohol="{!! CommonHelper::formatProductDescription($value->description) !!}" data-price="{{ $value->price }}" data-name="{{ $value->name }}" data-id="{{ $value->id }}" class="product-add-cart">
                                    <span data-item-id="{{ $value->id }}" class="product_cart_count">{!! CommonHelper::getProductCartCount($value->id, false) !!}</span>
                                </a>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
        @endforeach
        @endif
    </div>
    @if (!empty($aProducts))
        <div class="more-link">
            <div class="container">
                <div class="row">
                    <a href="{{route('customer.products')}}">See All</a>
                </div>
            </div>
        </div>
    @endif
</section>
<input type="hidden" name="_token" value="{!! csrf_token() !!}" />
@include('customer.partials.cart-modal')
@endsection
@section('javascript')
<script src="{{ url('external') }}/jquery.infinitescroll.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/infinitescroll-custom.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
var eventURL = "";
var paginationCustom = '1';
var cartAddUrl = "{!! route('customer.cart.add')!!}";
var cartUpdateUrl = "{!! route('customer.cart.update')!!}";
var getBundleDetailUrl = "{!! route('customer.getBundleDetail', 1)!!}";
getBundleDetailUrl = getBundleDetailUrl.slice(0, -2);
var checkCustomerCartStatusUrl = "{!! route('customer.cart.status.check')!!}";

var keywordRelatedContentUrl = "{{ route('common.locale.content.type.get')}}";
var selectedKeyword = "<?php echo $keywordUrl; ?>";
var paginationCustom = 'keyword';
var nextUrl = "{{ $nextPage}}";
var pieces = nextUrl.split("=");
var page = pieces[1];

$(document).on('click','input[name*="locale_select"]',function(){
    if($('input[name*="locale_select"]:checked').length < 2){
        $(this).closest('li').siblings('li').find('input[name*="locale_select"]:checked').prop('disabled',true);
    }
    else {
        $('input[name*="locale_select"]:checked').prop('disabled',false);
    }
})

$('.keyword_event_type').click(function () {
    var aSelBlogTypeId = [];
    var selectedHtml = '';
    $("input:checkbox[name=locale_select]:checked").each(function () {
        aSelBlogTypeId.push($(this).val());
        selectedHtml += $(this).data('type') + ", "
    });
    selectedHtml = selectedHtml.slice(0, -1);
    selectedHtml = selectedHtml.substring(0, selectedHtml.length - 1);
    if (aSelBlogTypeId.length < 1) {
        return false;
    }
    $.ajax({
        url: keywordRelatedContentUrl,
        type: "POST",
        data: {'keyword': selectedKeyword, 'content': aSelBlogTypeId, nextPage: ''},
        headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
        dataType: 'json',
        success: function (data) {
            $('#event-content').html(data.html_content);
            $('#selected_content_type').html(selectedHtml);
            nextUrl = data.nextPage;
            if(typeof nextUrl === 'undefined' || !nextUrl || nextUrl == 'NULL'){
                $('.load-more').hide();
            }else{
                $('.load-more').show();
            }
        },
        error: function (e) {
            alert("Some error. Please try refreshing page.");
        }
    });
});
$('#locale-data-listing').click(function (e) {
    e.preventDefault();
    var pieces = nextUrl.split("=");
    var page = pieces[1];
    var aSelBlogTypeId = [];
    var selectedHtml = '';
    $("input:checkbox[name=locale_select]:checked").each(function () {
        aSelBlogTypeId.push($(this).val());
        selectedHtml += $(this).data('type') + ", "
    });
    selectedHtml = selectedHtml.slice(0, -1);
    selectedHtml = selectedHtml.substring(0, selectedHtml.length - 1);
    if (aSelBlogTypeId.length < 1) {
        return false;
    }
    $.ajax({
        url: keywordRelatedContentUrl,
        type: "POST",
        data: {'keyword': selectedKeyword, 'content': aSelBlogTypeId, nextPage: page},
        headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
        dataType: 'json',
        success: function (data) {
            $('#event-content').append(data.html_content);
            $('#selected_content_type').html(selectedHtml);
            nextUrl = data.nextPage;
            if(typeof nextUrl === 'undefined' || !nextUrl || nextUrl == 'NULL'){
                $('.load-more').hide();
            }else{
                $('.load-more').show();
            }
        },
        error: function (e) {
            alert("Some error. Please try refreshing page.");
        }
    });
});
</script>
@endsection