<div class="header-top hidden-xs">
	<div class="row">
		<div class="store-timing col-xs-12 col-sm-8">
			@if((CommonHelper::checkForSiteTimings()))
			   {{ CommonHelper::getOfflineMessage()}}
			@else
			    {{ CommonHelper::getOnlineMessage()}}
			 @endif
		</div>
		<div class="top-navigation-links col-xs-12 col-sm-4">
			<ul>
                            <li>
                                @php
                                    $selectedPostcode = CommonHelper::getUserCartDeliveryPostcode();
                                    $postcodeClass = '';
                                @endphp
                                @if (!empty($selectedPostcode))
                                    @php
                                        $postcodeClass = 'active';
                                    @endphp
                                @endif
                                <a class="available-zipcode {{$postcodeClass}}" data-toggle="modal" data-target="#selected-location-popup">
                                    <span class="zipcode-location" id="topPostCode">{{$selectedPostcode}}</span>
                                </a>
                            </li>
				@if((Auth::user()))
                    <li>
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
		</div>
	</div>
</div>
