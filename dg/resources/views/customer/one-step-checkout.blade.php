@section('title', 'Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@section('title')
Alchemy - Checkout
@endsection
@extends('customer.layouts.customer')
@section('content')
<section class="checkout-banner visible-xs">
    <img src="{{ url('alchemy/images') }}/checkout-banner.png"/>
</section>
@if(!empty($cartContent))
<div class="container">
    <div class="page-title">
        <h1>Checkout <a href="{{route('customer.dashboard')}}" class="btn-red">x Close</a></h1>
    </div>
</div>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
    </ul>
</div>
@endif
<section class="checkout-cart-product customer-order-status">
    <h3 class="section-title"><span>Step One: Confirm Order</span></h3>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <ul class="cart-products">
                    <div id="cart-items">
                        @include('customer.partials.checkout-product-data')
                    </div>
                </ul>
                @if(!empty($cartViewData))
                <div class="grand-total">Total <span>{{$currencySymbol}}<span id='cart_grand_total'>{{$cartTotal}}</span></span></div>
                @endif
                @if((CommonHelper::checkForSiteTimings()))
                <div class="store-close">Sorry ! The Store is closed.</div>
                @endif
            </div>
        </div>
    </div>
</section>
 @if((Auth::user()))
<section class="payment-promo-section">
    <h3 class="section-title"><span>Step Two: Confirm Payment Address</span></h3>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="card-wrap">
                    <span class="icon-payment"></span>
                    @if($hasCard)
                    @if($card['cardDetails'])
                    <span class="card-cont">{{$card['cardDetails']}}</span>
                    @endif
                    <a class="btn-red" href="{{route('customer.dashboard')}}#change-payment">Add Card</a>
                    @else
                    <span class="card-cont unavailable">No Payment Method</span>
                    <a class="btn-red" href="{{route('customer.payment')}}">Add Card</a>
                    @endif
                </div>
                <div class="promo-code-wrap">
                    <span class="icon-promo"></span>
                    <div class="form-group card-cont">
                        <input type="text" id="apply_me" placeholder="Enter Promocode">
                    </div>
                    <input type="submit" id="apply_me_button" value="Apply" class="btn-red"/>
                    <div id='coupon_status' class="alert alert-danger"></div>
                </div>
            </div>
        </div>
    </div>
    <h3 class="section-title"><span>Step Three: Confirm Delivery Address</span></h3>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="address-wrap">
                    <span class="icon-marker"></span>
                     @if($hasAddress)
                    <span class="adder-cont">{{$address}}</span>
                    <a class="btn-red" href="{{route('customer.address')}}">Change Address</a>
                    @else
                    <span class="adder-cont unavailable">No Address entered</span>
                    <a class="btn-red" href="{{route('customer.address')}}">Enter Address</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif
<section class="address-map">
    @include('customer.partials.map')
</section>
<section class="bottom-action-links">
    <div class="container">
@if((Auth::user()))
    @if (isset($availableRetailer['data']) && empty($availableRetailer['data']))
        <div class="error-checkout">
            <img src="{{ url('alchemy/images') }}/broken-cycle.svg" />
            <div class="alert alert-danger">
                {{trans('messages.common_error')}}
            </div>
        </div>
    @else
    <div>
        @if((CommonHelper::checkForSiteTimings()))
        <div class="store-close">Sorry ! The Store is closed.</div>
        @elseif($canOrder)
        {!! Form::open(array('method' => 'POST',  'route' => array('customer.placeorder'))) !!}
        {!! Form::submit(trans('Confirm Order')) !!}
        {!! Form::close() !!}
        @else
        {!! Form::open(array('method' => 'GET',  'route' => array($url))) !!}
        {!! Form::submit(trans($orderMessage)) !!}
        {!! Form::close() !!}
        @endif
    </div>
    @endif
@else
    <div >
        <button data-toggle="modal" data-target="#login-register" id="login">Login to Proceed</button>
    </div>
@endif
    <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
@else
    <div class="cart-empty">Please add item in your cart.</div>
@endif

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
    </div>
</section>
    @include('partials.promo-code')
    @include('customer.partials.change-address-checkout')
    <?php
        if (empty($mapAddress)) {
            $mapAddress = '332 Ladbroke Grove, London, W10 5AS';
            $deliveryPostcode = App\Http\Helper\CommonHelper::getUserCartDeliveryPostcode();
            if (!empty($deliveryPostcode)) {
                $mapAddress = $deliveryPostcode;
            }
        }
    ?>
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
    var loggedinUser                = "{!! (Auth::user()? '1': '0')!!}";
    var cartContent                 = "{!! (!empty($cartContent) ? '1' : '0' ) !!}"
</script>
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
        if (!postCodeMatch && cartData.data && loggedinUser == '1') {
            $('#change-address-checkout').modal();
        }
        if(cartContent == 1){
            //track intercom.io initiate checkout event.
            var metaData = {};
            trackIntercomEvent('initiate-checkout', metaData);
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
                    $('#cart-items').html(result.html_content);
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
<script type="text/javascript">
    var map_address = '<?php echo $mapAddress ?>';
    var map_marker_image = "{{ url('alchemy/images/map_marker.png') }}";
</script>
<script type="text/javascript" src="{{ url('alchemy/js') }}/map.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src='https://maps.googleapis.com/maps/api/js?key=AIzaSyCBGx3A0dv7cs5isfLJNXeINT9UxeVcKQQ&callback=initMap'></script>
@endsection