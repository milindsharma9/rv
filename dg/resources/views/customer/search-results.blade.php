@section('title')
Alchemy - Results
@endsection
@extends('customer.layouts.customer')
@section('content')
	<div class="order-header">
		<a href="{{ url()->previous() }}" class="btn-red" style="margin-bottom: 0;">< Back</a>
	</div>
	<section class="search-results-static">
		@include('customer.partials.header.header-search-results')
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
    var searchParam = "<?php echo $searchTerm; ?>";
    var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
</script>
@endsection