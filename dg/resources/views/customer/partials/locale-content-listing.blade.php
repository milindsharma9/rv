@if (empty($contentData->count()))
<section class="post-listing">
    <div class="post-listing-empty">
        <img src="{{ url('alchemy/images') }}/broken-cycle.svg">
        <h1>No Data Available.</h1>
    </div>
</section>
@endif
@foreach($contentData as $contentSingle)
    <div class="group-item">
        @if ($contentSingle->type == config('blog.type_event'))
        <?php
            $startMonth     =  date("M", strtotime($contentSingle->start_date));
            $startDay       =  date("D", strtotime($contentSingle->start_date));
            $aStartDate     = explode("-", $contentSingle->start_date);
        ?>
            <div class="group-item-inner" style="background-image: url({{ asset('uploads/blog') . '/'. $contentSingle->image_thumb}});">
                <a class="group-link" href="{{route('common.events', $contentSingle->url_path)}}"></a>
                <img class="group-banner" alt="{{$contentSingle->title}}" src="{!! url('uploads/blog') !!}/{!!$contentSingle->image_thumb!!}"/>
                <div class="group-info">
                    <span class="date">{{substr($aStartDate[2], 0, 3)}}</span>
                    <span class="day">{{$startDay}}</span>
                    <h3>{{$contentSingle->title}}</h3>
                </div>
            </div>
        @elseif ($contentSingle->type == config('blog.type_blog'))
            <div class="group-item-inner" style="background-image: url({{ asset('uploads/blog') . '/'. $contentSingle->image_thumb}});">
                <a class="group-link" href="{{route('common.blog', $contentSingle->url_path)}}"></a>
                <img class="group-banner" alt="{{$contentSingle->title}}" src="{!! url('uploads/blog') !!}/{!!$contentSingle->image_thumb!!}"/>
                <div class="group-info">
                        <h3 class="blog-title">{{$contentSingle->title}}</h3>
                        <p class="blog-subtitle">{{$contentSingle->sub_title}}</p>
                </div>
            </div>
        @elseif ($contentSingle->type == config('blog.type_place'))
            <div class="group-item-inner" style="background-image: url({{ asset('uploads/blog') . '/'. $contentSingle->image_thumb}});">
                <a class="group-link" href="{{route('common.places', $contentSingle->url_path)}}"></a>
                <img class="group-banner" alt="{{$contentSingle->title}}" src="{!! url('uploads/blog') !!}/{!!$contentSingle->image_thumb!!}"/>
                <div class="group-info">
                    <h3 class="blog-title">{{$contentSingle->title}}</h3>
                </div>
            </div>
        @endif
    </div>
@endforeach