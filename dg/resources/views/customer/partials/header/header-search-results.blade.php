<div class="search-result-inner">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                @if(!empty($matchedData['suggestions']))
                <h3 class="hidden-xs section-title"><span>Suggestions</span></h3>
                <div class="search-suggestions">
                    <ul>
                        @foreach($matchedData['suggestions'] as $suggestion)
                        <li>
                            <a href="{{route('customer.search')}}?param={{urlencode($suggestion['phrase'])}}&rank={{$suggestion['rank']}}">
                                {{$suggestion['phrase']}}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @elseif ($showSuggestions)
                    <h3 class="hidden-xs section-title"><span>Suggestions</span></h3>
                    <div class="post-list-empty">
                        <h1>No suggestions Available.</h1>
                    </div>
                @endif
                <input type="hidden" id="nextIndex" value="{{$nextIndex}}">
                @include('customer.partials.search-sli-products')
            </div>
            <div class="col-sm-6 hidden-xs">
                @if (!empty($matchedDataBlog['event']['results']))
                    <h3 class="section-title"><span>Events</span></h3>
                    <div class="post-widget">
                        @foreach($matchedDataBlog['event']['results'] as $result)
                        <?php
                            $startMonth     =  date("F", strtotime($result['start_date']));
                            $startDay       =  date("S", strtotime($result['start_date']));
                            $aStartDate     = explode("-", $result['start_date']);
                            $aDay           = explode(" ", $aStartDate[2]);
                        ?>
                            <div class="post-widget-item">
                                <div class="post-image" style="background-image:url({{$result['event_thumbnail']}});">
                                </div>
                                <div class="post-widget-info">
                                    <p class="post-widget-name">{{$result['title']}}</p>
                                    <p class="post-widget-date">{{$aDay[0].$startDay ." ".$startMonth}}</p>
                                    <a href="{{$result['url']}}" class="post-widget-link">Read more</a>
                                </div>
                            </div>
                        @endforeach
    <!--                    <div class="post-widget-item">
                            <div class="post-image" style="background-image:url(http://52.50.219.163:81/alchemy/images/blog-static.png);">
                            </div>
                            <div class="post-widget-info">
                                <p class="post-widget-name">test123</p>
                                <p class="post-widget-date">1st February</p>
                                <a href="http://52.50.219.163:81/events/test123" class="post-widget-link">Read more</a>
                            </div>
                        </div>-->
                    </div>
                @endif
                @if (!empty($matchedDataBlog['place']['results']))
                    <h3 class="section-title"><span>Places</span></h3>
                    <div class="post-widget">
                        @foreach($matchedDataBlog['place']['results'] as $result)
                            <div class="post-widget-item">
                                <div class="post-image" style="background-image:url({{$result['place_thumbnail']}});">
                                </div>
                                <div class="post-widget-info">
                                    <p class="post-widget-name">{{$result['title']}}</p>
                                    <a href="{{$result['url']}}" class="post-widget-link">Read more</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if (!empty($matchedDataBlog['blog']['results']))
                    <h3 class="section-title"><span>Blog</span></h3>
                    <div class="post-widget">
                        @foreach($matchedDataBlog['blog']['results'] as $result)
                            <div class="post-widget-item">
                                <div class="post-image" style="background-image:url({{$result['blog_thumbnail']}});">
                                </div>
                                <div class="post-widget-info">
                                    <p class="post-widget-name">{{$result['title']}}</p>
<!--                                    <p class="post-widget-posted-date">Posted on 12/21/2016</p>-->
                                    <a href="{{$result['url']}}" class="post-widget-link">Read more</a>
                                </div>
                            </div>
                        @endforeach
    <!--                    <div class="post-widget-item">
                            <div class="post-image" style="background-image:url(http://52.50.219.163:81/alchemy/images/blog-static.png);">
                            </div>
                            <div class="post-widget-info">
                                <p class="post-widget-name">post6</p>
                                <p class="post-widget-posted-date">Posted on 12/21/2016</p>
                                <a href="http://52.50.219.163:81/blog/post6" class="post-widget-link">Read more</a>
                            </div>
                        </div>-->
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@if(!empty($matchedProducts) && $nextIndex != 0)
<div class="col-xs-12 search-view-all">
    <a href="{{route('customer.search')}}?param={{urlencode($searchTerm)}}" class="btn">View All results</a>
</div>
@endif
@section('javascript')
<script>
    var nextResult = "{{$nextIndex}}";
    $('#nextIndex').val(nextResult);
    var paginateUrl = "{{ route('customer.search.paginate')}}";
    var searchParam = "{{$searchTerm }}";
    var isLoading = false;
    $(window).scroll(function (e) {
        if ($(window).scrollTop() == $(document).height() - $(window).height()) {
            if (!isLoading) {
                nextResult = $('#nextIndex').val();
                if (nextResult != '0') {
                $.ajax({
                    url: paginateUrl,
                    type: "POST",
                    data: {param: searchParam, 'offset': nextResult},
                    headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                    dataType: 'json',
                    beforeSend: function () {
                        $('.loading-search').addClass('loading-search').show();
                    },
                    complete: function () {
                        $('.loading-search').addClass('loading-search').hide();
                    },
                    success: function (result) {
                        if (result.status) {
                            $('#products-search-sli').append(result.html_content);
                                $('#nextIndex').val(result.nextIndex);
                        } else {
                            $('#products-search-sli').append(result.html_content);
                        }
                        isLoading = false;
                    },
                    error: function () {
                        isLoading = false;
                    }
                });
                isLoading = true;
                }else{
                    e.preventDefault();
                }
            }
        }
    });
</script>
@endsection