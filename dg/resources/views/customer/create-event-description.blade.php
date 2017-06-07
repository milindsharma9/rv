@section('title')
Alchemy - Bundle detail
@endsection
@extends('customer.layouts.customer')
@section('content')
<section class="customer-content-section customer-product-description-section customer-bundle-decription-section">
    @if(isset($bundleDetails) && count($bundleDetails) > 0)
    <div class="order-header">
        <a href="{{ url()->previous() }}" class="btn-red">< Back</a>
        @if(isset($bundleDetails[0]->serves))
        <span class="btn-quan-serve">{!!$bundleDetails[0]->serves !!}</span>
        @endif
    </div>
        <div class="product-image">
            @if(isset($bundleDetails[0]->bundleImage))
            <div class="product-image-inner">
                <img src="{!! CommonHelper::getBundleImage($bundleDetails[0]->bundleImage) !!}">
            </div>
            @endif
        </div>
    <div class="container">
        <div class="row">
            @if(isset($bundleDetails[0]))
            <div class="tab-content">
                <h2 class="product-title hidden-xs">{!! $bundleDetails[0]->bundleName!!}</h2>
                <div id="prod-summary" class="tab-pane fade in active">
                    <h3 class="title visible-xs">{!! $bundleDetails[0]->bundleName!!}</h3>
                    <h3 class="title hidden-xs">Product</h3>
                    <a class="product-price bundle-price" data-name="{{ $bundleDetails[0]->bundleName }}" data-id="{{ $bundleDetails[0]->fk_bundle_id }}">{{Config::get('appConstants.currency_sign')}}{!! CommonHelper::formatPrice($total) !!}</a>
                    <div class="product-info">
                        {!! $bundleDetails[0]->bundleDescription!!}
                    </div>
                </div>
                <div id="your-need" class="tab-pane fade in active hidden-xs">
                    <h3 class="title">What do you need ?</h3>
                    <div class="product-list-group">
                        <ul>
                            @foreach($bundleDetails AS $key => $value)
                            <li>
                                <div class="product-img">
                                    <img src="{!! CommonHelper::getProductImage($value->productsBarcode, true) !!}">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">{!! CommonHelper::formatProductDescription($value->productsDescription) !!}</h3>
                                    <p class="product-quantity">{!! $value->productsName!!}</p>
                                    <p class="product-price">{{Config::get('appConstants.currency_sign')}}{!! CommonHelper::formatPrice($value->price)!!}</p>
                                </div>
                                <div class="product-modify">
                                    <span class="product-quan">
                                        <span class="product-booked">{!! $value->product_quantity !!}</span>
                                    </span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="stickyfooter hidden-xs">
                    @if(isset($bundleDetails[0]))
                        <button data-name="{{ $bundleDetails[0]->bundleName }}" data-id="{{ $bundleDetails[0]->fk_bundle_id }}" class="product-add-cart-bundle">
                            Add to Basket
                            <span data-item-id-bundle="{{ $bundleDetails[0]->fk_bundle_id }}" class="product_cart_count">{!! CommonHelper::getProductCartCount($bundleDetails[0]->fk_bundle_id, true) !!}</span>
                        </button>
                    @endif
                </div>
            </div>
            @endif
            <div class="your-need visible-xs">
                <h3 class="title">What do you need ?</h3>
                <div class="product-list-group">
                    <ul>
                        @foreach($bundleDetails AS $key => $value)
                        <li>
                            <div class="product-img">
                                <img src="{!! CommonHelper::getProductImage($value->productsBarcode, true) !!}">
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">{!! CommonHelper::formatProductDescription($value->productsDescription) !!}</h3>
                                <p class="product-quantity">{!! $value->productsName!!}</p>
                                <p class="product-price"> {{Config::get('appConstants.currency_sign')}}{!! CommonHelper::formatPrice($value->price)!!}</p>
                            </div>
                            <div class="product-modify">
                                <span class="product-quan">
                                    <span class="product-booked">{!! $value->product_quantity !!}</span>
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            </div>
    </div>
            <!--<div class="bought-frequency">
                <h3 class="title">Most frequently bought for</h3>
                <ul>
                    <li><img src="{{ url('alchemy/images') }}/casual-get-together-banner.png" class="img-banner">
                        <div class="count"><span>48%</span>Casual get together</div>
                    </li>
                    <li><img src="{{ url('alchemy/images') }}/party-time-banner.png" class="img-banner">
                        <div class="count"><span>35%</span>Party time</div>
                    </li>
                    <li><img src="{{ url('alchemy/images') }}/office-trolley-banner.png" class="img-banner">
                        <div class="count"><span>28%</span>Office trolley</div>
                    </li>
                </ul>
            </div>-->
            @include('customer.partials.related-occasion')
            @include('customer.partials.related-products')
    <div class="stickyfooter visible-xs">
        @if(isset($bundleDetails[0]))
            <button data-name="{{ $bundleDetails[0]->bundleName }}" data-id="{{ $bundleDetails[0]->fk_bundle_id }}" class="product-add-cart-bundle">
                Add to Basket
                <span data-item-id-bundle="{{ $bundleDetails[0]->fk_bundle_id }}" class="product_cart_count">{!! CommonHelper::getProductCartCount($bundleDetails[0]->fk_bundle_id, true) !!}</span>
            </button>
        @endif
    </div>
    <div class="explore-sub-occasion sub-occasion-home">
        <div class="explore-sub-occasion-wrap" id='explore-sub-occasion'>

        </div>
    </div>
</section>
<input type="hidden" name="_token" value="{!! csrf_token() !!}" />
@include('customer.partials.cart-modal')
@endsection
@section('javascript')
<script>
    var cartAddUrl              = "{!! route('customer.cart.add')!!}";
    var cartUpdateUrl           = "{!! route('customer.cart.update')!!}";
    var getBundleDetailUrl      = "{!! route('customer.getBundleDetail', 1)!!}";
    getBundleDetailUrl          = getBundleDetailUrl.slice(0, -2);
    var moodUrl                 = "{!! route('search.mood')!!}";
    var loadingImgUrl           = "{!! url('alchemy/images/loadingstock.gif')!!}";
    var occasionUrl             = "{!! route('search.occasion')!!}";
    var getSubOccasionUrl       = "{!! route('get.occasion', 1)!!}";
    getSubOccasionUrl           = getSubOccasionUrl.slice(0, -2);
    var checkCustomerCartStatusUrl             = "{!! route('customer.cart.status.check')!!}";
//    var cartSetDeliveryPostcodeUrl             = "{!! route('customer.delivery.postcode.set')!!}";
//    var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
</script>
<!--<script src="{{ url('alchemy/js') }}/product_cart.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>-->
@endsection
