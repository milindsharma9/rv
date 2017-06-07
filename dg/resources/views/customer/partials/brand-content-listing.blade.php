@if (empty($recipeMapping))
<section class="post-listing">
    <div class="post-listing-empty">
        <img src="{{ url('alchemy/images') }}/broken-cycle.svg">
        <h1>No Data Available.</h1>
    </div>
</section>
@endif
@foreach($recipeMapping as $contentSingle)
    <div class="group-item">
        <div class="group-item-inner" style="background-image: url({{ asset('uploads/bundles') . '/'. $contentSingle->image_thumb}});">
            <a class="group-link" href="{!!route('customer.bundleDetail',['productId' => $contentSingle->id ])!!}"></a>
            <img class="group-banner" alt="{{$contentSingle->name}}" src="{!! url('uploads/bundles') !!}/{!!$contentSingle->image_thumb!!}"/>
            <div class="group-info">
                    <h3 class="blog-title">{{$contentSingle->name}}</h3>
            </div>
        </div>
    </div>
@endforeach