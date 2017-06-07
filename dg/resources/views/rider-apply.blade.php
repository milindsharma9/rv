@section('title', ' Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@extends('layouts.simple-header-layout')

@section('content')
@php
    $bannerImageDir = config('banner.banner_image_dir');
@endphp
<section class="siteBanner-section-title" style="background-image:url({{ url('uploads/'.$bannerImageDir) }}/{{$bannerImage}});">
    
	<img src="{{ url('uploads/'.$bannerImageDir) }}/{{$bannerImage}}"/>
	<div class="banner-title">
		<h1>{{$bannerTitle}}</h1>
		<h3>{{$bannerSubText}}</h3>
	</div>
</section>
<section class="benefits">
    <h3 class="section-title"><span>Benefits</span></h3>
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-sm-3">
                <div class="benefit-item">
                    <div class="benefit-fig">
                        <img src="{{ url('alchemy/images') }}/benefits/benefits-1.png">
                    </div>
                    <div class="benefit-info">
                        Self-employed, and free to work your own schedule
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-3">
                <div class="benefit-item">
                    <div class="benefit-fig">
                        <img src="{{ url('alchemy/images') }}/benefits/benefits-2.png">
                    </div>
                    <div class="benefit-info">
                        We keep your delivery area small and your shift local
                    </div>
                </div>
            </div>
            <div class="clearfix visible-xs"></div>
            <div class="col-xs-6 col-sm-3">
                <div class="benefit-item">
                    <div class="benefit-fig">
                        <img src="{{ url('alchemy/images') }}/benefits/benefits-3.png">
                    </div>
                    <div class="benefit-info">
                        We take care of all the cash through electronic payment
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-3">
                <div class="benefit-item">
                    <div class="benefit-fig">
                        <img src="{{ url('alchemy/images') }}/benefits/benefits-4.png">
                    </div>
                    <div class="benefit-info">
                        You’ll receive high-quality gear and earn great money
                    </div>
                </div>
            </div>
            <div class="clearfix visible-xs"></div>
        </div>
    </div>
</section>
<section class="sign-up" id="response">
	<h3 class="section-title"><span>Sign Up Now</span></h3>
        <div class="row">
            <div class="container">
                @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                        </ul>
                        </div>
                @endif
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
            </div>
        </div>
            
@include('partials.driver-apply-form', ['driverFormRoute' => $driverFormRoute])
</section>

@include('partials.login')
@endsection
@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/driver_apply.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('alchemy/js') }}/driver_apply.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
@endsection