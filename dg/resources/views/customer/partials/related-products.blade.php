@if(isset($relatedProducts))
<div class="related-products">
    <h3 class="title">Related Products</h3>
    <div class="container">
        <div class="row">
            <ul>
                @foreach($relatedProducts as $product)
                <li>
                    <div class="prod-image">
                        <a href="{!!route('products.detail',['productId' => $product->id ]) !!}">
                            <img src="{!! CommonHelper::getProductImage($product->id, true) !!}">
                        </a>
                    </div>
                    <div class="prod-desc">
                        <a href="{!!route('products.detail',['productId' => $product->id ]) !!}">
                            <span class="prod-name">{!! CommonHelper::formatProductDescription($product->description) !!}</span>
                            <span class="prod-quan">{!! $product->name !!}</span>
                            <span class="prod-price">{{Config::get('appConstants.currency_sign')}}{!! $product->price !!}</span>
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif