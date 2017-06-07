<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=9">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Alchemy Store')</title>
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
    <link href="{{ url('alchemy/stylesheets/store') }}/style.css?v={{ env('ASSETS_VERSION_NUMBER') }}" rel="stylesheet">
    
</head>
<body class="store-template">
    <?php 
    $dashboards = $orderSearch = $sales = $products = $profile = '';
    $action = (isset(explode('/',Request::path())[1])) ? (explode('/',Request::path())[1]): '';
    switch (strtolower($action)) {
        case 'ordersearch':
        case 'history': 
            $orderSearch = 'active';

            break;
        case 'sales':
            $sales = 'active';

            break;
        case 'myproducts':
            $products = 'active';

            break;
        case 'profile':
        case 'editprofile':
        case 'faq':
            $profile = 'active';

            break;
        case 'dashboard': 
            $dashboards = 'active';
            break;
        default:
            break;
    }
    ?>
     @if(!(CommonHelper::checkVendorKycStatus()))
    <div class="store-unavail-error">
        {{trans('messages.vendor_kyc_not_found')}} <a href="{{ route('store.kyc.register') }}">here</a>.
    </div>
     @endif
<div class="siteWrapper">
    <header class="siteHeader">
        <div class="container">
            <div class="row">
                <div class="col-xs-6 col-sm-3 col-md-3 logo">
                    <a class="hidden-xs" href="{{ route('store.dashboard') }}"><img src="{{ url('alchemy/images') }}/logo.svg"></a>
                    <a href="{{ route('store.dashboard') }}" class="logo-icon visible-xs"></a><span class="logo-title visible-xs">@yield('header')</span>
                </div>
                <?php
                $vendorSubStores = CommonHelper::getVendorSubStores();
                $selectedSubStoreId = CommonHelper::getSelectedSubStoreId();
                ?>
                <div class="profile-bar col-xs-4 col-sm-3 col-md-3">
                    <ul>
                        <li class="store-selected"><span> <?php echo $vendorSubStores[0]['store_name']; ?> </span>
                            <ul>
                                <?php
                                    if (!empty($vendorSubStores)) {
                                        foreach ($vendorSubStores as $subStoreDetails) {
                                            $liClass = '';
                                            if ($selectedSubStoreId == $subStoreDetails['fk_users_id']) {
                                                $liClass = 'store-active';
                                            }
                                            echo '<li class="'. $liClass .'"><a href="'.route('store.set.substore', $subStoreDetails['fk_users_id']).'">'.$subStoreDetails['store_name'].'</a></li>';
                                        }
                                    }
                                ?>
                                <li><a data-toggle="modal" data-target="#add-store"><i class="glyphicon glyphicon-plus-sign"></i> Add Store</a></li>
                            </ul>
                        </li>
                    </ul>
                    @include('store.partials.add-store')
                </div>
                <div class="col-xs-2 col-sm-6 col-md-6 top-links">
                    <nav id="main-menu">
                        <ul class="visible-xs">
                            <li class="menu-home {!! $dashboards!!}"><a href="{{route('store.dashboard')}}">My dashboard</a></li>
                            <li class="menu-orders {!! $orderSearch!!}"><a href="{{route('store.orderSearch')}}">My orders</a>
                            </li>
                            <li class="menu-sales {!! $sales!!}"><a href="{{route('store.sales')}}">My sales</a>
                            </li>
                            <li class="menu-product {!! $products!!}"><a href="{{route('store.products')}}">My products</a></li>
                            <li class="menu-profile {!! $profile!!}"><a href="{{route('store.profile')}}">My profile</a></li>
                            <?php if(isset(Auth::user()['id'])) { ?>
                            <li class="menu-logout"><a href="{{ url('/logout') }}">Logout</a></li>
                            <?php } ?>
                        </ul>
                        <ul class="hidden-xs">
                            <li class="menu-orders {!! $orderSearch!!}"><a href="{{route('store.orderSearch')}}">My orders</a>
                            </li>
                            <li class="menu-sales {!! $sales!!}"><a href="{{route('store.sales')}}">My sales</a>
                            </li>
                            <li class="menu-product {!! $products!!}"><a href="{{route('store.products')}}">My products</a></li>
                            <li class="menu-profile {!! $profile!!} {!! $dashboards!!}"><a href="{{route('store.profile')}}">My profile</a>
                                <ul>
                                    <li class="menu-home {!! $profile!!}"><a href="{{route('store.dashboard')}}">My dashboard</a></li>
                                    <?php if(isset(Auth::user()['id'])) { ?>
                                    <li class="menu-logout"><a href="{{ url('/logout') }}">Logout</a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>