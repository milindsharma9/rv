@extends('store.layouts.products')
@section('header')
My orders
@endsection
@section('content')
<section class="store-content-section order-type-template sales-order-listing-section">
    {!! Form::open(array('files' => false,  'id' => 'vendor-confirm', 'method' => 'POST','route' => array('store.vendorVerification'))) !!}
        @if(isset($orderNumber) && isset($orderItem))
        {!! csrf_field() !!}
	<div class="container">
		<div class="row">
			<div class="order-header">
                            @if(isset($orderNumber))
				<h3 class="title"><a href="{{ url()->previous() }}" class="btn-red">< Back</a> <span>Order #{!!$orderNumber!!}</span></h3>
                                @endif
			</div>
			<div class="product-list-group">
				<ul>
                                    <?php $totalItems = 0;?>
                                    @foreach($orderItem AS $key => $value)
					<li>
						<div class="product-image">
                                                    @if(isset($value->fk_product_id))
                                                        <img src="{!! CommonHelper::getProductImage($value->fk_product_id, true) !!}">
                                                    @else
                                                        <img src="{!! CommonHelper::getProductImage($value['fk_product_id'], true) !!}">
                                                    @endif
						</div>
						<div class="product-info">
							<h3 class="product-name">
                                                             @if(isset($value->description))
                                                            {!!CommonHelper::formatProductDescription($value->description)!!}
                                                            @else {!! CommonHelper::formatProductDescription($value['description'])!!}
                                                            @endif
                                                            </h3>
							<p class="product-quantity">
                                                           @if(isset($value->name))
                                                            {!!$value->name!!}
                                                        @else {!! $value['name']!!}
                                                        @endif
                                                        </p>
							<p class="product-price">{!! Config::get('appConstants.currency_sign') !!}
                                                            @if(isset($value->store_price)){!!CommonHelper::formatPrice($value->store_price)!!}
                                                        @else {!! CommonHelper::formatPrice($value['store_price'])!!}
                                                        @endif</p>
						</div>
                                            @if(isset($value->id_sales_order_item))
                                            <?php $id = $value->id_sales_order_item; ?>
                                            @else 
                                            <?php $id = $value['id_sales_order_item']; ?>
                                            @endif
                                            <?php $prodQuantity = 0; ?>
                                            @if(isset($value->quantity))
                                            <?php $quantity = $value->quantity; ?>
                                            @else 
                                            <?php $quantity = $value['quantity']; 
                                            if(isset($value['newQty']) && !empty($value['newQty'])){
                                                $prodQuantity = $value['newQty']; 
                                                $totalItems += $prodQuantity;
                                            }
                                            ?>
                                            @endif
						<div class="product-modify">
                                                    <button type="button" class="prod-remove" id="remove_{!! $id !!}" data-id="{!! $id !!}"></button>
							<span class="product-quan">
                                                            <span class="product-booked" id="dispatched_{!! $id !!}" data-id="{!! $id !!}">
                                                                {!! $prodQuantity !!}
                                                            </span>
                                                                /<span class="product-available">{!!$quantity!!}</span>
							</span>
							<button type="button" class="prod-add" id="add_{!! $id !!}" data-limit="{!!$quantity!!}" data-id="{!! $id !!}"></button>
						</div>
					</li>
                                        @endforeach
				</ul>
				<div class="total-price-wrap">
					<span>Total</span>
                                        <span class="total-price">{!! Config::get('appConstants.currency_sign') !!}{!!CommonHelper::formatPrice($totalPrice) !!}</span>
				</div>
			</div>
		</div>
	</div>
	<div class="stickyfooter action-buttons">
		<button id="confirmProduct">Continue<span class="product-count">{!! $totalItems !!} items</span></button>
	</div>
        @endif
        {!! Form::close() !!}
	<div id="error-popup" class="modal fade" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	        	<div class="modal-header">
                            {{ link_to_route('store.orderSearch', 
                                        '', '', 
                                        array('type' => 'button', 'class' => 'close'))}}
                </div>
	        	<div class="modal-body">
	        		<img src="{{ url('/alchemy/images') }}/broken-cycle.svg">
	        		<h2>Order number not found</h2>
	        		<p>This order number doesn’t exist.<br>
	        		Please verify the details and <br>try it again.</p>
	        	</div>
	        	<div class="modal-footer">
                            {{ link_to_route('store.orderSearch', 
                                        'Try Again', '', 
                                        array('type' => 'button', 'class' => 'try-again'))}}
	        	</div>
	        </div>
	    </div>
    </div>
</section>
@endsection
@section('javascript')
<script>
var updateSession = "{{ route('store.updateSession') }}";
var orderItem = <?php echo json_encode($orderItem); ?>;
</script>
<script src="{{ url('js') }}/vendorProduct.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
@stop
