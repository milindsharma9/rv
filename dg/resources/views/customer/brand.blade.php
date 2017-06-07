@section('title', $brandData->meta_title)
@section('meta_description', $brandData->meta_description)
@section('meta_keywords', $brandData->meta_keywords)
@section('body-class')
locale-listing-type-pages brand-listing-type-page
@endsection
@section('body-style')
background-image: url({{ asset('uploads/brand').'/'. $brandData->image_background }});
@endsection
@extends('customer.layouts.customer')
@section('content')
@php
$bannerImageDir = config('banner.banner_image_dir');
@endphp
@if(empty($brandData->image2) && empty($brandData->image3) && empty($brandData->image4))
    <section class="siteBanner-section-title banner-static">
        <img class="multibanner-image" src="{{ asset('uploads/brand').'/'. $brandData->image }}"/>
    </section>
@else
    <section class="siteBanner-section-title font-large multibanner-section">
        @if($brandData->image)
        <img class="multibanner-image" src="{{ asset('uploads/brand').'/'. $brandData->image }}"/>
        @endif
        @if($brandData->image2)
        <img class="multibanner-image" src="{{ asset('uploads/brand').'/'. $brandData->image2 }}"/>
        @endif
        @if($brandData->image3)
        <img class="multibanner-image" src="{{ asset('uploads/brand').'/'. $brandData->image3 }}"/>
        @endif
        @if($brandData->image4)
        <img class="multibanner-image" src="{{ asset('uploads/brand').'/'. $brandData->image4 }}"/>
        @endif
        <div class="banner-title">
            <!--<h1>{{$brandData->title}}</h1>-->
        </div>
        <div class="multibanner-banner-wrap">

        </div>
    </section>
@endif
<section class="page-summary">
    <div class="container">
        <p>{!!$brandData->sub_title!!}</p>
    </div>
</section>
<section class="product-list customer-product-section">
    <h3 class="section-title"><span>{{$brandData->title}}</span></h3>
    <div class="container">
        <ul class="product_list">
            @foreach($brandProducts as $product)
                <li>
                    <div class="product-image">
                        <a href="{!!route('products.detail', $product->id) !!}">
                            <img src="{!! CommonHelper::getProductImage($product->id, true) !!}">
                        </a>
                    </div>
                    <div class="product-info">
                        <a href="{!!route('products.detail', $product->id) !!}">
                            <h3 class="product-name">{!! CommonHelper::formatProductDescription($product->description) !!}</h3>
                        </a>
                        <a class="product-price" data-alcohol="{!! CommonHelper::formatProductDescription($product->description) !!}" data-price="{{$product->price}}" data-name="{{$product->name}}" data-id="{{$product->id}}">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($product->price)}}</a>
                    </div>
                    <a data-alcohol="{!! CommonHelper::formatProductDescription($product->description) !!}" data-price="{{$product->price}}" data-name="{{$product->name}}" data-id="{{$product->id}}" class="product-add-cart">
                        <span data-item-id="{{ $product->id }}" class="product_cart_count">{!! CommonHelper::getProductCartCount($product->id, false) !!}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@if (empty(!$recipeMapping))
    <div class="section-title">
        <span>Recipes</span>
    </div>
    <section class="post-listing post-type-blog">
        <div class="three-column-group">
            <div class="three-column-group-inner" id='event-content'>
                <!--
                Partial
                -->
                @include('customer.partials.brand-content-listing')
            </div>
        </div>
    </section>
@endif
@if (empty(!$bundleMapping))
    <div class="section-title">
        <span>Bundles</span>
    </div>
    <section class="post-listing post-type-blog">
        <div class="three-column-group">
            <div class="three-column-group-inner" id='event-content'>
                <!--
                Partial
                -->
                @include('customer.partials.brand-content-listing', [ 'recipeMapping' => $bundleMapping ] )
            </div>
        </div>
    </section>
@endif
@if (!empty($brandData->button_text))
    @php
        $target = "";
    @endphp
    @if($brandData->is_external == 1)
        @php
            $target = 'target="_blank"';
        @endphp
    @endif
    <div class="col-xs-12" style="text-align: right;">
        <a class="btn-large-primary" {{$target}}  href="{{ $brandData->button_url }}">{{$brandData->button_text}}</a>
    </div>
@endif
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


</script>
@endsection