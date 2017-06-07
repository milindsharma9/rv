@extends('store.layouts.products')
@section('header')
My Products
@endsection
@section('content')
<section class="store-content-section order-type-template sales-product-description-section">
	<div class="product-image hidden-xs">
		<div class="btn-desktop-back hidden-xs"><a href="{{ URL::previous() }}" class="btn-red">< Back</a></div>
		<div class="product-image-inner">
                    <img src="{!! CommonHelper::getProductImage($product->id) !!}">
		</div>
	</div>
	<div class="container">
		<div class="row hidden-xs add-basket-prod-info">
			<div class="col-sm-6">
				<div class="product-name">{{$product->description}}</div>
				<div class="product-quantity">{{$product->name}}</div>
			</div>
		</div>
		<div class="row">
			<div class="order-header visible-xs">
				<h3 class="title"><a href="{{ URL::previous() }}" class="btn-red">< Back</a></h3>
			</div>
			<div class="product-image visible-xs">
				<div class="product-image-inner">
					<img src="{!! CommonHelper::getProductImage($product->id) !!}">
				</div>
			</div>
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#prod-summary"><span>Summary</span></a></li>
				<li><a data-toggle="tab" href="#prod-specs"><span>Specifications</span></a></li>
			</ul>
			<div class="tab-content">
				<div id="prod-summary" class="tab-pane fade in active">
					<div class="product-quantity visible-xs">{{$product->description}}</div>
					<div class="product-info">{!!$product->product_marketing!!}</div>
				</div>
				<div id="prod-specs" class="tab-pane fade">
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
					                        @if($product->product_marketing != ' ')
					                        <span>{!!$product->product_marketing!!}</span><br>
					                        @endif
					                        @if($product->ingredients != ' ')
					                        <span><strong>Ingedrients </strong></span>
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
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
            <div class="table-responsive table-price-compare">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>RRSP</th>
							<th>You</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{{$product->store_price}}</td>
                                                        @if(isset($productInstock['vendor_price']))
                                                                                <?php $storePrice = $productInstock['vendor_price']; ?>
                                                                            @else
                                                                                <?php $storePrice = $product->store_price; ?>
                                                                            @endif
							<td>
								<div class="available-price-wrap">
									<span class="vendor-price">{{$storePrice}}</span>
									<button class="btn-red btn-edit">Edit</button>
								</div>
                                                            
								<div class="edit-price-wrap">
									<input type="hidden" name="product_id" value="{{$product->id}}">
                                                                        <input type="text" name="vendor_price" value="{{$storePrice}}">
									<button class="btn-red btn-confirm" id='updateVendorPrice'>Confirm</button>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="suggest-note">*If you are happy to use our recommended price (RRSP) please press confirm. If not, please click edit to enter a different price.</div>
			</div>
			<div class="stickyfooter">
                            <button id="have-in-stock">
				<span class="product-available">
                                    <?php
                                    (($productInstock['status']) ?  $checked = 'checked="checked"' : $checked = '');
                                    ?>
                                    @if ($allowProductUpload)
                                        <input type="checkbox" class="prod_check" <?php echo $checked; ?> data-id='{{$product->id}}'>
                                    @else
                                        <input type="checkbox" data-id='{{$product->id}}'>
                                    @endif
                                    <span class="marker"></span>
                                </span> I have in stock
                            </button>
			</div>
		</div>
	</div>
</section>
@endsection
@section('javascript')
<script>
    var storeSaveProductUrl     = "{!! route('store.products.save')!!}";
    var updateVendorPriceUrl     = "{!! route('store.updateprice')!!}";
</script>
<script type="text/javascript">
    
    var allowProductUpload = '<?php echo $allowProductUpload; ?>';
    
    $(document).on('click', '.prod_check', function () {
        var add         = $(this).is(":checked");
        var prodId      = $(this).data("id");
        $(this).addClass('loading');
        var element = $(this);
        $.ajax({
            url: storeSaveProductUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                prodId: prodId,
                add: add,
                _token: $('input[name=_token]').val()
            },
            success: function(result) {
                if (result.status) {
                    element.removeClass('loading');
                } else {
                    element.removeClass('loading');
                    alert(result.message);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
    });
    $(document).ready(function(){
        $('#have-in-stock').click(function() {
            $('.prod_check').trigger('click');
        });
        $('input[name=vendor_price]').keyup(function(){
            var $this = $(this);
            $this.val($this.val().replace(/[^\d.]/g, ''));
            $(this).val($(this).val().replace(/\//, ''));
            if (this.value.length > 3 && !/\./.test(this.value)) {
                this.value = this.value.slice(0, 3).concat(".00")
            }      
            if (/\./.test(this.value)) {
                var $val = this.value.split('.');
                this.value = $val[0].concat('.').concat($val[1].slice(0,2));
            }
        });
        $('#updateVendorPrice').click(function() {
            $(this).addClass('loading');
            $.ajax({
            url: updateVendorPriceUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                product_id: $('input[name=product_id]').val(),
                vendor_price: $('input[name=vendor_price]').val(),
                _token: $('input[name=_token]').val()
            },
            success: function(result) {
                if (result.status == true) {
                    $('span[class=vendor-price]').html(result.data.vendor_price);  
                }
                $(this).removeClass('loading');
                alert(result.message);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
        });
     });

</script>
@endsection