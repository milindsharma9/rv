<ul class="product_list">
    @if(empty($matchedProducts))
        <li class="empty-product-search">
            <img src="{{ url('alchemy/images') }}/broken-cycle.svg">No Product found.<br>Please search with different criteria.
        </li>
    @endif
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
            <a href="{{route('products.detail',$product['id'])}}" class="product-description-link"></a>
        </li>
    @endforeach
</ul>