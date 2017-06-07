@extends('store.layouts.products')
@section('header')
My Dashboard
@endsection
@section('content')
<section class="store-content-section section-store-dashboard">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 store-description">
                                <img src="{{ asset('alchemy/images/profile-banner.jpg') }}" class="store-banner-image">
				<div class="store-desc">
                                    @if(isset($storeuserData->image) && $storeuserData->image != '')
                                    <img src="{{ asset('uploads/store') . '/'.  $storeuserData->image }}" class="store-image">
                                    @elseif ($storeuserData->image == '')
                                    <img src="{{ asset('alchemy/images/default-store.png')}}" class="store-image">
                                    @endif
					{{ link_to_route('store.profile', 'My Profile','', array('class' => 'btn-black'))}}
					<h3 class="store-name">
						@if(isset($storeuserData->subStoreDetails->store_name))
							<span class="hidden-xs">Welcome back,</span>
		                    {!!$storeuserData->subStoreDetails->store_name!!}
	                    @endif
		            </h3>
				</div>
			</div>
			<div class="container">
				<div class="col-xs-12 col-sm-6 store-order">
					<h3 class="title">My Orders						
					</h3>
					<div class="order-listing">
						<ul>
							<li>
								<img src="{{ url('alchemy/images') }}/live-order.png" class="image-order-listing">
								<a href="{{ route('store.orderSearch') }}"><div class="order-count">
									LIVE ORDERS</div></a>
							</li>
							<li>
								<img src="{{ url('alchemy/images') }}/order-history.png" class="image-order-listing">
								<a href="{{ url('store/orderSearch#order-history-wrap') }}"><div class="order-count">
			                    ORDER HISTORY</div></a>
							</li>
						</ul>
						{{ link_to_route('store.orderSearch', 'See my orders','#order-history-wrap', array('class' => 'btn-red hidden-xs'))}}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 store-sales">
					<h3 class="title">My Sales			                           
					</h3>
					<div class="sales-listing">
						<ul>
							<li class="sales-today">
                              <a href="{{ route('store.sales') }}">
								<img src="{{ url('alchemy/images') }}/daywise-sales.png" class="image-sales-listing">                                
								<div class="sales-count">
									{{Config::get('appConstants.currency_sign')}}{{$storeSalesData['today_sales']}}<span>Today</span>
                                </div>
                              </a>
							</li>
							<li class="sales-last-week">
                              <a href="{{ route('store.sales') }}">
								<img src="{{ url('alchemy/images') }}/daywise-sales.png" class="image-sales-listing">
								<div class="sales-count">
									{{Config::get('appConstants.currency_sign')}}{{$storeSalesData['last_week_sales']['week_total']}}<span>Last Week</span>
								</div>
                               </a>
							</li>
							<li class="sales-last-month">
                              <a href="{{ route('store.sales') }}">
								<img src="{{ url('alchemy/images') }}/daywise-sales.png" class="image-sales-listing">
								<div class="sales-count">
									{{Config::get('appConstants.currency_sign')}}{{$storeSalesData['last_month_sales']}}<span>Last Month</span>
								</div>
                               </a>
							</li>
						</ul>
						{{ link_to_route('store.sales', 'See my sales','', array('class' => 'btn-red hidden-xs'))}}
					</div>
				</div>
			</div>
			
		</div>
	</div>
	<div class="col-xs-12 store-products">
		<div class="container">
			<div class="container">
				<h3 class="title">My Products		            
				</h3>
				<ul class="category-links">
		            <?php $i = 1; ?>
		            @foreach($catTree['categories'] as $catId => $aCat)
		                <li><a href="{{route('store.products', ['id' => $catId])}}"><img src="{{ asset('uploads/categories') . '/'.  $aCat['image'] }}"><span class="visible-xs">{{$aCat['name']}}</span></a>
		                	<span class="hidden-xs category-main">{{$aCat['name']}}</span>
			                <ul class="hidden-xs sub-category">
                                            <?php $x = 0; ?>
                                            @foreach($aCat['subCategory'] as $subCatId => $aSubCat)
			                	<li><a href="{{route('store.products.subcat.list', ['catId' => $catId, 'subcatId' => $subCatId])}}">{{$aSubCat['name']}}</a></li>
                                                
                                                    @if ($x >= 2)
                                                    @break;
                                                    @endif
                                                    <?php $x++;?>
                                            @endforeach
			                </ul>
		                </li>
		                <?php $i++; ?>
		            @endforeach
		        </ul>
		        {{ link_to_route('store.products', 'See products','', array('class' => 'btn-red see-all-prod hidden-xs'))}}
			</div>
		</div>
	</div>
</section>
@endsection