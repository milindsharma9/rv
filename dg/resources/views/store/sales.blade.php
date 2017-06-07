@extends('store.layouts.products')
@section('header')
My Sales
@endsection
@section('content')
<section class="store-content-section store-sales-section">
	<div class="container">	
		<h3 class="title hidden-xs">My Sales</h3>
		<div class="row">
			<div class="col-xs-12 col-sm-6 store-sales-chart">
				<h3 class="title visible-xs">Last Week Sales</h3>
				<div class="chart-container" id="container">
                    <!--<img src="{{ url('alchemy/images') }}/chart-image.png">-->
				</div>
				<div class="chart-legend visible-xs">
					<span class="legend-revenue">Revenues ({{Config::get('appConstants.currency_sign')}})</span>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 store-sales">
				<div class="sales-listing">
					<ul>
						<li class="sales-today">
							<img src="{{ url('alchemy/images') }}/daywise-sales.png" class="image-sales-listing">
							<div class="sales-count">
								{{Config::get('appConstants.currency_sign')}}{{$storeSalesData['today_sales']}}<span>Today</span>
							</div>
						</li>
						<li class="sales-last-week">
							<img src="{{ url('alchemy/images') }}/daywise-sales.png" class="image-sales-listing">
							<div class="sales-count">
								{{Config::get('appConstants.currency_sign')}}{{$storeSalesData['last_week_sales']['week_total']}}<span>Last Week</span>
							</div>
						</li>
						<li class="sales-last-month">
							<img src="{{ url('alchemy/images') }}/daywise-sales.png" class="image-sales-listing">
							<div class="sales-count">
								{{Config::get('appConstants.currency_sign')}}{{$storeSalesData['last_month_sales']}}<span>Last Month</span>
							</div>
						</li>
					</ul>
				</div>
				<div class="store-sales-total hidden-xs ">
					<h2>Total Sales</h2>
					<p>{{Config::get('appConstants.currency_sign')}}{{$storeSalesData['total_sales']}}</p>
				</div>
			</div>
			<div class="col-xs-12 store-sales-total visible-xs ">
				<h2>Total Sales</h2>
				<p>{{Config::get('appConstants.currency_sign')}}{{$storeSalesData['total_sales']}}</p>
			</div>
		</div>
	</div>
	<div class="store-best-seller-wrapper">
		<div class="container">
		<div class="row">
			<div class="col-xs-12 store-best-seller">
				<h3 class="title">bestseller products
					<a href="{{route('store.products')}}" class="btn-black visible-xs">See all</a>
				</h3>
                                <ul class="best-seller-slider">
					<?php
                                        $i = 0;
                                        foreach ($bestSellerProducts as $products) {
                                            $liClass = 'disabled';
                                            if (in_array($products->id, $storeProducts)) {
                                                $liClass = 'active';
                                            }
                                            $i++;
                                    ?>
                                    <li>
                                        <span class="product-available"><span class="marker {{$liClass}}"></span></span>
                                        <a href="{{route('store.products.detail',[$products->id ])}}"><img src="{{ CommonHelper::getProductImage($products->id, true)}}"> </a>
                                    </li>
                                    <?php } ?>
				</ul>
				<a href="{{route('store.bestseller')}}" class="btn-red hidden-xs ">see all bestsellers</a>
			</div>
		</div>
	</div>
	</div>
	
</section>
@endsection
@section('javascript')
<script src="{{ url('external') }}/highcharts.js?v={{ env('ASSETS_VERSION_NUMBER') }}" type="text/javascript" ></script>
<script type="text/javascript">
    var currencySymbol = '<?php echo \Config::get('appConstants.currency_sign'); ?>';
    $(function () {
    $('#container').highcharts({
        credits: {
            enabled: false
        },
        title: {
            text: ' ',
            x: -20 //center
        },
        xAxis: {
            categories: ['Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun',
                'Mon']
        },
        yAxis: {
            title: {
                text: 'Revenue ('+currencySymbol +')'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: currencySymbol 
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: ' ',
            data: <?php echo json_encode($chartData); ?>
        }]
    });
});
</script>
@endsection