@extends('store.layouts.products')
@section('header')
My Products
@endsection
@section('content')
<section class="store-content-section store-product-section">
    @if($allowProductUpload)
        <!-- Level 1 Tabs -->
        <ul class="nav nav-tabs tab-level-1">
            <?php
            $i = 0;
            $firstCatName = $firstCatId = '';
            foreach($aCatTree['categories'] as $primaryCatId =>  $primaryCatName) {
                $liClass = '';
                if (empty($selectedId)) {
                    if ($i == 0) {
                        $firstCatName = $primaryCatName['name'];
                        $firstCatId = $primaryCatName['id'];
                        $liClass = 'active';
                    }
                    $i++;
                } else {
                    if ($primaryCatName['id'] == $selectedId) {
                        $firstCatName = $primaryCatName['name'];
                        $firstCatId = $primaryCatName['id'];
                        $liClass = 'active';
                    }
                }
                echo '<li class="'.$liClass.'" ><a data-toggle="tab" onclick ="getSubCat(\''.$primaryCatName['id'].'\', \''.$primaryCatName['name'].'\')" href="#'.strtolower($primaryCatName['name']).'"><span>'.$primaryCatName['name'].'</span></a></li>';
            }
            $fetchSubCat = 0;
            if (!empty($selectedSubCatId)) {
                $fetchSubCat = 1;
            }
            ?>
        </ul>
        <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
        <div id="content">

        </div>
    @else
        <?php
            $firstCatName = $firstCatId = '';$fetchSubCat = 0;
        ?>
        <div class="disable-product-upload-div">
            <h3>Please complete Retailer details.</h3>
            {!! link_to_route('store.kyc.register', trans('Complete') , "", array('class' => 'btn-red')) !!}
        </div>
    @endif
    
</section>
@endsection

@section('javascript')
<script>
    var storeSaveProductUrl     = "{!! route('store.products.save')!!}";
    var getSubCatTreeUrl        = "{!! route('store.products.subcat', 1)!!}";
    var getSubSubCatTreeUrl     = "{!! route('store.products.subcat.cat', 1)!!}";
    var productDetailUrl        = "{!! route('store.products.detail', 1)!!}";
    getSubCatTreeUrl    = getSubCatTreeUrl.slice(0, -2);
    getSubSubCatTreeUrl = getSubSubCatTreeUrl.slice(0, -2);
    productDetailUrl    = productDetailUrl.slice(0, -2);
    
    var currencySymbol = '<?php echo \Config::get('appConstants.currency_sign'); ?>';
    var defaultLength = '<?php echo \Config::get('appConstants.product_desc_default_length'); ?>';
</script>
<script type="text/javascript">
    var storeProducts = <?php echo json_encode($storeProducts); ?>;
    var subCatCount = {};
    var fetchSubCat = <?php echo $fetchSubCat; ?>;
    var pSubCatId = '<?php echo $selectedSubCatId; ?>';
    var allowProductUpload = '<?php echo $allowProductUpload; ?>';
    function getSubCat(pCatId, pCatName) {
        $('body').addClass('loading-content');
        $.ajax({
            url: getSubCatTreeUrl + "/" + pCatId,
            method: 'GET',
            dataType: 'json',
            success: function(result) {
                $("#content").html(result.html_content);
                $('body').removeClass('loading-content');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
                $('body').removeClass('loading-content');
            },
            complete: function(){
                $('[id*="subsubcat_"]').each(function(){
                    var countCheck = $(this).closest('li').find('ul input[type="checkbox"]:checked').length;
                    $(this).text(countCheck);
                });
            }
        });
    }

    $(document).ready(function() {
        if (allowProductUpload) { // no need to fetch products if not allowed to upload
            var pCatId = '<?php echo $firstCatId; ?>';
            var pCatName = '<?php echo $firstCatName; ?>';
            if (fetchSubCat == 1) {
                getSubSubCat(pSubCatId, pCatName);
            } else {
                getSubCat(pCatId, pCatName);
            }
        }
    });

    /// SubbScats
    function getSubSubCat(pCatId, pCatName) {
        $('body').addClass('loading-content');
        $.ajax({
            url: getSubSubCatTreeUrl + "/" + pCatId,
            method: 'GET',
            dataType: 'json',
            success: function(result) {
                $("#content").html(result.html_content);
                $('body').removeClass('loading-content');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
                $('body').removeClass('loading-content');
            },
            complete: function(){
                $('[id*="subsubcat_"]').each(function(){
                    var countCheck = $(this).closest('li').find('ul input[type="checkbox"]:checked').length;
                    $(this).text(countCheck);
                });
            }
        });
    }

    $(document).on('click', '.prod_check', function () {
        var add         = $(this).is(":checked");
        var hasSubcat   = $(this).data("subcat");
        var targetId    = $(this).data("subcatname");
        var prodId      = $(this).data("id");
        $("#prod_check_span_"+prodId+"_"+targetId).addClass('loading');
        $(this).addClass('loading');
        $.ajax({
            url: storeSaveProductUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                prodId: prodId,
                add: add,
                _token: $('input[name=_token]').val()
            },
            success: function(result) {
                if (result.status) {
                    $("#prod_check_span_"+prodId+"_"+targetId).removeClass('loading');
                    storeProducts = result.products;
                    handleTotalProductCount(add, hasSubcat, targetId);
                } else {
                    $("#prod_check_span_"+prodId+"_"+targetId).removeClass('loading');
                    alert(result.message);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
    });

    function handleTotalProductCount(add, hasSubcat, targetId) {
        if(hasSubcat) {
            var oldCount = $('#subsubcat_'+targetId).text();
            var newCount;
            if (add) {
                newCount = parseInt(oldCount) + 1;
            } else {
                newCount = parseInt(oldCount) - 1;
            }
            $('#subsubcat_'+targetId).text(newCount);
        }
    }
</script>
@endsection