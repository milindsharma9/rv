<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=9">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Alchemy')</title>

    <meta content="@yield('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')" name='description'>
    <meta content="@yield('meta_keywords', 'delivery, online, alcohol, free, liquor, food, snacks, home, shop, shopping, store, groceries, grocery, app, download, iOS, Android, service, beer, drinks, london, buy, market')" name='keywords'>
    <!-- Favicon -->
    <link rel="icon" href="{{ url('alchemy/images') }}/favicon.ico" sizes="32x32" type="image/ico">

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ url('css') }}/font-awesome.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ url('css') }}/bootstrap.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    <link href="{{ url('alchemy/stylesheets') }}/owl.carousel.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css') }}/jquery-ui.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    <link href="{{ url('alchemy/stylesheets/customer') }}/style.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <script src="{{ url('js') }}/jquery.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/open_time.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>

</head>
<?php 
    $dashboard = $home = $products = $occasions = $creations = '';
    $action = (isset(explode('/',Request::path())[1])) ? (explode('/',Request::path())[1]): Request::path();
    switch (strtolower($action)) {
        case 'products':
        case 'bundleDetail':
        case 'cat':
            $products = 'active';
            break;
        case 'home':
            $home = 'active';
            break;
        case 'occasions':
            $occasions = 'active';
            break;
        case 'creations':
            $creations = 'active';
            break;
        case 'dashboard':
        case 'history':
        case 'paymentdetails':
        case 'editprofile':
        case 'payment':
        case 'address':
        case 'faq':
            $dashboard = 'active';
            break;

      
        case 'search': if(isset(explode('/',Request::path())[2])){
        $action2 = (explode('/',Request::path())[2]);
        if ($action2 == 'creations')
            $creations = 'active';
        elseif ($action2 == 'occasions')
            $occasions = 'active';
        }
        break;
        
    default:
        $action = (isset(explode('/',Request::path())[0])) ? (explode('/',Request::path())[0]): Request::path();
        if($action == 'productdetail'){
            $products = 'active';
            break;
        }
//            $home = 'active';
            break;
    }
    ?>
<body id="app-layout" class="customer-template">
    @include('partials.google_tag')
<div class="siteWrapper">
    @if(isset($noHeader) && $noHeader == 'true')
    @else
    @if((CommonHelper::checkForSiteTimings()))
    <div class="store-unavail-error">
       {{ CommonHelper::getOfflineMessage()}}
    </div>
    @include('partials.check-store-site-timing')
    @else
    <div class="store-unavail-error">
        {{ CommonHelper::getOnlineMessage()}}
    </div>
    @endif
    <header class="siteHeader">
        <div class="container">
            <div class="row">                
                <div class="col-xs-6 col-sm-3 col-md-3 logo">
                    <a href="{{ route('home.index') }}"><img src="{{ url('alchemy/images') }}/logo.svg"></a>
                </div>
                <div class="col-xs-6 col-sm-9 col-md-9 top-links">
                    <ul class="search-cart">
                        <li>
                            <a class="available-zipcode" data-toggle="modal" data-target="#selected-location-popup">
                                <i class="glyphicon glyphicon-map-marker"></i>
                                <span class="zipcode-location">{{CommonHelper::getUserCartDeliveryPostcode()}}</span>
                            </a>
                        </li>
                        <li>
                            <a class="btn-search" href=""></a>
                        </li>
                        @if((Auth::user()))
                            <li>
                                <a class="btn-account" href="{{route('customer.dashboard')}}"></a>
                                <ul class="hidden-xs">
                                    <li class="menu-logout"><a href="{{ route('customer.dashboard') }}">Settings</a></li>
                                    <li class="menu-logout"><a href="{{ url('/logout') }}">Log out</a></li>
                                </ul>
                            </li>
                        @else
                        <li>
                            <a class="btn-account" id="login" data-target="#login-register" data-toggle="modal"></a>
                        </li>
                        @endif
                        <li>
                            <a href="{{route('customer.cart')}}" class="btn-cart">
                                <span class="prod-count" id="cart_header">{{session()->get("cart_custom_total", '')}}</span>
                            </a>
                        </li>
                    </ul>
                    <nav id="main-menu" class="visible-xs">
                        <ul>
                            <li class="menu-home {!! $home!!}"><a href="{{route('home.index')}}">Home</a></li>
                            <li class="menu-product {!! $products!!}"><a href="{{route('customer.products')}}">Products</a></li>
                            <li class="menu-occation {!! $occasions!!}"><a href="{{route('customer.occasions')}}">Occasions</a></li>
                            <li class="menu-creation {!! $creations!!}"><a href="{{route('customer.creations')}}">Themes</a></li>
                            @if((Auth::user()))
                                <li class="menu-profile {!! $dashboard!!}"><a href="{{route('customer.dashboard')}}">My profile</a></li>
                                <li class="menu-logout"><a href="{{ url('/logout') }}">Logout</a></li>
                            @endif
                        </ul>
                    </nav>
                    <nav id="main-menu" class="hidden-xs">
                        <ul>
                            <li class="menu-home {!! $home!!}"><a href="{{route('home.index')}}">Home</a></li>
                            <li class="menu-product {!! $products!!}"><a href="{{route('customer.products')}}">Products</a></li>
                            <li class="menu-occation {!! $occasions!!}"><a href="{{route('customer.occasions')}}">Occasions</a></li>
                            <li class="menu-creation {!! $creations!!}"><a href="{{route('customer.creations')}}">Themes</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-xs-12 search-form">
                    <form action="{{route('customer.search')}}">
                        <button class="btn-search-action">Search</button>
                        <input id='search' type="text" placeholder="Search..." name="param" required="" />
                        <a class="btn-search-close"></a>
                    </form>
                </div>
            </div>
        </div>
        @include('customer.partials.selected-location-postcode')
    </header>
    @endif
    <script>
        var checkStoreTimeUrl              = "{!! route('customer.site.time')!!}";
    </script>