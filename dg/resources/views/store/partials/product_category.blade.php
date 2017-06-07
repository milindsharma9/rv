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
                        <a href="#{{$subcat['name']}}" data-toggle="tab" onclick="getSubSubCat('{{$subcat['id']}}','{{$subcat['name']}}')"><span>{{$subcat['name']}}</span></a>
                    </li>
                @endforeach
            </ul>
        @endif
        <div id="subcontent">
            @if($subcatDetails['haSubCat'])
                <ul class="product-group">
                    @foreach($subcatDetails['products'] as $subCatProducts)
                        @php
                            $targetId = strtolower(str_replace(array(" ", "(", ")"), array("_", "", ""), $subCatProducts['name']));
                        @endphp
                        <li><!-- Repeat For Subcat -->
                            <a><span class="product-group-name">{{$subCatProducts['name']}}</span><span class="product-group-availability"><span id='subsubcat_{{$targetId}}'>2</span>/{{count($subCatProducts['products'])}} products</span></a>
                            @if(!empty($subCatProducts['products']))
                                <ul style="">
                                    @foreach($subCatProducts['products'] as $products)
                                        @php
                                            $checked = '';
                                            $storePrice = $products->store_price;
                                        @endphp
                                        @if (isset($storeProducts[$products->id]))
                                            @php
                                                $checked = 'checked="checked"';
                                                $storePrice = $storeProducts[$products->id]['price'];
                                            @endphp
                                        @endif
                                        <li>
                                            <span class='product-available'><input {{$checked}} type='checkbox' data-subcat="{{$subcatDetails['haSubCat']}}" data-subcatname="{{$targetId}}" id='prod_check_{{$products->id}}' data-id="{{$products->id}}" class='prod_check' />
                                                <span class='marker' id='prod_check_span_{{$products->id}}_{{$targetId}}'></span>
                                            </span>
                                            <div class='product-image'>
                                                    <a href="{!!route('store.products.detail',['productId' => $products->id ]) !!}">
                                                    <img src="{!! CommonHelper::getProductImage($products->id) !!}"></a>
                                            </div>
                                            <div class='product-info'>
                                                    <a href="{!!route('store.products.detail',['productId' => $products->id ]) !!}"><h3 class='product-name'>{!! CommonHelper::formatProductDescription($products->description) !!}</h3>
                                                    <p class='product-quantity'>EAN:{!! $products->barcode !!}</p></a>
                                            </div>
                                            <a href="{!!route('store.products.detail',['productId' => $products->id ]) !!}" class='product-description-link'>
                                            RRSP: <span>{!! $products->store_price !!}</span><br>
                                            YOU: <span>{!! $storePrice !!}</span><br>
                                            </a>
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
                            @php
                                $targetId = null;
                            @endphp
                            @php
                                $checked = '';
                                $storePrice = $products->store_price;
                            @endphp
                            @if (isset($storeProducts[$products->id]))
                                @php
                                    $checked = 'checked="checked"';
                                    $storePrice = $storeProducts[$products->id]['price'];
                                @endphp
                            @endif
                           <li>
                               <span class='product-available'><input {{$checked}} type='checkbox' data-subcat="{{$subcatDetails['haSubCat']}}" data-subcatname="{{$targetId}}" id='prod_check_{{$products->id}}' data-id="{{$products->id}}" class='prod_check' />
                                   <span class='marker' id='prod_check_span_{{$products->id}}_{{$targetId}}'></span>
                               </span>
                               <div class='product-image'>
                                       <a href="{!!route('store.products.detail',['productId' => $products->id ]) !!}">
                                       <img src="{!! CommonHelper::getProductImage($products->id) !!}"></a>
                               </div>
                               <div class='product-info'>
                                       <a href="{!!route('store.products.detail',['productId' => $products->id ]) !!}"><h3 class='product-name'>{!! CommonHelper::formatProductDescription($products->description) !!}</h3>
                                       <p class='product-quantity'>EAN:{!! $products->barcode !!}</p></a>
                               </div>
                               <a href="{!!route('store.products.detail',['productId' => $products->id ]) !!}" class='product-description-link'>
                               RRSP: <span>{!! $products->store_price !!}</span><br>
                               YOU: <span>{!! $storePrice !!}</span><br>
                               </a>
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