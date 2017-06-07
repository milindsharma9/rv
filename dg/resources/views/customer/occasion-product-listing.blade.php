@section('title')
Alchemy - Occasions Details
@endsection
@extends('customer.layouts.customer')
@section('content')
<section class="customer-content-section customer-product-description-section customer-create-listing-section">
    @if(!empty($parentDetails))
    @php 
        $imageBanner = $parentDetails[0]->image_banner;
        $imageLogo   = $parentDetails[0]->image_logo;
    @endphp
    @if(isset($parentDetails[0]->cimage_banner) && ($parentDetails[0]->cimage_banner != ''))
     @php 
        $imageBanner = $parentDetails[0]->cimage_banner;
    @endphp
    @endif
    @if(isset($parentDetails[0]->cimage_logo) && ($parentDetails[0]->cimage_logo != ''))
     @php 
        $imageLogo = $parentDetails[0]->cimage_logo;
    @endphp
    @endif
        <div class="feature-banner hidden-xs">
            <img src="{{ url('uploads/occasions') }}/{{$imageBanner}}" class="banner-img">
            <div class="caption">
                <img src="{{ url('uploads/occasions') }}/{{$imageLogo}}">
                <h3 class="event-name">
                    @if(!empty($parentDetails[0]->cfloating_text))
                        {{$parentDetails[0]->cfloating_text}}
                    @else
                        {{$parentDetails[0]->cname}}
                    @endif
                </h3>
            </div>
        </div>
        <div class="order-header hidden-xs">
            <a href="{{ url()->previous() }}" class="btn-red">< Back</a>
            <div class="tagline hidden-xs">
                @if(!empty($parentDetails[0]->csub_text))
                    {{$parentDetails[0]->csub_text}}
                @endif
            </div>
        </div>
    @endif
    <div class="bundel-product-wrap">
        <div class="container">
        <div class="row">
            <div class="order-header visible-xs">
                <a href="{{ url()->previous() }}" class="btn-red">< Back</a>
                <div class="tagline hidden-xs">
                    @if(!empty($parentDetails[0]->csub_text))
                        {{$parentDetails[0]->csub_text}}
                    @endif
                </div>
            </div>
            @if(!empty($parentDetails))
            <div class="feature-banner visible-xs">
                <img src="{{ url('uploads/occasions') }}/{{$imageBanner}}" class="banner-img">
                <div class="caption">
                    <!--<img src="{{ url('alchemy/images') }}/gifting-logo.svg">-->
                    <img src="{{ url('uploads/occasions') }}/{{$imageLogo}}">
                    <h3 class="event-name">
                        @if(!empty($parentDetails[0]->cfloating_text))
                            {{$parentDetails[0]->cfloating_text}}
                        @else
                            {{$parentDetails[0]->cname}}
                        @endif
                    </h3>
                </div>
            </div>
            @endif
        </div>
    </div>
    </div>
    <div class="event-listing-wrap">
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
                                        <img src="{!! CommonHelper::getBundleImage($image, true) !!}">
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
        @php
            $hasProducts = $products['hasProducts'];
        @endphp
        @if($products['hasProducts'])
            <?php
                unset($products['hasProducts']);
            ?>
            @foreach($products AS $catName => $aProducts)
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
        @if(!$hasProducts && empty($bundleMapping))
            <div class="sub-category-list-wrap">
                <div class="container">
                    <div class="product-list-group">
                        <ul>
                            <li class="empty-products-li">
                                No Record Found
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
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
    var checkCustomerCartStatusUrl             = "{!! route('customer.cart.status.check')!!}";
//    var cartSetDeliveryPostcodeUrl             = "{!! route('customer.delivery.postcode.set')!!}";
//    var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
</script>
<!--<script src="{{ url('alchemy/js') }}/product_cart.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>-->
@endsection
