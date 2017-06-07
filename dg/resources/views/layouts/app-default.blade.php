<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alchemy Home</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
    <link href="{{ url('alchemy/stylesheets') }}/owl.carousel.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <link href="{{ url('alchemy/stylesheets') }}/style.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <link href="{{ url('alchemy/stylesheets') }}/animate.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    
</head>
<body>
    <header class="siteHeader">
        <div class="container">
            <div class="row">
                <div class="col-xs-9 logo">
                    @yield('header')
                </div>
                @if((Auth::user()))
                    <div class="col-xs-3 top-links">
                        <nav id="main-menu">
                            <ul>
                                <li class="menu-home"><a href="{{route('store.dashboard')}}">My dashboards</a></li>
                                <li class="menu-orders"><a href="{{route('store.orderSearch')}}">My orders</a>
                                </li>
                                <li class="menu-sales"><a href="{{route('store.sales')}}">My sales</a>
                                </li>
                                <li class="menu-product"><a href="{{route('store.products')}}">My products</a></li>
                                <li class="menu-profile"><a href="{{route('store.profile')}}">My profile</a></li>
                                <?php if(isset(Auth::user()['id'])) { ?>
                                <li class="menu-logout"><a href="{{ url('/logout') }}">Logout</a></li>
                                <?php } ?>
                            </ul>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </header>
    @yield('content')
    <footer class="siteFooter">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 social-links">
                    <ul>
                        <li><a href="{!! config('appConstants.twitter'); !!}" target="_blank"><img src="{{ url('alchemy/images') }}/twitter.svg"></a></li>
                        <li><a href="{!! config('appConstants.facebook'); !!}" target="_blank"><img src="{{ url('alchemy/images') }}/facebook.svg"></a></li>
                        <li><a href="{!! config('appConstants.instagram'); !!}" target="_blank"><img src="{{ url('alchemy/images') }}/instagram.svg"></a></li>
                        <li><a href="{!! config('appConstants.pinterest'); !!}" target="_blank"><img src="{{ url('alchemy/images') }}/pinterest.svg"></a></li>
                        <li><a href="mailto:{!! config('appConstants.mailto'); !!}"><img src="{{ url('alchemy/images') }}/email.svg"></a></li>
                    </ul>
                </div>
                <div class="col-xs-12 copyright">&copy;2016 Alchemy Wings.</div>
            </div>
        </div>
    </footer>

    <!-- JavaScripts -->
    <script src="{{ url('alchemy/js') }}/jquery.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/owl.carousel.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/wow.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script src="{{ url('alchemy/js') }}/main.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script>
        new WOW().init();
    </script>
    @yield('javascript')
</body>
</html>