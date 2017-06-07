@section('title', 'Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@section('title')
Alchemy - Dashboard
@endsection
@extends('customer.layouts.customer')
@section('content')
	<section class="user-profile-info">
		<div class="user-cover" style="background-image: url({{ asset('alchemy/images/profile-banner.jpg')}})"></div>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 user-description">
		            <div class="user-description-wrapper">
		                {!! Form::open(array('files' => true,  'id' => 'image-upload', 'method' => 'POST','route' => array('customer.uploadImage'))) !!}
		                <span class="action-pic-upload">
		                    <input type="file" onchange="this.form.submit()" name="image">
		                </span>
		                {!! Form::hidden('image_w', 8192) !!}
		                {!! Form::hidden('image_h', 8192) !!}
		                {!! Form::close() !!}
		                @if(isset($userData->image) && $userData->image != '')
		                <div style="background-image: url({{ asset('uploads/'.$fileSubDir) . '/'. $userData->image }});" class="user-image"></div>
		                @elseif ($userData->image == '')
		                <div style="background-image: url({{ asset('alchemy/images/default-store.png')}})" class="user-image"></div>
		                @endif
		                <h3 class="user-name">
		                    <span class="hidden-xs">Welcome back,</span> {!! $userData['first_name'] !!} {!! $userData['last_name'] !!}
		                </h3>
		            </div>
		        </div>
			</div>
		</div>
	</section>
	<section class="user-orders">
		<h3 class="section-title"><span>My Orders</span></h3>
		<div class="container">
            <div class="row">
				<div class="col-xs-12 latest-order">
		            @if(isset($history))
		            <ul>
		                @foreach($history AS $key => $value)
		                <li class="item">
		                    <div class="order-info">
		                        <span class="orderId">#{!! $value['orderId'] !!}</span>
		                        <span class="orderCount">{!! $value['quantity'] !!} items</span>
		                        <span class="orderCost">{!! Config::get('appConstants.currency_sign') !!}{!! CommonHelper::formatPrice($value['totalPrice']) !!}</span>
		                    </div>
		                    <div class="order-thumb">
		                        @foreach($value['productDetail'] AS $key => $productDetail)
		                        <span><img src="{!! CommonHelper::getProductImage($productDetail->id, true) !!}"></span>
		                        @endforeach
		                    </div>
		                    <a href="{{url('customer/trackorder', [$value['orderId']])}}" class="order-summary-link"></a>
		                </li>
		                @endforeach
		            </ul>
		            @endif
		        </div>
        	</div>
        </div>
	</section>
	<section class="user-account-setting">
		<h3 class="section-title"><span>My Account</span></h3>
        <div class="container">
            <div class="row">
                <div class="store-info-links">
                    <ul>
                        <li class="menu-contact tree-view">
                            <a>My Contact Details</a>
                            <div class="tree-child form-group-wrap">
                            	<form>
                            		<div class="row">
                            			<div class="col-xs-12">
                            				<div class="alert alert-danger">test error</div>
                            			</div>
                            			<div class="col-xs-12 col-sm-6">
                            				<div class="form-group">
												<label>First Name*</label>
												<input type="text" placeholder="First Name">
											</div>
											<div class="form-group">
												<label>Last Name*</label>
												<input type="text" placeholder="Last Name">
											</div>
    									</div>
    									<div class="col-xs-12 col-sm-6">
                      						<div class="form-group">
												<label>Email</label>
												<input type="text" placeholder="Email">
											</div>
											<div class="form-group">
												<label>Phone</label>
												<input type="text" placeholder="Phone Number">
											</div>
    									</div>
    									<div class="col-xs-12">
    										<input type="submit" name="" value="Save Details">
    									</div>
                            		</div>
                            	</form>
                            </div>
                        </li>
                        <li class="menu-address tree-view">
                            <a>My Delivery Address</a>
                            <div class="tree-child form-group-wrap">
                            	<form>
                            		<div class="row">
                            			<div class="col-xs-12 col-sm-6">
                            				<div class="form-group">
												<label>Address</label>
												<input type="text">
											</div>
											<div class="form-group">
												<label>Country</label>
												<input type="text">
											</div>
    									</div>
    									<div class="col-xs-12 col-sm-6">
                      						<div class="form-group">
												<label>Town Name</label>
												<input type="text">
											</div>
											<div class="form-group">
												<label>Postcode</label>
												<input type="text">
											</div>
    									</div>
    									<div class="col-xs-12">
    										<input type="submit" name="" value="Save Details">
    									</div>
                            		</div>
                            	</form>
                            </div>
                        </li>
                        <li class="menu-change-pass tree-view">
                            <a>Change Password</a>
                            <div class="tree-child form-group-wrap">
                            	<form>
                            		<div class="row">
                            			<div class="col-xs-12 col-sm-6">
                            				<div class="form-group">
												<label>New Password*</label>
												<input type="text">
											</div>
    									</div>
    									<div class="col-xs-12 col-sm-6">
                      						<div class="form-group">
												<label>Confirm Password*</label>
												<input type="text">
											</div>
    									</div>
    									<div class="col-xs-12">
    										<input type="submit" name="" value="Save Details">
    									</div>
                            		</div>
                            	</form>
                            </div>
                        </li>
                        <li class="menu-payment tree-view">
                            <a>My Payment Method</a>
                            <div class="tree-child payment-options">
                            	<div class="card-view">
		                            <div class="card-info">
		                            	<span class="icon-payment"></span>
			                            <span class="card-cont">VISA ending with 0009</span>
			                            <a href="#">Change Payment</a>
		                            </div>
		                            <div class="card-info">
		                            	<span class="icon-payment"></span>
			                            <span class="card-cont">VISA ending with 0009</span>
			                            <a href="#">Change Payment</a>
		                            </div>
		                            <div class="card-info add-payment-card">
		                            	<span class="icon-payment"></span>
		                            	<span class="card-cont"></span>
			                            <a href="#">Add Payment</a>
		                            </div>
                            	</div>	
                            </div>
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
                                    <?php $checked = ''; 
                                    $class = ''; ?>
                                    @if(isset($subscribe) && $subscribe == 1)
                                    <?php $checked = "checked='checked'";
                                    $class = 'checked'; ?>
                                    @endif
                                    <a>
                                        <label class="check-option {{$class}}">
                                            <input name="suscribe" {{$checked}} type="checkbox" value="suscribed" id="suscribe">  I agree to be contacted for direct marketing purposes.
                                        </label>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-help">
                            <a href="{!! route('api.faq') !!}">Help & FAQs</a>
                        </li>
                        <li class="menu-logout">
                            <a href="{!! url('/logout') !!}">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer-scripts')
<script>
    var subscribeUrl = "{{ route('customer.update.subscribe') }}";
    var checkedStatus ;
    $(function () {
        $('input[name=suscribe]').on('change', function () {
            if($(this).is(":checked")){
                checkedStatus = 1;
            } else {
                checkedStatus = 0;
            }
                $.ajax({
                    url: subscribeUrl,
                    type: 'POST',
                    data: {is_subscribed: checkedStatus},
                    headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                    success: function (data) {
                        if (!data)
                            alert("some error occured please try again!!");
                    },
                    error: function (data) {
                        alert("some error occured please try again!!");
                    }
                });
        });
    });
</script>
@endsection