@if (empty($contentData->count()))
<section class="post-listing">
    <div class="post-listing-empty">
        <img src="{{ url('alchemy/images') }}/broken-cycle.svg">
        <h1>No Data Available.</h1>
    </div>
</section>
@else 

    @if($contentData[0]->type == config('blog.type_event'))
        @include('customer.partials.event-listing')
    @elseif($contentData[0]->type == config('blog.type_place'))
        @include('customer.partials.content-places-listing')
    @elseif($contentData[0]->type == config('blog.type_blog'))
        @include('customer.partials.content-blog-listing')
    @endif
@endif