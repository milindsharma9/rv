<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Seshaasai')</title>
    <meta content="@yield('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')" name='description'>
    <meta content="@yield('meta_keywords', 'delivery, online, alcohol, free, liquor, food, snacks, home, shop, shopping, store, groceries, grocery, app, download, iOS, Android, service, beer, drinks, london, buy, market')" name='keywords'>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ url('css') }}/bootstrap.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    <link href="{{ url('alchemy/stylesheets') }}/owl.carousel.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <link href="{{ url('alchemy/stylesheets') }}/animate.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css') }}/jquery-ui.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    <link rel="stylesheet" href="{{ url('alchemy/stylesheets') }}/bootstrap-select.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    <link rel="stylesheet" href="{{ url('alchemy/stylesheets') }}/jquery.jscrollpane.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}" type="text/css" media="all" />
    <link href="{{ url('alchemy/stylesheets') }}/store/style.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <link href="{{ captcha_layout_stylesheet_url() }}" type="text/css" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
</head>
<body class="store-template">
    <div class="siteWrapper">
    <header class="siteHeader">
        <div class="container">
            <div class="row">
                <div class="col-xs-9 col-sm-3 col-md-3 logo">
                    @if(isset(Auth::user()['id']) && Auth::user()['id'] == config('appConstants.vendor_role_id')) 
                    <a href="{{ route('store.dashboard') }}"><img src="{{ url('alchemy/images') }}/logo.png"></a>
                    @else
                    <a href="{{ route('home.index') }}"><img src="{{ url('alchemy/images') }}/logo.png"></a>
                    @endif
                </div>
            </div>
        </div>
    </header>