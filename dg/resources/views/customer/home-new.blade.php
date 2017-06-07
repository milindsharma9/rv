@section('title', 'Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@section('title')
Alchemy - Home
@endsection
@extends('customer.layouts.customer')
@section('content')
    <section class="home-categories-mobile category-wrap visible-xs">
        <ul class="category-links">
            <?php $i = 1; ?>
            @foreach($catTree['categories'] as $catId => $aCat)
            @php 
            $formatcatName = CommonHelper::formatCatName($aCat['name']);
            @endphp
            <li>
                <a href="{{route('customer.products', ['catname' => $formatcatName, 'id' => $catId])}}">
                    @if(isset($aCat['image']))
                    <img src="{{ asset('uploads/categories') . '/'.  $aCat['image'] }}">
                    @endif
                    <span>{{$aCat['name']}}</span>
                </a>
            </li>
            <?php $i++; ?>
            @endforeach
        </ul>
    </section>
	<section class="home-banner">
		<div class="row">
			<div class="col-xs-12">
                            @php
                                $bannerImageDir = config('banner.banner_image_dir');
                            @endphp
				<img src="{{ asset('uploads/'.$bannerImageDir.'') . '/'.  $bannerImage }}" alt="Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery">
			</div>
		</div>
	</section>
	<section class="shop-by">
		<div class="row">
			<div class="col-xs-12">
				<h3 class="section-title"><span>Shop By</span></h3>
				<ul class="nav nav-tabs tab-level-1 shop-by-category">
			        <li class="active" ><a data-toggle="tab" data-target="#popular"><span>Popular</span></a></li>
			        <li><a data-toggle="tab" data-target="#recipes"><span>Recipes</span></a></li><li><a data-toggle="tab" data-target="#bundles"><span>Bundles</span></a></li>
			        <li><a data-toggle="tab" data-target="#gifts"><span>Gifts</span></a></li>
			    </ul>
			    <div id="shop-by-product-list">
                                <div class="container">
                                    <div class="row">
                                        <div class="tab-content clearfix">
                                            <div class="product-group tab-pane active" id="popular">
                                                @if(empty($shopByData['popular']['products'])
                                                    && empty($shopByData['popular']['bundles']))
                                                    <div class="items-unavailable">No Item Available</div>
                                                @else
                                                    @foreach($shopByData['popular']['products'] as $popularProduct)
                                                        <div class="product-item">
                                                            <div class="product-thumb">
                                                                <a href="{!!route('products.detail',['productId' => $popularProduct->id ]) !!}"><img src="{!! CommonHelper::getProductImage($popularProduct->id, true) !!}" alt="{{$popularProduct->name}}"></a>
                                                            </div>
                                                            <div class="product-info">
                                                                <h3>
                                                                    <a href="{!!route('products.detail',['productId' => $popularProduct->id ]) !!}">{!! CommonHelper::formatProductDescription($popularProduct->description) !!} 
                                                                        {{config('appConstants.currency_sign')}}{!! $popularProduct->price !!}
                                                                    </a>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @if(isset($shopByData['popular']['bundles']))
                                                        @foreach($shopByData['popular']['bundles'] as $popularBundles)
                                                        @php $image = $popularBundles->image_thumb ;@endphp
                                                        @if($popularBundles->image_thumb == '')
                                                        @php $image = $popularBundles->image ;@endphp
                                                        @endif
                                                            <div class="product-item">
                                                                <div class="product-thumb">
                                                                    <a href="{!!route('customer.bundleDetail',['productId' => $popularBundles->id ])!!}"><img src="{!! CommonHelper::getBundleImage($image, true) !!}" alt="{{$popularBundles->image}}"></a>
                                                                </div>
                                                                <div class="product-info">
                                                                    <h3>
                                                                        <a href="{!!route('customer.bundleDetail',['productId' => $popularBundles->id ])!!}">
                                                                        {!! CommonHelper::formatProductDescription($popularBundles->name) !!} 
                                                                        {{config('appConstants.currency_sign')}}{!! $popularBundles->price !!}
                                                                    </a>
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="product-group tab-pane" id="recipes">
                                                @if(empty($shopByData['recipe']))
                                                    <div class="items-unavailable">No Item Available</div>
                                                @else
                                                    @foreach($shopByData['recipe'] as $recipes)
                                                        @php $image = $recipes->image_thumb ;@endphp
                                                        @if($recipes->image_thumb == '')
                                                        @php $image = $recipes->image ;@endphp
                                                        @endif
                                                        <div class="product-item">
                                                            <div class="product-thumb">
                                                                <a href="{!!route('customer.bundleDetail',['productId' => $recipes->id ])!!}"><img src="{!! CommonHelper::getBundleImage($image, true) !!}" alt="{{$recipes->name}}"></a>
                                                            </div>
                                                            <div class="product-info">
                                                                <h3>
                                                                    <a href="{!!route('customer.bundleDetail',['productId' => $recipes->id ])!!}">
                                                                        {!! CommonHelper::formatProductDescription($recipes->name) !!} 
                                                                        {{config('appConstants.currency_sign')}}{!! $recipes->price !!}
                                                                    </a>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="product-group tab-pane" id="bundles">
                                                @if(empty($shopByData['bundle']))
                                                    <div class="items-unavailable">No Item Available</div>
                                                @else
                                                    @foreach($shopByData['bundle'] as $bundles)
                                                        @php $image = $bundles->image_thumb ;@endphp
                                                        @if($bundles->image_thumb == '')
                                                            @php $image = $bundles->image ;@endphp
                                                        @endif
                                                        <div class="product-item">
                                                            <div class="product-thumb">
                                                                <a href="{!!route('customer.bundleDetail',['productId' => $bundles->id ])!!}"><img src="{!! CommonHelper::getBundleImage($image, true) !!}" alt="{{$bundles->name}}"></a>
                                                            </div>
                                                            <div class="product-info">
                                                                <h3>
                                                                    <a href="{!!route('customer.bundleDetail',['productId' => $bundles->id ])!!}">
                                                                        {!! CommonHelper::formatProductDescription($bundles->name) !!} 
                                                                        {{config('appConstants.currency_sign')}}{!! $bundles->price !!}
                                                                    </a>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="product-group tab-pane" id="gifts">
                                                @if(empty($shopByData['gift']['products'])
                                                    && empty($shopByData['gift']['bundles']))
                                                    <div class="items-unavailable">No Item Available</div>
                                                @else
                                                    @foreach($shopByData['gift']['products'] as $giftProduct)
                                                        <div class="product-item">
                                                            <div class="product-thumb">
                                                                <a href="{!!route('products.detail',['productId' => $giftProduct->id ]) !!}"><img src="{!! CommonHelper::getProductImage($giftProduct->id, true) !!}" alt="{{$giftProduct->name}}"></a>
                                                            </div>
                                                            <div class="product-info">
                                                                <h3>
                                                                    <a href="{!!route('products.detail',['productId' => $giftProduct->id ]) !!}">
                                                                        {!! CommonHelper::formatProductDescription($giftProduct->description) !!} 
                                                                        {{config('appConstants.currency_sign')}}{!! $giftProduct->price !!}
                                                                    </a>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @if(isset($shopByData['gift']['bundles']))
                                                        @foreach($shopByData['gift']['bundles'] as $giftBundles)
                                                        @php $image = $giftBundles->image_thumb ;@endphp
                                                        @if($giftBundles->image_thumb == '')
                                                        @php $image = $giftBundles->image ;@endphp
                                                        @endif
                                                            <div class="product-item">
                                                                <div class="product-thumb">
                                                                    <a href="{!!route('customer.bundleDetail',['productId' => $giftBundles->id ])!!}"><img src="{!! CommonHelper::getBundleImage($image, true) !!}" alt="{{$giftBundles->name}}"></a>
                                                                </div>
                                                                <div class="product-info">
                                                                    <h3>
                                                                        <a href="{!!route('customer.bundleDetail',['productId' => $giftBundles->id ])!!}">{!! CommonHelper::formatProductDescription($giftBundles->name) !!} 
                                                                            {{config('appConstants.currency_sign')}}{!! $giftBundles->price !!}
                                                                        </a>
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
			    </div>
			</div>
		</div>
	</section>
	<section class="explore-occasion">
            <div class="row">
                <div class="col-xs-12">
                    <h3 class="section-title"><span>Explore</span></h3>
                    <div class="explore-list">
                        <?php $x = 1; ?>
                        @foreach($primaryOccasion as $occasion)
                            <div class="explore-item" data-occasion-id="{!! $occasion['id'] !!}" data-occasion-name="{!! $occasion['name'] !!}">
                                <img class="bgImg" src="{{ url('uploads/occasions') }}/{{$occasion['image']}}">
                                <div class="explore-desc">
                                    <div class="desc-icon"><img src="{{ url('uploads/occasions') }}/{{$occasion['image_logo']}}"></div>
                                    <h3>{{$occasion['name']}}</h3>
                                </div>
                            </div>
                        @if($x >= 10)
                            @break;
                        @endif
                       <?php  $x++; ?>
                        @endforeach
                    </div>
                </div>
            </div>
	</section>
        <div class="explore-sub-occasion sub-occasion-home">
            <div class="explore-sub-occasion-wrap" id='explore-sub-occasion'>
                
            </div>
        </div>
@endsection
@section('footer-scripts')
<script>
    var moodUrl                 = "{!! route('search.mood')!!}";
    var loadingImgUrl           = "{!! url('alchemy/images/loadingstock.gif')!!}";
    var occasionUrl             = "{!! route('search.occasion')!!}";
    var getSubOccasionUrl       = "{!! route('get.occasion', 1)!!}";
    getSubOccasionUrl           = getSubOccasionUrl.slice(0, -2);
</script>
@endsection