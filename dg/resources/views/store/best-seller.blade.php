@extends('store.layouts.products')
@section('header')
My Products
@endsection
@section('content')
<section class="store-content-section store-product-section">
    
     <ul class="nav nav-tabs tab-level-2">
        <li class="active"><a data-toggle="tab" href="#tab-best-seller"><span>Platform Best Seller</span></a></li>
         <li><a data-toggle="tab" href="#my-best-seller"><span>My Best Seller</span></a></li>
     </ul>
    <div class="tab-content">
        <div id="tab-best-seller" class="tab-pane fade in active">
            <ul class="product-group" style="background: #fff">
                <li>
                    <ul style="display: block">
                        @if (empty($bestSellerProducts))
                            <li class='empty-bestseller'>
                                No Product found.
                            </li>
                        @endif
                            <?php
                            foreach ($bestSellerProducts as $products) {
                                $liClass = 'disabled';
                                $checked = '';
                                if (in_array($products->id, $storeProducts)) {
                                    $liClass = 'active';
                                    $checked = 'checked="checked"';
                                }
                            ?>
                            <li>
                                <span class="product-available">
                                    <input type="checkbox" {{$checked}} class="prod_check" for="prod_check_span_{{$products->id}}" disabled="">
                                    <span id="prod_check_span_{{$products->id}}" class="marker"></span>
                                </span>
                                <div class="product-image">
                                    <a href="{{route('store.products.detail',[$products->id ])}}">
                                        <img src="{{ CommonHelper::getProductImage($products->id, true)}}">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <a href="{{route('store.products.detail',[$products->id ])}}">
                                        <h3 class="product-name">{!! CommonHelper::formatProductDescription($products->description) !!}</h3>                                    
                                        <p class="product-quantity">{{$products->name}}</p>
                                    </a>
                                    <a class="product-price">{{config('appConstants.currency_sign')}}.{{$products->store_price}}</a>                            
                                </div>                            
                                <a class="product-description-link" href="{{route('store.products.detail',[$products->id ])}}"></a>                    
                            </li>
                         <?php } ?>
                    </li>
                </ul>
            </ul>
        </div>
        <div id="my-best-seller" class="tab-pane fade in">
            <ul class="product-group" style="background: #fff">
                <li>
                    <ul style="display: block">
                        @if (empty($storeBestSellerProducts))
                            <li class='empty-bestseller'>
                                No Product found.
                            </li>
                        @endif
                        <?php
                            foreach ($storeBestSellerProducts as $products) {
                                $liClass = 'disabled';
                                $checked = '';
                                if (in_array($products->id, $storeProducts)) {
                                    $liClass = 'active';
                                    $checked = 'checked="checked"';
                                }
                            ?>
                            <li> 
                                <span class="product-available">
                                    <input type="checkbox" {{$checked}} class="prod_check" for="prod_check_span_{{$products->id}}" disabled="">
                                    <span id="prod_check_span_{{$products->id}}" class="marker"></span>
                                </span>
                                <div class="product-image">                                   
                                    <a href="{{route('store.products.detail',[$products->id ])}}">
                                        <img src="{{ CommonHelper::getProductImage($products->id, true)}}">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <a href="{{route('store.products.detail',[$products->id ])}}">
                                        <h3 class="product-name">{!! CommonHelper::formatProductDescription($products->description) !!}</h3>
                                        <p class="product-quantity">{{$products->name}}</p>
                                    </a>
                                    <a class="product-price">{{config('appConstants.currency_sign')}}.{{$products->store_price}}</a>       
                                </div>
                                <a class="product-description-link" href="{{route('store.products.detail',[$products->id ])}}"></a>
                            </li>
                         <?php } ?>
                    </li>
                </ul>
            </ul>
        </div>
    </div>
</section>
@endsection