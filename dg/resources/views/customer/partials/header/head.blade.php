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
    $dashboard = $home = $products = $occasions = $creations = $blog = $events 
            = $places = $alcohol = $softdrinks = $food = $other = '';
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
        case 'blog':
            $blog = 'active';
            break;
        case 'events':
            $events = 'active';
            break;
        case 'places':
            $places = 'active';
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
        case 'alcohol':
            $alcohol = 'active';
            break;
        case 'soft-drinks':
            $softdrinks = 'active';
            break;
        case 'food':
            $food = 'active';
            break;
        case 'other':
            $other = 'active';
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
        $action = (isset(explode('/', Request::path())[0])) ? (explode('/', Request::path())[0]) : Request::path();
        switch ($action) {
            case 'productdetail':
                $products = 'active';

                break;

            case 'blog':
                $blog = 'active';
                break;
            case 'events':
                $events = 'active';
                break;
            case 'places':
                $places = 'active';
                break;

            default:
                break;
        }

//            $home = 'active';
        break;
}
    ?>
<body class="@yield('body-class')" style="@yield('body-style')">
    @include('partials.google_tag')
<div class="siteWrapper">
    @if(isset($noHeader) && $noHeader == 'true')
    @else
    @include('customer.partials.header.navigation')
    @endif
    <script>
        var checkStoreTimeUrl   = "{!! route('customer.site.time')!!}";
        var loadingImageURL     = "{{  url('alchemy/images')}}/rolling-loader.svg";
    </script>