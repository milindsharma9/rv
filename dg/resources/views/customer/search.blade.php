@section('title', 'Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@section('title')
Alchemy - Search
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
            echo '<li >'
                    . '<a class="search_li_a" data-toggle="tab" data-id="'.$primaryCatName['id'].'" href="#'.strtolower($primaryCatName['name']).'">'
                    . '<span>'.$primaryCatName['name'].'</span></a>'
                    . '</li>';
        }
        ?>
        </ul>
        <div id="content">
            @include('customer.partials.search')
        </div>
</section>
@include('customer.partials.cart-modal')
@endsection
@section('javascript')
<script>
    var cartAddUrl              = "{!! route('customer.cart.add')!!}";
    var cartUpdateUrl           = "{!! route('customer.cart.update')!!}";
    var checkCustomerCartStatusUrl             = "{!! route('customer.cart.status.check')!!}";
    var cartSetDeliveryPostcodeUrl             = "{!! route('customer.delivery.postcode.set')!!}";
    var productSearchCatUrl        = "{!! route('customer.search.cat', [1,1])!!}";
    productSearchCatUrl            = productSearchCatUrl.slice(0, -4);
    var searchParam = "<?php echo $param; ?>";
    var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
</script>
<!--<script src="{{ url('alchemy/js') }}/product_cart.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>-->
<script type="text/javascript">
    $(document).on('click touchstart', '.search_li_a', function () {
        $('body').addClass('loading-content');
        var catId      = $(this).data("id");
        $.ajax({
            url: productSearchCatUrl + "/" + catId + "/" + searchParam,
            method: 'GET',
            success: function(result) {
                if (result.status) {
                    $('#content').html(result.html_content);
                } else {
                    alert("Some error. Please try refreshing page.");
                }
                $('body').removeClass('loading-content');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
                $('body').removeClass('loading-content');
            }
        });
    });
    var eventName = "search_" + searchParam;
    Intercom('trackEvent', eventName);
    //console.log(eventName);
</script>
@endsection