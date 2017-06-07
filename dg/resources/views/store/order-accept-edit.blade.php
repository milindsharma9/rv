@extends('store.layouts.products')
@section('header')
My Orders
@endsection
@section('content')
<section class="store-content-section order-type-template sales-order-listing-section">
    @if(isset($orderNumber) && isset($orderItem))	
    <div class="container">
        <div class="row">
            <div class="order-header">
                @if(isset($orderNumber))
                <h3 class="title"><a href="{{ url()->previous() }}" class="btn-red">< Back</a> <span>Order #{!!$orderNumber!!}</span></h3>
                @endif
            </div>
            <div class="product-list-group">
                <ul>
                    @foreach($orderItem AS $key => $value)
                    <li>
                        <div class="product-image">
                            @if(isset($value->fk_product_id))
                                <img src="{!! CommonHelper::getProductImage($value->fk_product_id, true) !!}">
                            @else
                                <img src="{!! CommonHelper::getProductImage($value['fk_product_id'], true) !!}">
                            @endif
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">
                                @if(isset($value->description))
                                {!!CommonHelper::formatProductDescription($value->description)!!}
                                @else {!! CommonHelper::formatProductDescription($value['description'])!!}
                                @endif
                            </h3>
                            <p class="product-quantity">
                                @if(isset($value->name))
                                {!!$value->name!!}
                                @else {!! $value['name']!!}
                                @endif
                            </p>
                            <p class="product-price">{{Config::get('appConstants.currency_sign')}}
                                @if(isset($value->store_price)){!!CommonHelper::formatPrice($value->store_price)!!}
                                @else {!! CommonHelper::formatPrice($value['store_price'])!!}
                                @endif</p>
                        </div>
                        <?php $prodQuantity = 0; ?>
                        @if(isset($value->quantity))
                        <?php $quantity = $value->quantity; ?>
                        @else 
                        <?php
                        $quantity = $value['quantity'];
                        if (isset($value['newQty']) && !empty($value['newQty']))
                            $prodQuantity = $value['newQty'];
                        ?>
                        @endif
                        <div class="product-modify">

                            <span class="product-quan">
                                <span class="product-booked"> {!! $prodQuantity !!}
                                </span>/<span class="product-available">{!!$quantity!!}</span>
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                <div class="total-price-wrap">
                    <span>Total</span>
                    <span class="total-price">{!! Config::get('appConstants.currency_sign') !!}{!!CommonHelper::formatPrice($totalPrice) !!}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="stickyfooter action-buttons btn-count-2">
        {{ link_to_route('store.orderData', 'Edit')}}
        {{ link_to_route('store.orderComplete', 'Accept', '', array('class' => 'btn-order-accept'))}}
    </div>
    @endif
    <div id="error-popup" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ url('/alchemy/images') }}/broken-cycle.svg">
                    <h2>Oh oh!</h2>
                    <p>There was a problem during the process. Check your connection and try it again.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal">Try Again</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection