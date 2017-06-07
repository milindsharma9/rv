@section('title', 'Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', ' All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@section('title')
Alchemy - Cart
@endsection
@extends('customer.layouts.customer')
@section('content')
<section class="customer-content-section customer-basket-order-section">
    <div class="container">
        <div class="row">
    <div class="order-header right">
        <h3 class="title">Your Basket
            <a class="btn-red" href="{{ url()->previous() }}">&lt; Back</a>
        </h3>
    </div>
    <ul class="cart-products">
        @foreach($cartViewData as $cartItem)
            @if(isset($cartItem['bundleId']))
                <li>
                    <div class="col-left">
                        <div class="product-name">{{$cartItem['bundleName']}}</div>
                        <!--<div class="product-desc">4 x 330ml (%Alcohol)</div>-->
                        <div class="product-price">{!! Config::get('appConstants.currency_sign') !!}<span id='bundleDefaultPrice_{{$cartItem['rowid']}}'>{{CommonHelper::formatPrice($cartItem['bundleDefaultTotalPrice'])}}</span></div>
                    </div>
                    <div class="col-right">
                        <div class="product-quantity">
                            <span class="product-modify">
                                <button class="prod-remove bundle-cart-remove" data-id='{{$cartItem['rowid']}}'></button>
                                <input type="text" class="product-quan" id='bundleQuantity_{{$cartItem['rowid']}}' value="{{$cartItem['bundleQty']}}" readonly>
                                <button class="prod-add bundle-cart-add" data-id='{{$cartItem['rowid']}}'></button>
                            </span>
                            <span class="single-total">Total <span class="price">{!! Config::get('appConstants.currency_sign') !!} <span id='bundleTotalPrice_{{$cartItem['rowid']}}'>{{CommonHelper::formatPrice($cartItem['bundleTotalPrice'])}}</span></span></span>
                        </div>
                    </div>
                    <div id="bundle_product_count_div_{{$cartItem['rowid']}}">
                        <ul class="bucket-product">
                            @foreach($cartItem['bundleProducts'] as $bundleProducts)
                                <li>
                                        <div class="col-left">
                                            <div class="product-name">{{$bundleProducts['options']['0']['alcohol_content']}}</div>
                                            <div class="product-desc">{{$bundleProducts['name']}}</div>
                                            <div class="product-price">{!! Config::get('appConstants.currency_sign') !!}<span id=''>{{CommonHelper::formatPrice($bundleProducts['price'])}}</span></div>
                                        </div>
                                        <div class="col-right">
                                            <div class="product-quantity">
                                                <span class="product-modify">
                                                    <input type="text" class="product-quan" id='bundle_product_count_{{$cartItem['rowid']}}_{{$bundleProducts['id']}}' value="{{$bundleProducts['qty']}}" readonly>
                                                        <input type="hidden" class="product-quan-default"
                                                           data-id='{{$cartItem['rowid']}}_{{$bundleProducts['id']}}'
                                                           value="{{$bundleProducts['bundleDefaultProductQuantity']}}" >
                                                </span>
                                            </div>
                                        </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <a href="#" class="remove-product glyphicon glyphicon-remove" data-id='{{$cartItem['rowid']}}'></a>
                </li>
            @else
                <li>
                    <div class="col-left">
                        <div class="product-name">{{$cartItem['options']['0']['alcohol_content']}}</div>
                        <div class="product-desc">{{$cartItem['name']}}</div>
                        <div class="product-price">{!! Config::get('appConstants.currency_sign') !!}<span id='productDefaultPrice_{{$cartItem['rowid']}}'>{{CommonHelper::formatPrice($cartItem['price'])}}</span></div>
                    </div>
                    <div class="col-right">
                        <div class="product-quantity">
                            <span class="product-modify">
                                <button class="prod-remove product-cart-remove" data-id='{{$cartItem['rowid']}}'></button>
                                <input type="text" readonly class="product-quan" id='productQuantity_{{$cartItem['rowid']}}' value="{{$cartItem['qty']}}">
                                <button class="prod-add product-cart-add" data-id='{{$cartItem['rowid']}}'></button>
                            </span>
                            <span class="single-total">Total <span class="price">{!! Config::get('appConstants.currency_sign') !!}<span id='productTotalPrice_{{$cartItem['rowid']}}'>{{CommonHelper::formatPrice($cartItem['subtotal'])}}</span></span></span>
                        </div>
                    </div>
                    <a href="#" class="remove-product glyphicon glyphicon-remove" data-id='{{$cartItem['rowid']}}'></a>
                </li>
            @endif
        @endforeach
        @if(!empty($cartViewData))
            <li class="drive-charge" id='driver_charges_li'>
                @include('customer.partials.delivery_charges')
            </li>
        @endif
        @if(!empty($appliedCoupon))
            <li class="promo-applied">
                Promocode {{$appliedCoupon['coupon_code']}} <span class="promo-deduction">{{CommonHelper::getDiscountSymbol($appliedCoupon['discount_type'], $appliedCoupon['discount_amount'])}}</span>
            </li>
        @endif
    </ul>
    @if(!empty($cartViewData))
        <div class="grand-total">Total <span>{!! Config::get('appConstants.currency_sign') !!}<span id='cart_grand_total'>{{CommonHelper::formatPrice($cartTotal)}}</span></span></div>
        @if((CommonHelper::checkForSiteTimings()))
        <div class="store-close">Sorry ! The Store is closed.</div>
         @else         
        <div class="stickyfooter">
            <!--<button>Place Order</button>-->
            {{ link_to_route('customer.checkout', 'Continue')}}
        </div>
         @endif
    @else
        <div class="cart-empty">No Item in cart</div>
    @endif
<input type="hidden" name="_token" value="{!! csrf_token() !!}" />
    </div>
    </div>
</section>
@endsection
@section('javascript')
<script>
    var cartAddUrl              = "{!! route('customer.cart.add')!!}";
    var cartUpdateUrl           = "{!! route('customer.cart.update')!!}";
    var cartRemoveUrl           = "{!! route('customer.cart.remove')!!}";
    var getBundleDetailUrl      = "{!! route('customer.getBundleDetail', 1)!!}";
    getBundleDetailUrl          = getBundleDetailUrl.slice(0, -2);
    var checkCustomerCartStatusUrl             = "{!! route('customer.cart.status.check')!!}";
//    var cartSetDeliveryPostcodeUrl             = "{!! route('customer.delivery.postcode.set')!!}";
//    var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
</script>
<!--<script src="{{ url('alchemy/js') }}/product_cart.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>-->
@endsection