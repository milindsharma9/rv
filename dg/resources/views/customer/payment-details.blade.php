@section('title')
Alchemy - Payment Details
@endsection
@extends('customer.layouts.customer')
@section('content')
<section class="customer-content-section customer-address-confirm-section customer-payment-detail-section">
    <div class="payment-address">
        @if((Auth::user()))
            <div class="container">
                <h3 class="title"><a href="{{ url()->previous() }}" class="btn-red">< Back</a>payment details</h3>
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
</section>
@endsection