@extends('store.layouts.products')
@section('header')
Profile
@endsection
@section('content')
<section class="store-content-section store-profile">
	<div class="container">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
            </div>
            @endif
            @if ( session()->has('message') )
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            <div class="row">
                    <div class="col-xs-12 store-description">
                         <img src="{{ asset('alchemy/images/profile-banner.jpg') }}" class="store-banner-image">  
                        <div class="store-desc">
                            @if(isset($storeuserData->image) && $storeuserData->image != '')
                            <img src="{{ asset('uploads/store') . '/'.  $storeuserData->image }}" class="store-image">
                            @elseif ($storeuserData->image == '')
                            <img src="{{ asset('alchemy/images/default-store.png')}}" class="store-image">
                            @endif
                            <h3 class="store-name">
                                @if(isset($storeuserData->subStoreDetails->store_name))
                                <span class="hidden-xs">Welcome back,</span>
                                {!!$storeuserData->subStoreDetails->store_name!!}
                                @endif
                            </h3>
                        </div>
                    </div>
			<div class="store-info-links">
				<ul>
                    <li class="menu-profile-title hidden-xs">
                        <h3 class="title">My Profile</h3>
                    </li>
                                        <li class="menu-opening tree-view">
						<a href="#">My Store</a>
                                            <ul class="tree-child">
                                                <li class="menu-address">
                                                        <a href="{{route('store.editAddress')}}">My Address</a>
                                                </li>
                                                <li class="menu-opening">
                                                        <a href="{{route('store.time')}}">My Opening Times</a>
                                                </li>                                                
                                            </ul>
                                        </li>
                                        <li class="menu-contact tree-view">
						<a href="#">My Profile</a>
                                            <ul class="tree-child">
                                                <li class="menu-contact">
                                                        <a href="{{route('store.editProfile')}}">My Contact Details</a>
                                                </li>
                                                <li class="menu-change-pass">
                                                        <a href="{{route('store.changePassword')}}">Change Password</a>
                                                </li>					
                                                <li class="menu-payment">
                                                        <a href="{{route('store.bank')}}">My Payment Details</a>
                                                </li>
                                                <li class="menu-upload">
                                                        {!! link_to_route('store.kyc.register', trans('KYC') , "", array('class' => '')) !!}
                                                </li>
                                            </ul>
                                       </li>
                                        
                                  <li class="menu-legal tree-view">
						<a href="#">Legal</a>
                                            <ul class="tree-child">
                                                @foreach($userLegaldata as $legalPage)
                                                    <li>
                                                        <a href="{{route($legalPage->user_type.'.page', $legalPage->url_path)}}" >{{$legalPage->title}}</a>
                                                    </li>
                                                @endforeach
                                                <li>
<!--                                                    <a href="{{route('store.seller.agreement')}}" >Seller Participation Agreement</a>-->
                                                    <a href="#" >Seller Participation Agreement</a>
                                                    <ul class="tree-child" style="display: block;">
                                                        <li>
                                                            <a href="{{url('/terms/product_list.docx')}}" target="_blank">Schedule One: Product List</a>
                                                        </li>
                                                        <li class="menu-legal">
                                                            <a href="{!! url('terms/Mangopay Terms.pdf') !!}" target="_blank">Schedule Three: MangoPay Terms</a>
                                                        </li>
                                                    </ul>
                                                </li>
<!--                                                <li class="menu-legal tree-view">
                                                    <a href="#"  >Courier Framework Agreement</a>
                                                    <ul class="tree-child" style="display: block;">
                                                        <li>
                                                            <a href="{{route('store.courier.agreement')}}" >Schedule One: Call-Off Contract</a>
                                                        </li>                                                        
                                                    </ul>
                                                </li>
                                                <li>
                                                    <a href="{{route('search.legalterms')}}#terms-consumers" >Customer Terms and Conditions</a>
                                                </li>
                                                <li>
                                                    <a href="{{route('search.legalterms')}}#website-tnc" >Terms of Website Use</a>
                                                </li>
                                                <li>
                                                    <a href="{{route('search.legalterms')}}#acceptable-policy" >Acceptable Use Policy</a>
                                                </li> 
                                                <li class="menu-legal">
                                                    {!! link_to_route('search.cookies', trans('Cookies Policy') , "", array('class' => '')) !!}
                                                </li>
                                                <li class="menu-legal">
                                                    {!! link_to_route('search.privacypolicy', trans('Privacy Policy') , "", array('class' => '')) !!}
                                                </li>-->
                                                
                                            </ul>
					</li>
					<li class="menu-help">
						<a href="{{route('api.faq')}}">Help & FAQ's</a>
                                        </li>                    
				</ul>
			</div>
		</div>
	</div>
</section>
@endsection