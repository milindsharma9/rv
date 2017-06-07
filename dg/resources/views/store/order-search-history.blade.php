@extends('store.layouts.products')
@section('header')
My Orders
@endsection

@section('content')
<?php $historyCount = 0; ?>
@if(isset($history))
<?php $historyCount = count($history); ?>
@endif
<section class="store-content-section order-type-template store-order-search-history-section">
    {!! Form::open() !!}
    <div class="container">
        <div class="row">
            <h3 class="title hidden-xs">Search New Orders</h3>
            <ul class="nav nav-tabs tab-level-1">
                <li class="active"><a data-toggle="tab" href="#search-wrap"><span>Search new orders</span></a></li>
                <li><a data-toggle="tab" href="#order-history-wrap"><span>My order history ({!! $historyCount !!})</span></a></li>
            </ul>
            <div class="tab-content tab-content-level-1">
                <div id="search-wrap" class="tab-pane fade in active">
                    <div class="search-wrap-outer">
                        <div class="search-inner">
                            <img src="{{ url('/alchemy/images') }}/search-order-image.svg">
                            <div class="search-form">
                                <label><span class="visible-xs">Order Number</span><span class="hidden-xs">find a live order</span></label>
                                <input type="text" placeholder="E.G. 12345" name="order_id" required="true">
                                <div class="help-text"><span class="hidden-xs">Type here the  driver’s order number to see the details.</span><span class="visible-xs">Enter the order number to see the details.</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="stickyfooter action-buttons">
                        <button>Search Order</button>
                    </div>
                </div>
                <div id="order-history-wrap" class="tab-pane fade">
                    @if(isset($history))
                    <ul>
                        @foreach($history AS $key => $value)
                        <li class="item">
                            <div class="order-info">
                                <span class="orderId">#{!! $value['orderId'] !!}</span>
                                <span class="orderCount">{!! $value['quantity'] !!} items</span>
                                <span class="orderCost">{!! Config::get('appConstants.currency_sign') !!}{!! CommonHelper::formatPrice($value['totalPrice']) !!}</span>
                            </div>
                            <div class="order-thumb">
                                @foreach($value['productDetail'] AS $key => $productDetail)
                                <span><img src="{!! CommonHelper::getProductImage($productDetail->id, true) !!}"></span>
                                @endforeach
                            </div>
                            <a href="{{url('store/trackorder', [$value['orderId']])}}" class="order-summary-link"></a>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="order-history-desktop hidden-xs">
        <div class="container">
            <div class="row">
                <h3 class="title">My order history</h3>
                @if(isset($history))
                    <ul>
                        @foreach($history AS $key => $value)
                        <li class="item">
                            <div class="order-info">
                                <span class="orderId">#{!! $value['orderId'] !!}</span>
                                <span class="orderCount">{!! $value['quantity'] !!} items</span>
                                <span class="orderCost">{!! Config::get('appConstants.currency_sign') !!}{!! CommonHelper::formatPrice($value['totalPrice']) !!}</span>
                            </div>
                            <div class="order-thumb">
                                @foreach($value['productDetail'] AS $key => $productDetail)
                                <span><img src="{!! CommonHelper::getProductImage($productDetail->id, true) !!}"></span>
                                @endforeach
                            </div>
                            <a href="{{url('store/trackorder', [$value['orderId']])}}" class="order-summary-link"></a>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @if(count($history) >= 1)
            <div class="col-xs-12 show-more-btn hidden-xs">
                <a class="btn-red" href="{!! route('store.history')!!}">See more</a>
            </div>
            @endif
        </div>    
    </div>
    {!! Form::close() !!}
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
                    {{ link_to_route('store.orderSearch', 
                                        'Try Again', '', 
                                        array('data-dismiss' => 'modal', 'type' => 'button', 'class' => 'try-again'))}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
