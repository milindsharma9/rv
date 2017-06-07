<div class="explore-sub-occasion-inner">
    <div class="title-header">
        <a class="btn-red"></a>
        <span>{{$occasionName}}</span>
    </div>
    <div class="sub-occasion-container">
        @foreach($subOccasions as $subOccasion)
            @php
                $subOccasionUrlName = CommonHelper::formatCatName($subOccasion['name']);
                $routeName = 'search.occasion';
                $imagePath = 'occasions' ;
            @endphp
            @if(isset($isTheme) && $isTheme)
                @php
                    $routeName = 'search.mood';
                    $imagePath = 'events' ;
                @endphp
            @endif
            <div class="sub-occasion-item" style="background-image:url({{ url('uploads/'.$imagePath) }}/{{$subOccasion['image']}})">
                <a href="{!!route($routeName,['occasionId' => $subOccasion['id'], 'occasionName' => $subOccasionUrlName]) !!}">
                    <span>{{$subOccasion['name']}}</span>
                </a>
            </div>
        @endforeach
    </div>
</div>