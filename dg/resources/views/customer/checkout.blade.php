@section('title', 'Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@section('title')
Alchemy - Checkout
@endsection
@extends('customer.layouts.customer')
@section('content')
<section class="customer-content-section customer-address-confirm-section">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
        </ul>
    </div>
    @endif
    @if(!empty($cartContent))
    <div class="container">
        <div class="order-header right">
            <h3 class="title">Confirm your order<a href="{{ url()->previous() }}" class="btn-red">&lt; Back</a></h3>
        </div>
        @if((Auth::user()))
        <div class="promo-code-wrap">
            <div id='coupon_status' class="alert alert-danger"></div>
            <div class="form-group">
                <label>Enter Promocode :</label>
                <input type="text" id="apply_me" placeholder="xxxxxx">
                <input type="submit" id="apply_me_button" value="Apply"/>
            </div>
        </div>
        @endif
        <ul class="cart-products">
            <div id="cart-items">
                @include('customer.partials.checkout-product-data')
            </div>
            @if(!empty($appliedCoupon))
            <li class="promo-applied">
                Promocode {{$appliedCoupon['coupon_code']}} <span class="promo-deduction">{{CommonHelper::getDiscountSymbol($appliedCoupon['discount_type'], $appliedCoupon['discount_amount'])}}</span>
            </li>
            @endif
        </ul>
        <!--<div class="grand-total">Total <span>{{$currencySymbol}}<span id='cart_total_span'>{{$cartTotal}}</span></span></div>-->
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
    </div>
    <div class="payment-address">
        @if((Auth::user()))
        <div class="container">
            <h3 class="title">delivery and payment details</h3>
            @if($hasAddress)
            <div class="col-xs-12 col-sm-6 address-wrap">
                <div class="inner-wrap">
                    <span class="icon-marker"></span>
                    <span class="adder-cont">{{$address}}</span>
                    <a href="{{route('customer.address')}}">Change Address</a>
                </div>
            </div>
            @else
            <div class="col-xs-12 col-sm-6 address-wrap">
                <div class="inner-wrap">
                    <span class="icon-marker"></span>
                    <span class="adder-cont unavailable">No Address entered</span>
                    <a href="{{route('customer.address')}}">Enter Address</a>
                </div>
            </div>
            @endif
            @if($hasCard)
            <div class="col-xs-12 col-sm-6 card-wrap">
                <div class="inner-wrap">
                    <span class="icon-payment"></span>
                    <span class="card-cont">{{$card}}</span>
                    <a href="{{route('customer.payment')}}">Change Payment</a>
                </div>
            </div>
            @else
            <div class="col-xs-12 col-sm-6 card-wrap">
                <div class="inner-wrap">
                    <span class="icon-payment"></span>
                    <span class="card-cont unavailable">No Payment Method</span>
                    <a href="{{route('customer.payment')}}">Add Card</a>
                </div>
            </div>
            @endif
        </div>
        @else
        <div class="row"><div class="errorLogin">Please Login To Proceed.</div></div>
        @endif
    </div>
    <div class="address-map">
        <!--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3506.0680396128696!2d77.08286961516131!3d28.507600582467255!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1944b3f22513%3A0x339f61acaa678349!2sKellton+Tech!5e0!3m2!1sen!2sin!4v1463737945313" frameborder="0" style="border:0" allowfullscreen></iframe>-->
        @include('customer.partials.map')
    </div>
    @if((Auth::user()))
    @if (isset($availableRetailer['data']) && empty($availableRetailer['data']))
    <div class="error-checkout">
        <img src="{{ url('alchemy/images') }}/broken-cycle.svg" />
        <div class="alert alert-danger">
            {{trans('messages.common_error')}}
        </div>
    </div>
    @else
    <div class="stickyfooter">
        @if($hasAddress && $hasCard)
        @if($pinServiceability)
        @if($postCodeMatch)
        @if((CommonHelper::checkForSiteTimings()))
        <div class="store-close">Sorry ! The Store is closed.</div>
        @else
        <button class="confirm-order">Confirm Order</button>
        @endif
        @else
        <button>Change Pincode</button>
        @endif
        @else
        <button>Change Pincode</button>
        @endif
        @else
        @if(!$hasAddress)
        <button>Please Enter Address</button>
        @elseif(!$hasCard)
        <button>Enter Payment Details</button>
        @endif
        @endif
    </div>
    @endif
    @else
    <div class="stickyfooter">
        <!--<button>Login to Proceed</button>-->
        <button data-toggle="modal" data-target="#login-register" id="login">Login to Proceed</button>
    </div>
    @endif
    <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
    @else
    <div class="cart-empty">Please add item in your cart.</div>
    @endif
    <div id="askId" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="login-section">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <img src="<?php echo e(url('alchemy/images')); ?>/id-card.svg">
                        <h3>Your ID will be asked for, do you have it?</h3>
                        <p>It has to be the ID on the ordering card.</p>
                    </div>
                    <div class="modal-footer">
                        {!! Form::open(array('method' => 'POST',  'route' => array('customer.placeorder'))) !!}
                        {!! Form::submit(trans('Pay Now')) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="shopInfoModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="login-section">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <img src="{{ url('alchemy/images') }}/store-icon.png">
                        <h3 id="available_store_name">Store's Name</h3>
                        <ul>
                            <li id="available_store_address">Address</li>
                            <li id="available_store_postcode">Postcode</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.promo-code')
    @include('customer.partials.change-address-checkout')
</section>
@endsection
@section('javascript')
<script>
    var placeOrderUrl               = "{!! route('customer.placeorder')!!}";
    var cartUrl                     = "{!! route('customer.cart')!!}";
    var applypromocode              = "{!! route('customer.applypromocode')!!}";
    var cartSetDeliveryPostcodeUrl  = "{!! route('customer.delivery.postcode.set')!!}";
    var validPostCodeUrl            = "{!! route('customer.postcode.get'); !!}";
    var postCodeMatch               = "{!! $postCodeMatch !!}";
    var availableCart               = '{!! json_encode($availableRetailer, true)!!}';
    var cartAddUrl                  = "{!! route('customer.cart.add')!!}";
    var cartUpdateUrl               = "{!! route('customer.cart.update')!!}";
    var cartRemoveUrl               = "{!! route('customer.cart.remove')!!}";
    var getBundleDetailUrl          = "{!! route('customer.getBundleDetail', 1)!!}";
    getBundleDetailUrl              = getBundleDetailUrl.slice(0, -2);
    var checkCustomerCartStatusUrl  = "{!! route('customer.cart.status.check')!!}";
</script>
<!--<script src="{{ url('alchemy/js') }}/product_cart.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>-->
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>

<script type="text/javascript">
    $(function () {
        $("#submit-edit-address-form").click(function(){
            $('#form-customer-delivery-address').validate({
            focusInvalid: true,
            debug: true,
            rules: {
                address: 'required',
                city: 'required',
                state: 'required',
                pin: 'required',
            },
            submitHandler: function (form) {
                form.submit();
            }

            });
        });
    });
    $(document).ready(function () {
        var cartData = jQuery.parseJSON(availableCart);
        if (!postCodeMatch && cartData.data) {
            $('#change-address-checkout').modal();
        }
    });
    function saveOrder() {
        $.ajax({
            url: placeOrderUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                _token: $('input[name=_token]').val()
            },
            success: function (result) {
                if (result.status) {
                    //$('#cart_header').text(result.quantity);
                    var orderNumber = result.data.orderNumber;
                    alert('Order Placed Successfully.| Order Number : ' + orderNumber);
                } else {
                    alert('Error while placing order Product');
                }
                window.location = cartUrl;
                //$('#cart-modal').modal('hide');
            },
        });
    }

    $(document).on('click', '.confirm-order', function () {
        $('#askId').modal();
    });
    $(document).on('click', '.shop-details', function () {
        var storeName = $(this).data("store_name");
        var storeAddress = $(this).data("store_address");
        var storePostcode = $(this).data("store_postcode");
        $('#shopInfoModal').modal();
        $('#available_store_name').html(storeName);
        $('#available_store_address').html(storeAddress);
        $('#available_store_postcode').html(storePostcode);
    });

    $(document).on('click', '.info-icon', function () {
        if ($(window).width() > 767) {
            var storeName = $(this).data("store_name");
            var storeAddress = $(this).data("store_address");
            var storePostcode = $(this).data("store_postcode");
            $('#shopInfoModal').modal();
            $('#available_store_name').html(storeName);
            $('#available_store_address').html(storeAddress);
            $('#available_store_postcode').html(storePostcode);
        }
    });

    $(document).on('click', '#apply_me_button', function () {
        var promo_code = jQuery('#apply_me').val();
        if (!promo_code) {
            $('#coupon_status').addClass('alert-danger');
            $('#coupon_status').html('Please enter coupon code.');
            return false;
        }
        $.ajax({
            url: applypromocode,
            method: 'POST',
            data: {
                promo_code: promo_code
            },
            success: function (result) {
                if (result.status) {
                    $('#cart_grand_total').html(result.data.new_amount);
                    $('#coupon_status').removeClass('alert-danger');
                    $('#coupon_status').addClass('alert-success');
                    $('#coupon_status').html(promo_code + " " + result.message);
                } else {
                    $('#promoCode').modal({});
                    $('#promo_error_p').html(result.message);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
    });
</script>
@endsection