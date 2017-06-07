@extends('layouts.api')
@section('content')
<section class="customer-content-section customer-payment-edit-section">
    <div class="order-header visible-xs"><h3 class="title">

            @if(isset($noHeader) && $noHeader == 'true')
            @else
            <a href="{{ url()->previous() }}" class="btn-red">< Back</a>
            @endif
            Payment Details</h3></div>
    @foreach($data as $errorKey => $errorVal)
    <div class="alert alert-danger">
        <ul>
            {!! $errorKey !!}:
            @if(is_array($errorVal))
            {!! $errorVal['Message'] !!}
            @else
            {!! $errorVal!!}
            @endif
        </ul>
    </div>
    @endforeach
    <span class="help-block" id="error-popup"></span>
    <div class="stickyfooter action-buttons">
        <a href="{{ url()->previous() }}">Try Again</a>
    </div>
</section>
@endsection