<section class="post-listing">
    @if (empty($contentData->count()))
    <div class="post-listing-empty">
        <img src="{{ url('alchemy/images') }}/broken-cycle.svg">
        <h1>No Events Available.</h1>
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
                                <a class="group-link" href="{{route('common.events', $contentSingle->url_path)}}"></a>
                                <img class="group-banner" alt="{{$contentSingle->title}}" src="{!! url('uploads/blog') !!}/{!!$contentSingle->image_thumb!!}"/>
                                <div class="group-info">
                                    <span class="date">{{$contentSingle->eventDate}}</span>
                                    <span class="day">{{substr($contentSingle->eventDay, 0, 3)}}</span>
                                    <h3>{{$contentSingle->title}}</h3>
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

