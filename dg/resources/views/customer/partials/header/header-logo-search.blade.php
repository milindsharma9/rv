<div class="header-middle">
    <div class="container">
        <div class="row">            
            <div class="col-xs-12 col-sm-4 logo">
                <div class="mobile-trigger visible-xs"></div>
                <a href="{{ route('home.index') }}">
                    <img src="{{ url('alchemy/images') }}/logo.svg" alt="Alchemy Wings" class="logo-full"/>
                    <img src="{{ url('alchemy/images') }}/logo-icon.svg" alt="Alchemy Wings" class="logo-icon" />
                </a>
            </div>
            <div class="col-xs-12 search-form">
                <form action="{{route('customer.search')}}">
                    <input id='search' type="text" placeholder="Search for drinks, snacks, extras..." name="param" required="" />
                    <span class="btn-search-close">Search</span>
                </form>
            </div>
            <div class="search-result" id='search_content_data'>
                
            </div>
            <nav id="main-menu" class="col-xs-12 col-sm-4 main-menu">
                <ul class="hidden-xs">
                    <li class="menu-blog"><a href="{{route('common.blog')}}">Blog</a></li>
                    <li class="menu-event"><a href="{{route('common.events')}}">Events</a></li>
                    <li class="menu-places"><a href="{{route('common.places')}}">Places</a></li>
                    <li class="menu-occation {!! $occasions!!}"><a href="{{route('customer.occasions')}}">Occasions</a></li>
                    <li class="menu-creation {!! $creations!!}"><a href="{{route('customer.creations')}}">Themes</a></li>
                </ul>

                <ul class="visible-xs">
                     @php
                        $selectedPostcode = CommonHelper::getUserCartDeliveryPostcode();
                        $postcodeClass = '';
                    @endphp
                    <li class="menu-postcode">
                        @if (empty($selectedPostcode))
                            <a>
                                <span>Where can we deliver your drinks?</span>
                                <span class="postcode-icon">Enter your postcode</span>
                            </a>
                        @else
                            <a class="active">
                                <span class="postcode-icon">{{$selectedPostcode}}</span>
                            </a>
                        @endif
                    </li>
                    @if((Auth::user()))
                        <li class="menu-user-desc">
                            <a href="{{ route('customer.dashboard') }}">Welcome {{Auth::user()->first_name}} {{Auth::user()->last_name}}
                            </a>
                        </li>
                        <li class="menu-home {!! $home!!}"><a href="{{route('home.index')}}">Home</a></li>
                    @else
                        <li class="menu-login"><a>Login</a></li>
                        <li class="menu-register"><a>Register</a></li>
                    @endif
                    <li class="menu-border"></li>
                    @php
                        $catTree = CommonHelper::getCategoryTree();
                    @endphp
                    @foreach($catTree['categories'] as $catId => $aCat)
                        @php 
                            $formatcatName = CommonHelper::formatCatName($aCat['name']);
                            $cat2 = str_replace('-', '', $formatcatName);
                        @endphp
                         <li class="menu-{{$formatcatName}} {{$$cat2}}"><a href="{{route('customer.products', ['catname' => $formatcatName, 'id' => $catId])}}"">{{$aCat['name']}}</a></li>
                    @endforeach
                    <li class="menu-border"></li>
                    <li class="menu-occation {!! $occasions!!}"><a href="{{route('customer.occasions')}}">Occasions</a></li>
                    <li class="menu-creation {!! $creations!!}"><a href="{{route('customer.creations')}}">Themes</a></li>
                    <li class="menu-border"></li>
                    <li class="menu-blog {{$blog}}"><a href="{{route('common.blog')}}">Blog</a></li>
                    <li class="menu-event {{$events}}"><a href="{{route('common.events')}}">Events</a></li>
                    <li class="menu-places {{$places}}"><a href="{{route('common.places')}}">Places</a></li>
                    @if((Auth::user()))
                        <li class="menu-logout"><a href="{{ url('/logout') }}">Logout</a></li>
                    @endif
                </ul>

            </nav>
            <div class="top-navigation-links">
                <ul>
                    <li>
                        <a class="btn-search"></a>
                    </li>
                    <li>
                       
                        @if (!empty($selectedPostcode))
                            @php
                                $postcodeClass = 'active';
                            @endphp
                        @endif
                        <a class="available-zipcode {{$postcodeClass}}" data-toggle="modal" data-target="#selected-location-popup">
                            <span class="zipcode-location">{{$selectedPostcode}}</span>
                        </a>
                    </li>
                    @if((Auth::user()))
                        <li class="hidden-xs">
                            <a class="btn-account" href="{{route('customer.dashboard')}}"></a>
                            <ul class="hidden-xs">
                                <li class="menu-user-desc">
                                    @if((Auth::user()->image) != '')
                                        <span class="user-profile" style="background-image:url({{ asset('uploads/user') . '/'. Auth::user()->image }});"></span>
                                    @else
                        		<span class="user-profile" style="background-image:url({{ url('alchemy/images') }}/user-default.png);"></span>
                                    @endif
                                    <span class="user-name">Welcome {{Auth::user()->first_name}} {{Auth::user()->last_name}}</span>
                                </li>
                                <li class="menu-profile"><a href="{{ route('customer.dashboard') }}">My Profile</a></li>
                                <li class="menu-logout"><a href="{{ url('/logout') }}">Logout</a></li>
                            </ul>
                        </li>
                    @else
                        <li class="hidden-xs">
                            <a class="btn-account" id="login" data-target="#login-register" data-toggle="modal"></a>
                        </li>
                    @endif
                    <li>
                        <a href="{{route('customer.cart')}}" class="btn-cart">
                            <span class="prod-count" id="cart_header">{{session()->get("cart_custom_total", '')}}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>