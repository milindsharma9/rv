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
<section class="benefits register-page">
    <h3 class="section-title"><span>Benefits</span></h3>
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-sm-4">
                <div class="benefit-item">
                    <div class="benefit-fig">
                        <img src="{{ url('alchemy/images') }}/benefits/benefits-5.png">
                    </div>
                    <div class="benefit-info">
                        Reach more shoppers<br/>over a wider area
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-4">
                <div class="benefit-item">
                    <div class="benefit-fig">
                        <img src="{{ url('alchemy/images') }}/benefits/benefits-6.png">
                    </div>
                    <div class="benefit-info">
                        We take care<br/>of delivery
                    </div>
                </div>
            </div>
            <div class="clearfix visible-xs"></div>
            <div class="col-xs-6 col-sm-4">
                <div class="benefit-item">
                    <div class="benefit-fig">
                        <img src="{{ url('alchemy/images') }}/benefits/benefits-1.png">
                    </div>
                    <div class="benefit-info">
                        Connect with shoppers that<br/>don’t know where you are
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-4">
                <div class="benefit-item">
                    <div class="benefit-fig">
                        <img src="{{ url('alchemy/images') }}/benefits/benefits-7.png">
                    </div>
                    <div class="benefit-info">
                        Sell more products<br/>that make you margin
                    </div>
                </div>
            </div>
            <div class="clearfix visible-xs"></div>
            <div class="col-xs-6 col-sm-4">
                <div class="benefit-item">
                    <div class="benefit-fig">
                        <img src="{{ url('alchemy/images') }}/benefits/benefits-3.png">
                    </div>
                    <div class="benefit-info">
                        Free POS<br/>for your store
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-4">
                <div class="benefit-item">
                    <div class="benefit-fig">
                        <img src="{{ url('alchemy/images') }}/benefits/benefits-8.png">
                    </div>
                    <div class="benefit-info">
                        Your own account<br/>with free selling guides
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="video-container">
    <div class="video-overlay"></div>
    <a class="video-button"></a>
    <div class="container">
        <video poster="{{ url('alchemy/video') }}/alchemy-poster.png">
            <source src="{{ url('alchemy/video') }}/alchemy.mp4" type="video/mp4">
        </video>
    </div>
</section>
<section class="sign-up register-content-section" id="response">
    <h3 class="section-title"><span>Sign Up Now</span></h3>
    <div class="container">
        @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif
        @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
            </ul>
        </div>
        @endif

        @include('partials.register-vendor-form')
    </div>
</section>

@include('partials.login')
@endsection
@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('alchemy/js') }}/jquery.mousewheel.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}" type="text/javascript"></script>
<script src="{{ url('alchemy/js') }}/jquery.jscrollpane.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}" type="text/javascript"></script>
<script src="{{ url('alchemy/js') }}/bootstrap-select.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
var companyDetailsUrl = "{!! route('store.companydetails'); !!}";
var officerDetailsUrl = "{!! route('store.officerdetails'); !!}";
</script>
<script src="{{ url('js') }}/register-vendor.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
@endsection