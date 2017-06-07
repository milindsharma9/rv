@section('title', 'Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@section('title')
Alchemy - Home
@endsection
@extends('customer.layouts.customer')
@section('header')
<a href="">Alchemy</a>
@endsection
@section('content')
<section class="home-categories-mobile visible-xs">
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
<section class="home-slider">
        <div class="slider-wrap">
            <div class="item"><img src="{{ url('alchemy/images') }}/home-banner-4.jpg"></div>
            <!--<div class="item caption-wrap"><img src="{{ url('alchemy/images') }}/home-banner.png">
                <div class="caption">
                    <img src="{{ url('alchemy/images') }}/j&b-logo.png">
                    <h1>J&B New Tattoo<br><span>Collection</span></h1>
                </div>
            </div>
            <div class="item caption-wrap"><img src="{{ url('alchemy/images') }}/home-banner-2.png">
                <div class="caption">
                    <img src="{{ url('alchemy/images') }}/foodpairing-logo.svg">
                    <h1>Food<br><span>Pairing</span></h1>
                </div>
            </div>
            <div class="item caption-wrap"><img src="{{ url('alchemy/images') }}/home-banner-3.png">
                <div class="caption">
                    <img src="{{ url('alchemy/images') }}/party-logo.svg">
                    <h1><span>Party Time</span></h1>
                </div>
            </div>-->
        </div>
    </section>
    <section class="category-wrap">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h3 class="title center">What are you looking for?</h3>
                    <ul class="category-links">
                        <?php $i = 1; ?>
                        @foreach($catTree['categories'] as $catId => $aCat)
                        @php 
                          $formatcatName = CommonHelper::formatCatName($aCat['name']);
                        @endphp
		                <li><a href="{{route('customer.products', ['catname' => $formatcatName, 'id' => $catId])}}">
                                        @if(isset($aCat['image']))
                                        <img src="{{ asset('uploads/categories') . '/'.  $aCat['image'] }}">
                                        @endif
                                        <span class="visible-xs">{{$aCat['name']}}</span></a>
		                	<span class="hidden-xs category-main">{{$aCat['name']}}</span>
			                <ul class="hidden-xs sub-category">
                                            <?php $x = 0; ?>
                                            @foreach($aCat['subCategory'] as $subCatId => $aSubCat)
                                            @php 
                                                $formatsubcatName = CommonHelper::formatCatName($aSubCat['name']);
                                            @endphp
			                	<li><a href="{{route('customer.products.subcat.list', ['catname' => $formatcatName, 'catId' => $catId, 'subcatname' => $formatsubcatName, 'subcatId' => $subCatId])}}">{{$aSubCat['name']}}</a></li>
                                                    @if($x >= 2)
                                                    @break;
                                                    @endif
                                                   <?php  $x++; ?>
                                            @endforeach
			                </ul>
		                </li>
		                <?php $i++; ?>
		            @endforeach
                    </ul>
                    <div class="see-more-link hidden-xs">
                        {{ link_to_route('customer.products', 'Browse all products')}}
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('customer.partials.occasion-home')
    @include('customer.partials.create-event-home')
    <!-- Opening Time -->

    <!-- Login/Register Modal -->

@include('customer.partials.cart_js')
@endsection
@section('javascript')
<script>
    var moodUrl                 = "{!! route('search.mood')!!}";
    var loadingImgUrl           = "{!! url('alchemy/images/loadingstock.gif')!!}";
    var occasionUrl             = "{!! route('search.occasion')!!}";
    var getSubOccasionUrl       = "{!! route('get.occasion', 1)!!}";
    getSubOccasionUrl           = getSubOccasionUrl.slice(0, -2);
</script>
@endsection
