<div class="tab-content tab-content-level-1">
    <div class="tab-pane fade in active" id="alcohol">
        <!-- Level 2 Tabs -->
        @if(!empty($aSubCats))
            <ul class="nav nav-tabs tab-level-2">
                @foreach($aSubCats as $subcat)
                    @if($subcat['id'] == $selsubCatId)
                        <li class='active'>
                    @else
                        <li>
                    @endif
                        <a data-intercom-event="click_sub_category_{{$subcat['name']}}" href="#{{$subcat['name']}}" data-toggle="tab" onclick="getSubSubCat('{{$subcat['id']}}','{{$subcat['name']}}')"><span>{{$subcat['name']}}</span></a>
                    </li>
                @endforeach
            </ul>
        @endif
        <div id="subcontent">
            @if($subcatDetails['haSubCat'])
                <ul class="product-group">
                    @foreach($subcatDetails['products'] as $subCatProducts)
                        <li><!-- Repeat For Subcat -->
                            <a><span class="product-group-name">{{$subCatProducts['name']}}</span><span class="product-group-availability">{{count($subCatProducts['products'])}} products</span></a>
                            @if(!empty($subCatProducts['products']))
                                <ul style="">
                                    @foreach($subCatProducts['products'] as $products)
                                        <li><!-- Repeat For Product -->
                                            <div class="product-image">
                                                <a href="{!!route('products.detail',['productId' => $products->id ]) !!}">
                                                    <img src="{!! CommonHelper::getProductImage($products->id) !!}">
                                                </a>
                                            </div>
                                            <div class="product-info">
                                                <a href="{!!route('products.detail',['productId' => $products->id ]) !!}"><h3 class="product-name">{!! CommonHelper::formatProductDescription($products->description) !!}</h3>
                                                    </a>
                                                <a data-alcohol="{!! CommonHelper::formatProductDescription($products->description) !!}" data-price="{{$products->price}}" data-name="{{$products->name}}" data-id="{{$products->id}}" class="product-price">{{config('appConstants.currency_sign')}}{{$products->price}}</a>
                                            </div>
                                            <a data-alcohol="{!! CommonHelper::formatProductDescription($products->description) !!}" data-price="{{$products->price}}" data-name="{{$products->name}}" data-id="{{$products->id}}" class="product-add-cart">
                                                <span data-item-id="{{ $products->id }}" class="product_cart_count">{!! CommonHelper::getProductCartCount($products->id, false) !!}</span>
                                            </a>
                                            <a href="{!!route('products.detail',['productId' => $products->id ]) !!}" class="product-description-link"></a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class='product-unavailable'><h3>{{$subCatName}}</h3>No Product Found.</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                @if(!empty($subcatDetails['products']))
                    <ul class="product_list">
                        @foreach($subcatDetails['products'] as $products)
                            <li><!-- Repeat For Product -->
                                <div class="product-image">
                                    <a href="{!!route('products.detail',['productId' => $products->id ]) !!}">
                                        <img src="{!! CommonHelper::getProductImage($products->id) !!}">
                                    </a>
                                </div>
                                <div class="product-info">
                                    <a href="{!!route('products.detail',['productId' => $products->id ]) !!}"><h3 class="product-name">{!! CommonHelper::formatProductDescription($products->description) !!}</h3>
                                        </a>
                                    <a data-alcohol="{!! CommonHelper::formatProductDescription($products->description) !!}" data-price="{{$products->price}}" data-name="{{$products->name}}" data-id="{{$products->id}}" class="product-price">{{config('appConstants.currency_sign')}}{{$products->price}}</a>
                                </div>
                                <a data-alcohol="{!! CommonHelper::formatProductDescription($products->description) !!}" data-price="{{$products->price}}" data-name="{{$products->name}}" data-id="{{$products->id}}" class="product-add-cart">
                                    <span data-item-id="{{ $products->id }}" class="product_cart_count">{!! CommonHelper::getProductCartCount($products->id, false) !!}</span>
                                </a>
                                <a href="{!!route('products.detail',['productId' => $products->id ]) !!}" class="product-description-link"></a>
                            </li>
                        @endforeach
                    </ul>
                @else
                <div class='product-unavailable'><h3>{{$subCatName}}</h3>No Product Found.</div>
                @endif
            @endif
        </div>
    </div>
</div>