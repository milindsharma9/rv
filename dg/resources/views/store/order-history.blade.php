@extends('store.layouts.products')
@section('content')
<section class="store-content-section order-type-template sales-order-history-section">
    @if(isset($history))
    <div class="container">
        <div class="row">
            @if(isset($history[0]->order_id))
            <div class="order-header">
                <h3 class="title"><a href="{{ url()->previous() }}" class="btn-red">< Back</a> <span>Order #{!!$history[0]->order_id!!} </span></h3>
            </div>
            @endif
            <div class="product-list-group">
                <ul>
                    <?php $totalPrice = 0; ?>
                    @foreach($history AS $key => $value)
                    <li>
                        <div class="product-image">
                            <img src="{!! CommonHelper::getProductImage($value->id, true) !!}">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">{!! CommonHelper::formatProductDescription($value->description) !!}</h3>
                            <p class="product-quantity">{!! $value->name !!}</p>
                            <p class="product-price">{{Config::get('appConstants.currency_sign')}}{!! CommonHelper::formatPrice($value->price) !!}</p>
                        </div>
                        <div class="product-modify">

                            <span class="product-quan">
                                <span class="product-booked">{!! $value->prodQuantity !!}</span>
                            </span>

                        </div>
                        <?php $totalPrice+= $value->totalPrice; ?>
                    </li>
                    @endforeach
                </ul>
                <div class="total-price-wrap">
                    <span>Total</span>
                    <span class="total-price">{!! Config::get('appConstants.currency_sign') !!}{!! CommonHelper::formatPrice($totalPrice) !!}</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</section>
@endsection