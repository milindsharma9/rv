@section('title')
Alchemy - Products
@endsection
@extends('customer.layouts.customer')
@section('header')
<a href="">Alchemy</a>
@endsection
@section('content')
<section class="customer-content-section customer-product-section">
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
            echo '<li class="'.$liClass.'" ><a data-intercom-event="click_category_'.$primaryCatName['name'].'" data-toggle="tab" onclick ="getSubCat(\''.$primaryCatName['id'].'\', \''.$primaryCatName['name'].'\')" href="#'.strtolower($primaryCatName['name']).'"><span>'.$primaryCatName['name'].'</span></a></li>';
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
</section>
@include('customer.partials.cart-modal')
@endsection


@section('javascript')
<script>
    var cartAddUrl              = "{!! route('customer.cart.add')!!}";
    var cartUpdateUrl           = "{!! route('customer.cart.update')!!}";
    var getSubCatTreeUrl        = "{!! route('customer.products.subcat', 1)!!}";
    var getSubSubCatTreeUrl     = "{!! route('customer.products.subcat.cat', 1)!!}";
    var productDetailUrl        = "{!! route('products.detail', 1)!!}";
    getSubCatTreeUrl            = getSubCatTreeUrl.slice(0, -2);
    getSubSubCatTreeUrl         = getSubSubCatTreeUrl.slice(0, -2);
    productDetailUrl            = productDetailUrl.slice(0, -2);
    
    var currencySymbol          = '<?php echo \Config::get('appConstants.currency_sign'); ?>';
    var defaultLength           = '<?php echo \Config::get('appConstants.product_desc_default_length'); ?>';
    
    var checkCustomerCartStatusUrl             = "{!! route('customer.cart.status.check')!!}";
//    var cartSetDeliveryPostcodeUrl             = "{!! route('customer.delivery.postcode.set')!!}";
//    var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
</script>
<!--<script src="{{ url('alchemy/js') }}/product_cart.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>-->
<script type="text/javascript">
    var fetchSubCat = <?php echo $fetchSubCat; ?>;
    var pSubCatId = '<?php echo $selectedSubCatId; ?>';
    $(document).ready(function() {
        var pCatId = '<?php echo $firstCatId; ?>';
        var pCatName = '<?php echo $firstCatName; ?>';
        if (fetchSubCat == 1) {
            getSubSubCat(pSubCatId, pCatName);
        } else {
            getSubCat(pCatId, pCatName);
        }
    });

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
            }
        });
    }

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
            }
        });
    }
    
</script>
@endsection