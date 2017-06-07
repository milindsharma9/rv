@section('title')
Alchemy - TrackOrder
@endsection
@extends('customer.layouts.customer')
@section('content')
<section class="customer-content-section customer-order-status">
<div class="user-profile-info">
    <a href="{{ route('customer.dashboard') }}" class="btn-red visible-xs">&lt; Back</a>
    @if((Auth::user()))
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</h1>
                <p>{{Auth::user()->email}}</p>
            </div>
        </div>
    </div>
    @endif
</div>
<div class="order-status">
    <h3 class="section-title visible-xs"><span>Status</span></h3>
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h3 class="section-title hidden-xs"><span>Status</span> <a href="{{ route('customer.dashboard') }}" class="btn-red hidden-xs">&lt; Back</a></h3>
                                <?php
                                    $orderStatusId  = $orderData['orderStatusId'];
                                    $isCancelled    = false;
                                    $refundString   = '';
                                    switch ($orderStatusId) {
                                        case "1":
                                            $statuWidth = 10;
                                            break;
                                        
                                        case "2":
                                            $statuWidth = 50;
                                            break;
                                        
                                        case "3":
                                            $statuWidth = 100;
                                            break;
                                        
                                        case "5":
                                            $isCancelled = true;
                                            break;
                                        
                                        case "6":
                                            $isCancelled = true;
                                            $refundString = trans('messages.order_status_change_refund_message');
                                            break;
                                        
                                        default:
                                            $statuWidth = 10;
                                    }
                                ?>
                                @if($isCancelled)
                                    <!-- Status Cancelled Order -->
                                    <div class="status-canceled-order">
                                        <img src="{{ url('alchemy/images/icons') }}/cancel-order-cross.png" alt="Order Canceled">
                                        <p>Your order was cancelled.<br>
                                        Contact our team so we can help you<br> 
                                        with more details.</p>
                                    </div>
                                @else
                                    <div class="status-order">
                                        <ul class="order-progress-point">
                                            <li><img src="{{ url('alchemy/images') }}/order-confirm.svg" alt="Order Confirm" width="46"><span>Order<br>confirmed</span></li>
                                            <li><img src="{{ url('alchemy/images/icons') }}/order-on-way.png" alt="Order on Way" width="46"><span>Order<br>on it’s way</span></li>
                                            <li><img src="{{ url('alchemy/images') }}/order-completed.svg" alt="Order Delivered" width="34"><span>Order<br>delivered</span></li>
                                        </ul>
                                        <div class="progress-bar">
                                                <span style="width:<?php echo $statuWidth; ?>%;"></span>
                                        </div>
                                    </div>
				@endif
			</div>
		</div>
	</div>
</div>
<div class="order-info">
    <h3 class="section-title visible-xs"><span>your order</span></h3>
	<div class="container">
		<div class="row">
        <h3 class="section-title hidden-xs"><span>your order</span></h3>
			<ul>
                            @foreach($orderData['products'] as $products)
				<li>
					<div class="product-info">
						<div class="product-name">{{$products['count']}} {{ CommonHelper::formatProductDescription($products['description'])}}</div>
						<div class="product-quantity">{{$products['name']}}</div>
					</div>
					<div class="product-price">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($products['price'])}}</div>
				</li>
                            @endforeach
                            @foreach($orderData['bundle'] as $bundle)
				<li>
					<div class="product-info">
						<div class="product-name">{{$bundle['name']}}</div>
                                                <div class="product-quantity">
                                                    @if(isset($bundle->serves))
                                                    {!!CommonHelper::formatProductDescription($bundle->serves) !!}
                                                    @endif
                                                </div>
					</div>
					<div class="product-price">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($bundle['price'])}}</div>
                                        <ul class="bucket-product">
                                            @foreach($bundle['product'] as $bundleProduct)
						<li>
							<div class="product-info">
								<div class="product-name">{{$bundleProduct['count']}} {{ CommonHelper::formatProductDescription($bundleProduct['description'])}}</div>
								<div class="product-quantity">{{$bundleProduct['name']}}</div>
							</div>
							<div class="product-price">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($bundleProduct['price'] * $bundleProduct['count'])}}</div>
						</li>
                                            @endforeach
					</ul>
				</li>
                            @endforeach
				<li class="drive-charge">
                                    <!--<div class="product-name">Driver Charge <span>{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($orderData['driverCharges'])}}</span></div>
                                    <div class="product-desc mid-night-charge">After midnight <span>+ £1.50</span></div>
                                    <div class="product-desc other-category-charge">After midnight <span>+ £1.50</span></div>-->
                                    @include('customer.partials.delivery_charges', ['charges' => $orderData['charges']])
				</li>
			</ul>
                        @if(!empty($orderData['coupon_data']))
                            <div class="promo-applied">
                                Promocode {{$orderData['coupon_data']['coupon']}} <span class="promo-deduction">{{CommonHelper::getDiscountSymbol($orderData['coupon_data']['type'], $orderData['coupon_data']['amount'])}}</span>
                            </div>
                        @endif
			<div class="total">
				TOTAL <span>{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($orderData['orderTotal'])}}</span>
                <span class="refund-info">{{$refundString}}</span>
			</div>
		</div>
	</div>
</div>
<div class="order-details">
    <h3 class="section-title visible-xs"><span>order details</span></h3>
	<div class="container">
		<div class="row">
            <h3 class="section-title hidden-xs"><span>order details</span></h3>
			<div class="order-id"><img src="{{ url('alchemy/images') }}/hashtag-icon.svg">{{$orderData['orderNumber']}}</div>
			<div class="order-address"><img src="{{ url('alchemy/images') }}/marker.svg">
                            {{$orderData['orderAddress']}}
                        </div>
                        @if(isset($cardDetails) &&  !empty($cardDetails))
                        <div class="order-payment"><img src="{{ url('alchemy/images') }}/pay-card.svg">{!! $cardDetails !!}</div>
                        @endif
		</div>
	</div>
</div>
<div class="need-help">
    <h3 class="section-title visible-xs"><span>Need help?</span></h3>
	<div class="container">
		<div class="row">
            <h3 class="section-title hidden-xs"><span>Need help?</span></h3>
			<a class="email-link" href="mailto:{!! config('appConstants.mailto'); !!}"><img src="{{ url('alchemy/images') }}/email-2.svg">Email us</a>
			<a class="phone-link" href="tel:{!! config('appConstants.tel'); !!}"><img src="{{ url('alchemy/images') }}/icons/menu-contact.png">Call us</a>
		</div>
	</div>
</div>
<!--<div class="stickyfooter action-buttons">
	<a href="{!! route('customer.products') !!}">continue shopping</a>
</div>-->
</section>
@endsection