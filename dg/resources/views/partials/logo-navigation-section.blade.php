<header class="siteHeader">
    <div class="container">
        <div class="row">                
            <div class="col-xs-8 col-sm-3 col-md-3 logo">
                <a href="{{ route('home.index') }}"><img src="{{ url('alchemy/images') }}/logo.svg"></a>
            </div>
            <div class="col-xs-4 col-sm-9 col-md-9 login-direct">
                @if(!Auth::user())
                <button id="login-btn" data-target="#login-register" data-toggle="modal">Login</button>
                @else
                    @php
                        $route = 'home.index'
                        @endphp
                    @if(Auth::user()->fk_users_role == config('appConstants.admin_role_id'))
                        @php
                        $route = 'admin.dashboard'
                        @endphp
                    @elseif (Auth::user()->fk_users_role == config('appConstants.vendor_role_id'))
                        @php
                        $route = 'store.dashboard'
                        @endphp
                    @endif
                <a id="home-btn" href="{{ route($route) }}">Home</a>
                @endif
            </div>
        </div>
    </div>
    @include('customer.partials.selected-location-postcode')
</header>