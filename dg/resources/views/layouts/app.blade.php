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
    <link href="{{ url('alchemy/stylesheets') }}/animate.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    <link href="{{ url('alchemy/stylesheets') }}/style.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
</head>
<body id="app-layout">
    <header class="siteHeader">
        <div class="container">
            <div class="row">
                <div class="col-xs-7 logo">
                    <a href="{{ url('/') }}">Alchemy</a>
                </div>
                <div class="col-xs-5 top-links">
                    <ul class="search-cart">
                        <li>
                            <a class="btn-search" href=""></a>
                        </li>
                        <li>
                            <a class="btn-account" href=""></a>
                        </li>
                    </ul>
                    <nav id="main-menu">
                        <ul>
                            <li class="menu-home"><a href="#">Home</a></li>
                            <li class="menu-product"><a href="#">Products</a>
                                <ul>
                                    <li><a href="#">Products 1</a></li>
                                    <li><a href="#">Products 2</a>
                                        <ul>
                                            <li><a href="#">Products 1</a></li>
                                            <li><a href="#">Products 2</a></li>
                                            <li><a href="#">Products 3</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="#">Products 3</a></li>
                                </ul>
                            </li>
                            <li class="menu-occation"><a href="#">Occasions</a>
                                <ul>
                                    <li><a href="#">Occasions 1</a></li>
                                    <li><a href="#">Occasions 2</a></li>
                                    <li><a href="#">Occasions 3</a></li>
                                </ul>
                            </li>
                            <li class="menu-creation"><a href="#">Themes</a></li>
                            <li class="menu-profile"><a href="{{url('/customer/editProfile')}}">My profile</a></li>
                            <?php if(isset(Auth::user()['id'])) { ?>
                            <li class="menu-logout"><a href="{{ url('/logout') }}">Logout</a></li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
                <div class="col-xs-12 search-form">
                    <form action="">
                        <button class="btn-search-action">Search</button>
                        <input type="text" placeholder="Search..." />
                        <a class="btn-search-close"></a>
                    </form>
                </div>
            </div>
        </div>
    </header>
    <section class="home-slider feature-banner">
        <div class="item"><img src="{{ url('alchemy/images') }}/home-banner.png">
            <div class="caption">
                <img src="{{ url('alchemy/images') }}/j&b-logo.png">
                <h1>J&B New Tattoo<br><span>Collection</span></h1>
            </div>
        </div>
        <div class="item"><img src="{{ url('alchemy/images') }}/home-banner-2.png">
            <div class="caption">
                <img src="{{ url('alchemy/images') }}/foodpairing-logo.svg">
                <h1>Food<br><span>Pairing</span></h1>
            </div>
        </div>
        <div class="item"><img src="{{ url('alchemy/images') }}/home-banner-3.png">
            <div class="caption">
                <img src="{{ url('alchemy/images') }}/party-logo.svg">
                <h1><span>Party Time</span></h1>
            </div>
        </div>
    </section>
    <section class="category-wrap">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h3 class="title center">What are you looking for?</h3>
                    <ul class="category-links">
                        <li><a href=""><img src="{{ url('alchemy/images') }}/alcohol.svg">Alcohol</a></li>
                        <li><a href=""><img src="{{ url('alchemy/images') }}/drinks.svg">Drinks</a></li>
                        <li><a href=""><img src="{{ url('alchemy/images') }}/food.svg">Food</a></li>
                        <li><a href=""><img src="{{ url('alchemy/images') }}/other.svg">Other</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="occation-wrap">
        <h3 class="title center">What’s the occasion?</h3>
        <div class="featured-occation">
            <div class="occation-item">
                <img class="bgImg" src="{{ url('alchemy/images') }}/casual-get-together.svg">
                <div class="occation-desc">
                    <img src="{{ url('alchemy/images') }}/casual-get-together-logo.svg">
                    <h3>Casual get together</h3>
                </div>
            </div>
        </div>
        <div class="occation-slider">
            <div class="occation-item">
                <img class="bgImg" src="{{ url('alchemy/images') }}/party.svg">
                <div class="occation-desc">
                    <img src="{{ url('alchemy/images') }}/party-logo.svg">
                    <h3>Party time</h3>
                </div>
            </div>
            <div class="occation-item">
                <img class="bgImg" src="{{ url('alchemy/images') }}/relax-image.svg">
                <div class="occation-desc">
                    <img src="{{ url('alchemy/images') }}/relax-logo.svg">
                    <h3>Relax</h3>
                </div>
            </div>
            <div class="occation-item">
                <img class="bgImg" src="{{ url('alchemy/images') }}/occation-4.svg">
                <div class="occation-desc">
                    <img src="{{ url('alchemy/images') }}/party-logo.svg">
                    <h3>Party time</h3>
                </div>
            </div>
        </div>
    </section>
    <section class="create-event-wrap">
        <h3 class="title center">What do you want to create?</h3>
        <div class="create-event-inner-wrap">
            <div class="occation-item lets-hope-sun">
                <img class="bgImg" src="{{ url('alchemy/images') }}/lets-hope-sun.png">
                <div class="occation-desc">
                    <img src="{{ url('alchemy/images') }}/lets-hope-sun-logo.svg">
                    <h3>Let's hope for sun</h3>
                </div>
            </div>
            <div class="occation-item">
                <img class="bgImg" src="{{ url('alchemy/images') }}/events-image.png">
                <div class="occation-desc">
                    <img src="{{ url('alchemy/images') }}/events-logo.svg">
                    <h3>My social calendar</h3>
                </div>
            </div>
            <div class="occation-item">
                <img class="bgImg" src="{{ url('alchemy/images') }}/gifting-image.png">
                <div class="occation-desc">
                    <img src="{{ url('alchemy/images') }}/gifting-logo.svg">
                    <h3>Gifting</h3>
                </div>
            </div>
            <div class="occation-item">
                <img class="bgImg" src="{{ url('alchemy/images') }}/trendsetters-image.png">
                <div class="occation-desc">
                    <img src="{{ url('alchemy/images') }}/trendsetters-logo.svg">
                    <h3>Trendsetters</h3>
                </div>
            </div>
            <div class="occation-item">
                <img class="bgImg" src="{{ url('alchemy/images') }}/cocktail-image2.png">
                <div class="occation-desc">
                    <img src="{{ url('alchemy/images') }}/cocktail-logo.svg">
                    <h3>Visit the bar</h3>
                </div>
            </div>
            <div class="occation-item">
                <img class="bgImg" src="{{ url('alchemy/images') }}/foodpairing-image.png">
                <div class="occation-desc">
                    <img src="{{ url('alchemy/images') }}/foodpairing-logo.svg">
                    <h3>Food pairing</h3>
                </div>
            </div>
            
        </div>
    </section>
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
</body>
</html>
