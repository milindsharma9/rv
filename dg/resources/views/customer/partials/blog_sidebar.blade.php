<div class="post-sidebar">
        @if(!empty($relatedData['event'])
            || !empty($relatedData['blog'])
            || !empty($relatedData['place'])
        )
            <h3 class="widget-title"><span>Related</span></h3>
        @endif
        <div class="post-widget related-post">
            @if(!empty($relatedData['event']))
                <div class="post-widget-item">
                        <div class="post-image" style="background-image:url({{ asset('uploads/blog') . '/'. $relatedData['event']->image_thumb}});">
                                <h3>Event</h3>
                        </div>
                        <div class="post-widget-info">
                                <p class="post-widget-name">{{$relatedData['event']->title}}</p>
                                <p class="post-widget-date">{{$relatedData['event']->eventDateSuffix}} {{$relatedData['event']->eventMonth}}</p>
                                <a href="{{route('common.events', $relatedData['event']->url_path)}}" class="post-widget-link">Read more</a>
                        </div>
                </div>
            @endif
            @if(!empty($relatedData['blog']))
                <div class="post-widget-item">
                        <div class="post-image" style="background-image:url({{ asset('uploads/blog') . '/'. $relatedData['blog']->image_thumb}});">
                                <h3>Blog</h3>
                        </div>
                        <div class="post-widget-info">
                                <p class="post-widget-name">{{$relatedData['blog']->title}}</p>
                                @php
                                    $date = date_create($relatedData['blog']->created_at);
                                @endphp
                                <p class="post-widget-posted-date">Posted on {{date_format($date,"d/m/Y")}}</p>
                                <a href="{{route('common.blog', $relatedData['blog']->url_path)}}" class="post-widget-link">Read more</a>
                        </div>
                </div>
            @endif
            @if(!empty($relatedData['place']))
                <div class="post-widget-item">
                        <div class="post-image" style="background-image:url({{ asset('uploads/blog') . '/'. $relatedData['place']->image_thumb}});">
                                <h3>Place</h3>
                        </div>
                        <div class="post-widget-info">
                                <p class="post-widget-name">{{$relatedData['place']->location}}</p>
                                <a href="{{route('common.places', $relatedData['place']->url_path)}}" class="post-widget-link">Read more</a>
                        </div>
                </div>
            @endif
        </div>
        <h3 class="widget-title"><span>Keywords</span></h3>
        <div class="post-widget keywords">
            <ul>
                @foreach($relatedData['keyword'] as $keywordData)
                    <li><a href="{{route('common.content.keywords', $keywordData->machine_name)}}">{{$keywordData->name}}</a></li>
                @endforeach
            </ul>
        </div>
        @php
            $aLastMonth = CommonHelper::getLastMonths();
            $aLastYears = CommonHelper::getPreviousYears();
        @endphp
        <h3 class="widget-title"><span>Archives</span></h3>
        <div class="post-widget archives">
            <ul>
                @foreach($aLastYears as $lastYear)
                <li class="tree-view">
                    <a href="">{{$lastYear}}</a>
                    <ul class="tree-child">
                        @php
                            $allMonth = CommonHelper::getArchiveYears($lastYear);
                        @endphp
                        @foreach($allMonth as $monthId => $monthName)
                            <li><a href="{{route('common.content.archieve', [$monthId, $lastYear])}}">{{$monthName}}</a></li>
                        @endforeach
                    </ul>
                </li>
                @endforeach
            </ul>
            <!--<a href="#" class="post-widget-link">See all</a>-->
        </div>
</div>