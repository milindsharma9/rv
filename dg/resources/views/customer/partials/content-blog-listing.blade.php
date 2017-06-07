<section class="post-listing post-type-blog">
    @if (empty($contentData->count()))
        <div class="post-listing-empty">
                <img src="{{ url('alchemy/images') }}/broken-cycle.svg">
                <h1>No Post data available.</h1>
        </div>
    @else
        <div class="three-column-group">
            <div class="three-column-group-inner">
                <div id="infinite-scroll">
                    <ul id="blog-type-data">
                        <li class="item">
                        @foreach($contentData as $contentSingle)
                            <div class="group-item">
                                <div class="group-item-inner" style="background-image: url({{ asset('uploads/blog') . '/'. $contentSingle->image_thumb}});">
                                    <a class="group-link" href="{{route('common.blog', $contentSingle->url_path)}}"></a>
                                    <img class="group-banner" alt="{{$contentSingle->title}}" src="{!! url('uploads/blog') !!}/{!!$contentSingle->image_thumb!!}"/>
                                    <div class="group-info">
                                            <h3 class="blog-title">{{$contentSingle->title}}</h3>
                                            <p class="blog-subtitle">{{$contentSingle->sub_title}}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </li>
                    @if (!empty($contentData))
                    {!! $contentData->links() !!}
                    @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif
</section>