<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Alchemy Wings')</title>
        <meta content="@yield('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')" name='description'>
        <meta content="@yield('meta_keywords', 'delivery, online, alcohol, free, liquor, food, snacks, home, shop, shopping, store, groceries, grocery, app, download, iOS, Android, service, beer, drinks, london, buy, market')" name='keywords'>
        <!-- Styles -->
        <link rel="stylesheet" href="{{ url('css') }}/bootstrap.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
        <link href="{{ url('alchemy/stylesheets') }}/style.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">

    </head>
    <body style="background-color: #fff">
        @yield('content')

    <script type="text/javascript" src="{{ url('alchemy/js') }}/jquery.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('js') }}/bootstrap.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/owl.carousel.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script type="text/javascript" src="{{ url('alchemy/js') }}/main.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    </body>
</html>