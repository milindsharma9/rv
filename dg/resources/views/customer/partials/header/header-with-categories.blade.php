<div class="header-categories hidden-xs">
	<ul class="category-menu">
            @php
                $catTree = CommonHelper::getCategoryTree();
            @endphp
            @foreach($catTree['categories'] as $catId => $aCat)
                <li>
                    @php 
                        $formatcatName = CommonHelper::formatCatName($aCat['name']);
                    @endphp
                    <a href="{{route('customer.products', ['catname' => $formatcatName, 'id' => $catId])}}">{{$aCat['name']}}</a>
                    @if(!empty($aCat['subCategory']))
                        <ul class="sub-category">
                            @foreach($aCat['subCategory'] as $subCatId => $subCatDetails)
                                @php 
                                    $formatsubcatName = CommonHelper::formatCatName($subCatDetails['name']);
                                @endphp
                                <li>
                                    <a class="sub-category-title" href="{{route('customer.products.subcat.list', ['catname' => $formatcatName, 'catId' => $catId, 'subcatname' => $formatsubcatName, 'subcatId' => $subCatId])}}"><span >All {{$subCatDetails['name']}}</span></a>
                                    @if(!empty($subCatDetails['subSubCat']))
                                        <ul>
                                            @foreach($subCatDetails['subSubCat'] as $subSubCatId => $subSubCatDetails)
                                                @php 
                                                    $formatsubsubcatName = CommonHelper::formatCatName($subSubCatDetails['name']);
                                                @endphp
                                                <li><a href="{{route('customer.products.subcat.list', ['catname' => $formatcatName, 'catId' => $catId, 'subcatname' => $formatsubcatName, 'subcatId' => $subCatId, 'subsubcatname' => $formatsubsubcatName, 'subsubcatId' => $subSubCatId])}}">{{$subSubCatDetails['name']}}</a></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
	</ul>
</div>