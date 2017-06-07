@extends('admin.layouts.master')

@section('content')
<h1>Products List</h1>
<ul class="category-links">
    @foreach($catTree['categories'] as $catId => $aCat)
    @php 
    $formatcatName = CommonHelper::formatCatName($aCat['name']);
    @endphp
    <li><a href="{{route('customer.products', ['catname' => $formatcatName, 'id' => $catId])}}">{{$aCat['name']}}</a>
        <ul class="hidden-xs sub-category">
            @foreach($aCat['subCategory'] as $subCatId => $aSubCat)
            @php 
            $formatsubcatName = CommonHelper::formatCatName($aSubCat['name']);
            @endphp
            <li><a href="{{route('customer.products.subcat.list', ['catname' => $formatcatName, 'catId' => $catId, 'subcatname' => $formatsubcatName, 'subcatId' => $subCatId])}}">{{$aSubCat['name']}}</a></li>
            <ul class="hidden-xs sub-category">                
                @foreach($aSubCat['subSubCat'] as $subsubCatId => $asubSubCat)
                @php 
                $formatsubsubcatName = CommonHelper::formatCatName($asubSubCat['name']);
                @endphp
                <li><a href="{{route('customer.products.subcat.list', ['catname' => $formatcatName, 'catId' => $catId, 'subcatname' => $formatsubcatName, 'subcatId' => $subCatId, 'subsubcatname' => $formatsubsubcatName, 'subsubcatId' => $subsubCatId])}}">{{$asubSubCat['name']}}</a></li>
                @endforeach
            </ul>
            @endforeach
        </ul>
    </li>
    @endforeach
</ul>
@endsection