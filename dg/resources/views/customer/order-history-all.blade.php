@section('title')
Alchemy - My Orders
@endsection
@extends('customer.layouts.customer')
@section('header')
<a href="{{ route('customer.dashboard') }}" class="logo-icon visible-xs">My Orders</a>
@endsection
@section('content')
<section class="customer-content-section customer-order-history-section">
    <div class="order-history-desktop">
        <div class="container">
            <h3 class="title visible-xs">My order history</h3>
            <div class="row" id="news">
                <h3 class="title hidden-xs">My order history</h3>
                @if(isset($history))
                <ul id="items">
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
                        <a href="{{url('customer/trackorder', [$value['orderId']])}}" class="order-summary-link"></a>
                    </li>
                    @endforeach
                    @if (!empty($history))
                    {!! $history->links() !!}
                    @endif
                </ul>
                @endif
            </div>
        </div>    
    </div>
</section>
@endsection
@section('javascript')
<script src="{{ url('external') }}/jquery.infinitescroll.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
$(document).ready(function() {
        $('.pagination').hide();
            var loading_options = {
                finishedMsg: "<div class='end-msg'>No more Orders.</div>",
                msgText: "<div class='center'>Loading Orders...</div>",
                img: "/assets/img/ajax-loader.gif"
            };
            $('#items').infinitescroll({
              loading : loading_options,
              navSelector : "#news .pagination",
              nextSelector : "#news .pagination li.active + li a",
              itemSelector : "#items li.item"
            });
        });
</script>
@stop