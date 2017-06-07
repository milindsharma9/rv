@section('title', 'Alchemy - ProductDetail')
@section('meta_description', $product->meta_description)
@section('meta_keywords', $product->meta_keywords)
@extends('customer.layouts.customer')
@section('content')
<section class="customer-content-section order-type-template customer-product-description-section">
	<div class="order-header">
		<a href="{{ URL::previous() }}" class="btn-red">< Back</a>
		@if(isset($product->serves))
        <span class="btn-quan-serve">{{CommonHelper::formatProductDescription($product->serves)}}</span>
        @endif
	</div>
	<div class="container">
		<div class="row">
			<div class="product-image">
                            <div class="product-image-inner">
                                <div class="product-carousel">
                                    <ul>
                                        @php
                                            $aProductImages = CommonHelper::getProductImage($product->id, false, true);
                                        @endphp
                                        @foreach($aProductImages as $productImage)
                                            <li class="items"><img src="{!! CommonHelper::getProductImageAbsolutePath($productImage['image']) !!}"></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="product-info-inner">
		            <div class="product-name">{{$product->description}}</div>
					<div class="product-quantity">{{$product->name}}</div>
					<!--<div class="product-rating">
						<span class="rating-stars"></span> 2 reviews
					</div>-->
					<a class="product-price" data-alcohol="{{CommonHelper::formatProductDescription($product->description)}}" data-price="{{$product->price}}" data-name="{{$product->name}}" data-id="{{$product->id}}">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($product->price)}}</a>
				</div>
			</div>
			<div class="product-add-cart-wrap col-xs-12">
				<button data-alcohol="{{CommonHelper::formatProductDescription($product->description)}}"
		                data-price="{{$product->price}}"
		                data-name="{{$product->name}}"
		                data-id="{{$product->id}}"
		                class="product-add-cart btn-primary-cart">
					Add to Basket
                                        <span data-item-id="{{ $product->id }}" class="product_cart_count">{!! CommonHelper::getProductCartCount($product->id, false) !!}</span>
				</button>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#prod-summary"><span>Summary</span></a></li>
				<li><a data-toggle="tab" href="#prod-specs"><span>Specifications</span></a></li>
			</ul>
			<div class="tab-content">
				<div id="prod-summary" class="tab-pane fade in active">
				<div class="product-info">
					{!!$product->product_marketing!!}
	                <div class="varietal-text">{!!$product->varietal!!}</div>
				</div>
				</div>
				<div id="prod-specs" class="tab-pane fade">
                    @if(!empty(trim($product->servings)))
                        <div class="product-serve">{!!$product->servings!!}</div>
                    @endif
					<h4>per 100 ml/g</h4>
					<ul class="product-measures">
						<li>
							<span class="elem-measure">
								Energy
								<span>{!!$product->energy!!}kcal</span>
							</span>
							<span class="elem-proportion elem-energy">
								{!!CommonHelper::getMacroDetails('energy', $product->energy);!!}
							</span>
						</li>
						<li>
							<span class="elem-measure">
								Fat
								<span>{!!$product->fat!!}g</span>
							</span>
							<span class="elem-proportion elem-fat">
								{!!CommonHelper::getMacroDetails('fat', $product->fat);!!}
							</span>
						</li>
						<li>
							<span class="elem-measure">
								Saturates
								<span>{!!$product->sat_fat!!}g</span>
							</span>
							<span class="elem-proportion elem-saturates">
								{!!CommonHelper::getMacroDetails('sat_fat', $product->sat_fat);!!}
							</span>
						</li>
						<li>
							<span class="elem-measure">
								Sugars
								<span>{!!$product->sugar!!}g</span>
							</span>
							<span class="elem-proportion elem-sugars">
								{!!CommonHelper::getMacroDetails('sugar', $product->sugar);!!}
							</span>
						</li>
						<li>
							<span class="elem-measure">
								Salt
								<span>{!!$product->salt!!}g</span>
							</span>
							<span class="elem-proportion elem-salt">
								{!!CommonHelper::getMacroDetails('salt', $product->salt);!!}
							</span>
						</li>
					</ul>
					<div class="product-info">
                        @if($product->lower_age_limit != ' ')
                        <span><strong>Lower Age Limit:</strong>{!!$product->lower_age_limit!!}</span><br>
                        @endif
                        @if($product->safety_warnings != ' ')
                        <span><strong>Safety Warnings:</strong>{!!$product->safety_warnings!!}</span><br>
                        @endif
                        @if($product->ingredients != ' ')
                        <span><strong>Ingredients </strong></span>
                        <span>{!!$product->ingredients!!}</span>
                        <br>
                        @endif
                        @if($product->allergy1 != ' ' || $product->allergy3 != ' ')
                        <span><strong>Allergy Information</strong></span><br>
                        @if($product->allergy1 != ' ')
                        <span>{!!$product->allergy1!!}</span><br>
                        @endif
                        @if($product->allergy2 != ' ')
                        <span>{!!$product->allergy2!!}</span><br>
                        @endif
                        @if($product->allergy3 != ' ')
                        <span>{!!$product->allergy3!!}</span><br>
                        @endif
                        @endif
                        <span><strong>EAN:</strong>{!!$product->barcode!!}</span>
					</div>	
				</div>
			</div>
			</div>
		</div>
			@include('customer.partials.related-occasion')
			@include('customer.partials.related-products')
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        <div class="explore-sub-occasion sub-occasion-home">
            <div class="explore-sub-occasion-wrap" id='explore-sub-occasion'>
                
            </div>
        </div>
</section>
@include('customer.partials.cart-modal')
@endsection

@section('javascript')
<script>
    var cartAddUrl              = "{!! route('customer.cart.add')!!}";
    var cartUpdateUrl           = "{!! route('customer.cart.update')!!}";
    var moodUrl                 = "{!! route('search.mood')!!}";
    var loadingImgUrl           = "{!! url('alchemy/images/loadingstock.gif')!!}";
    var occasionUrl             = "{!! route('search.occasion')!!}";
    var getSubOccasionUrl       = "{!! route('get.occasion', 1)!!}";
    getSubOccasionUrl           = getSubOccasionUrl.slice(0, -2);
    var checkCustomerCartStatusUrl             = "{!! route('customer.cart.status.check')!!}";
    var cartSetDeliveryPostcodeUrl             = "{!! route('customer.delivery.postcode.set')!!}";
    var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
</script>
<!--<script src="{{ url('alchemy/js') }}/product_cart.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>-->
<script>
    //to be used in Google Tag Manager
  dataLayer = [{
    'id': "{{$product->id}}",
    'name': '{{$product->name}}',
    'amount': '{{$product->price}}'
  }];
</script>
<script type="text/javascript" src="{{ url('alchemy/js') }}/verticalZoom.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.product-carousel ul').verticalZoom();
	})
</script>
@endsection