@section('title', ' Alchemy Wings - Sitemap')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@extends('layouts.sitemap')

@section('content')
<section class="site-banner" style="background-image: url({{ url('alchemy/images')}}/whats-occasion-banner.png);">
    <div class="container">
        <h1 class="banner-title">Site Map</h1>
    </div>
</section>
<section class="sitemap-section">
    @foreach($aSiteMap as $cat => $catDetails)
    <div class="category">
        <h2 class="category-name category-main">{{$catDetails['name']}}</h2>
        @foreach($catDetails['data'] as $subcat => $subcatDetails)
        <div class="category-level-2">
            <hr>
            <h3 class="category-name"><span>{{$subcatDetails['name']}}</span></h3>
            @if(isset($subcatDetails['data']))
            @foreach($subcatDetails['data'] as $subsubcat => $subsubcatDetails)
            <div class="container">
                <div class="category-level-3">
                    <h3 class="category-name">{{$subsubcatDetails['name']}}</h3>
                    @if(isset($subsubcatDetails['products']))
                    <div class="category-products">                                             
                        <ul>
                            @foreach($subsubcatDetails['products'] as $products => $productdetail)
                            <li><a href="{{url($productdetail->url)}}">{{$productdetail->description}}</a></li>							
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
            @endif
        </div>
        @endforeach
    </div>
    @endforeach
</section>
@include('partials.login')
@endsection