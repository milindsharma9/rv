@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10">
        <h1>{{ trans('admin/vendors.manage_products') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
<div class="product-group">
    <ul>
        @foreach($allProducts as $products)
            @php
                $checked = '';
                $storePrice = $products->store_price;
            @endphp
            @if (isset($storeProducts[$products->id]))
                @php
                    $checked = 'checked="checked"';
                    $storePrice = $storeProducts[$products->id]['price'];
                @endphp
            @endif
            <li>
                <span class='product-available'><input {{$checked}} type='checkbox' id='prod_check_{{$products->id}}' data-id="{{$products->id}}" class='prod_check' />
                    <span class='marker' id='prod_check_span_{{$products->id}}'></span>
                </span>
                <div class='product-image'>
                        <a href="{!!route('products.detail',['productId' => $products->id ]) !!}">
                        <img src="{!! CommonHelper::getProductImage($products->id, true) !!}"></a>
                </div>
                <div class='product-info'>
                        <a href="{!!route('products.detail',['productId' => $products->id ]) !!}"><h3 class='product-name'>{!! CommonHelper::formatProductDescription($products->description) !!}</h3>
                        <p class='product-quantity'>EAN:{!! $products->barcode !!}</p></a>
                </div>
                <a href="{!!route('products.detail',['productId' => $products->id ]) !!}" class='product-description-link'>
                    RRSP: <span>{!! $products->store_price !!}</span><br>
                    YOU: <span>{!! $storePrice !!}</span><br>
                </a>
            </li>
        @endforeach
    </ul>
</div>
<div class="pagination"> {{ $allProducts->links() }} </div>
<input type="hidden" name="_token" value="{!! csrf_token() !!}" />
@endsection

@section('javascript')
<script>

    var storeSaveProductUrl     = "{!! route('admin.vendors.products.save')!!}";
    var storeId     = "{{$storeId}}"
    $(document).on('click', '.prod_check', function () {
        var add         = $(this).is(":checked");
        var prodId      = $(this).data("id");
        $("#prod_check_span_"+prodId).addClass('loading');
        $(this).addClass('loading');
        $.ajax({
            url: storeSaveProductUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                prodId: prodId,
                add: add,
                storeId: storeId,
                _token: $('input[name=_token]').val()
            },
            success: function(result) {
                if (result.status) {
                    $("#prod_check_span_"+prodId).removeClass('loading');
                } else {
                    $("#prod_check_span_"+prodId).removeClass('loading');
                    alert(result.message);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
    });
</script>    
    
@endsection
