<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Seshaasai ')</title>
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
    <link href="{{ url('alchemy/stylesheets') }}/animate.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css') }}/jquery-ui.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    <link href="{{ url('alchemy/stylesheets/customer') }}/style.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <script src="{{ url('js') }}/jquery.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/open_time.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
</head>
<body id="landing-front-layout">
    @include('partials.google_tag')
    @yield('content')
    @include('partials.social-links')
    <!-- JavaScripts -->
    <script src="{{ url('alchemy/js') }}/jquery.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('js') }}/bootstrap.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/owl.carousel.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/main.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
   
    @yield('javascript')
    @if(CommonHelper::checkVisitor() == TRUE)
    @include('partials.cookies-policy')
    @endif
</body>
</html>
