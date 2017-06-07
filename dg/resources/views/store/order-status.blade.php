@section('title')
Alchemy - TrackOrder
@endsection
@extends('store.layouts.products')
@section('header')
My Orders
@endsection
@section('content')
<section class="customer-content-section customer-order-status store-single-layout">
    <div class="order-info">
        <div class="container">
            <div class="row">
                <h3 class="title">For order #{{$orderNumber}}</h3>
                <ul>
                    <?php $total = 0; ?>
                    @foreach($orderData as $products)
                    <li>
                        <div class="product-info">
                            <div class="product-name">{{$products['quantity']}}  {{ CommonHelper::formatProductDescription($products['description'])}}</div>
                            <div class="product-quantity">{{$products['name']}}</div>
                        </div>
                        <div class="product-price">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($products['totalPrice'])}}
                            <span>( {{$products['quantity']}} * {{CommonHelper::formatPrice($products['store_price'])}} )</span></div>
                    <?php $total += $products['totalPrice']; ?>
                    </li>
                    @endforeach
                </ul>
                <div class="total">
                    TOTAL <span>{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($total)}}</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection