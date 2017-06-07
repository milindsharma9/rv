<div class="search-products customer-product-section hidden-xs" id="products-search-sli">
    @if(!empty($matchedProducts))
    @if(!isset($isAjax))
    <h3 class="section-title"><span>Products</span></h3>
    @endif
    <ul class="product_list">
        @foreach($matchedProducts as $product)
        <li>
            <div class="product-image">
                <a href="{!!route('products.detail', $product['id']) !!}">
                    <img src="{!! CommonHelper::getProductImage($product['id'], true) !!}">
                </a>
            </div>
            <div class="product-info">
                <a href="{!!route('products.detail', $product['id']) !!}">
                    <h3 class="product-name">{!! CommonHelper::formatProductDescription($product['description']) !!}</h3>
                </a>
                <a class="product-price" data-alcohol="{!! CommonHelper::formatProductDescription($product['description']) !!}" data-price="{{$product['price']}}" data-name="{{$product['name']}}" data-id="{{$product['id']}}">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($product['price'])}}</a>
            </div>
            <a data-alcohol="{!! CommonHelper::formatProductDescription($product['description']) !!}" data-price="{{$product['price']}}" data-name="{{$product['name']}}" data-id="{{$product['id']}}" class="product-add-cart">
                <span data-item-id="{{ $product['id'] }}" class="product_cart_count">{!! CommonHelper::getProductCartCount($product['id'], false) !!}</span>
            </a>
        </li>
        @endforeach
    </ul>
    @else
    <div class="post-listing-empty">
        <img src="{{ url('alchemy/images') }}/broken-cycle.svg">
        <h1>No Products Available.</h1>
    </div>
    @endif
    <div class="loading-search">
    </div>
</div>