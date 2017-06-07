@foreach($cartViewData as $cartItem)
@if(isset($cartItem['bundleId']))
<li class="is-basket-order">
    <div class="store-info">
        <h3>{{$cartItem['store_name']}}</h3>
        <p>{{$cartItem['store_address']}}</p>
    </div>
    <div class="col-left">
        <div class="product-name">{{$cartItem['bundleName']}}</div>
        <div class="product-price">{!! Config::get('appConstants.currency_sign') !!}<span id="bundleDefaultPrice_{{$cartItem['rowid']}}">{{CommonHelper::formatPrice($cartItem['bundleDefaultTotalPrice'])}}</span></div>
    </div>
    <div class="col-right">
        <div class="product-quantity">
            <span class="product-modify">
                <button class="prod-remove bundle-cart-remove" data-id="{{$cartItem['rowid']}}"></button>
                <input type="text" class="product-quan" id="bundleQuantity_{{$cartItem['rowid']}}" value="{{$cartItem['bundleQty']}}" readonly>
                <button class="prod-add bundle-cart-add" data-id="{{$cartItem['rowid']}}"></button>
            </span>
            <span class="single-total">Total <span class="price">{!! Config::get('appConstants.currency_sign') !!} <span id="bundleTotalPrice_{{$cartItem['rowid']}}">{{CommonHelper::formatPrice($cartItem['bundleTotalPrice'])}}</span></span></span>
        </div>
    </div>
    <div id="bundle_product_count_div_{{$cartItem['rowid']}}">
        <ul class="bucket-product">
            @foreach($cartItem['bundleProducts'] as $bundleProducts)
            <li>
                <div class="col-left">
                    <div class="product-name">{{$bundleProducts['options']['0']['alcohol_content']}}</div>
                    <div class="product-desc">{{$bundleProducts['name']}}</div>
                    <div class="product-price">{!! Config::get('appConstants.currency_sign') !!}<span id=''>{{CommonHelper::formatPrice($bundleProducts['price'])}}</span></div>
                </div>
                <span class="info-icon" data-store_name="{{$bundleProducts['store_name']}}"
                      data-store_address="{{$bundleProducts['store_address']}}"
                      data-store_postcode="{{$bundleProducts['store_postcode']}}"></span>
                <div class="store-info">
                    <h3>{{$bundleProducts['store_name']}}</h3>
                    <p>{{$bundleProducts['store_address']}}</p>
                </div>
                <div class="col-right">
                    <div class="product-quantity">
                        <span class="product-modify">
                            <input type="text" class="product-quan" id="bundle_product_count_{{$cartItem['rowid']}}_{{$bundleProducts['id']}}" value="{{$bundleProducts['qty']}}" readonly>
                            <input type="hidden" class="product-quan-default"
                                   data-id="{{$cartItem['rowid']}}_{{$bundleProducts['id']}}"
                                   value="{{$bundleProducts['bundleDefaultProductQuantity']}}" >
                        </span>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    <a href="#" class="remove-product glyphicon glyphicon-remove" data-id="{{$cartItem['rowid']}}"></a>
</li>
@else
<li>
    <span class="info-icon" data-store_name="{{$cartItem['store_name']}}"
          data-store_address="{{$cartItem['store_address']}}"
          data-store_postcode="{{$cartItem['store_postcode']}}"></span>
    <div class="store-info">
        <h3>{{$cartItem['store_name']}}</h3>
        <p>{{$cartItem['store_address']}}</p>
    </div>
    <div class="col-left">
        <div class="product-name">{{$cartItem['options']['0']['alcohol_content']}}</div>
        <div class="product-desc">{{$cartItem['name']}}</div>
        <div class="product-price">{!! Config::get('appConstants.currency_sign') !!}<span id="productDefaultPrice_{{$cartItem['rowid']}}">{{CommonHelper::formatPrice($cartItem['price'])}}</span></div>
    </div>
    <div class="col-right">
        <div class="product-quantity">
            <span class="product-modify">
                <button class="prod-remove product-cart-remove" data-id="{{$cartItem['rowid']}}"></button>
                <input type="text" readonly class="product-quan" id="productQuantity_{{$cartItem['rowid']}}" value="{{$cartItem['qty']}}">
                <button class="prod-add product-cart-add" data-id="{{$cartItem['rowid']}}"></button>
            </span>
            <span class="single-total">Total <span class="price">{!! Config::get('appConstants.currency_sign') !!}<span id="productTotalPrice_{{$cartItem['rowid']}}">{{CommonHelper::formatPrice($cartItem['subtotal'])}}</span></span></span>
        </div>
    </div>
    <a href="#" class="remove-product glyphicon glyphicon-remove" data-id="{{$cartItem['rowid']}}"></a>
</li>
@endif
@endforeach
@if(!empty($cartViewData))
<li class="drive-charge" id="driver_charges_li">
    @include('customer.partials.delivery_charges')
</li>
@endif
@if(!empty($appliedCoupon))
<li class="promo-applied">
    <div class="promo-applied">Promocode{{$appliedCoupon['coupon_code']}} <span class="promo-deduction">{{CommonHelper::getDiscountSymbol($appliedCoupon['discount_type'], $appliedCoupon['discount_amount'])}}</span></div>
</li>
@endif
