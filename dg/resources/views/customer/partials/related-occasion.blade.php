@if(isset($relatedOccasion))
<div class="related-occations">
    <h3 class="title">Related Occasions</h3>
    <div class="related-occations-inner-wrap">
        <ul>
            @foreach($relatedOccasion as $occasion)
            <li><a class="occation-item occasion-item-div" data-occasion-id="{!! $occasion->id !!}" data-occasion-name="{!! $occasion->name !!}">
                    <img class="bgImg" src="{!! url('uploads/occasions') !!}/{!!$occasion->image!!}">
                    <div class="caption">
                        @if (!empty($occasion->image_logo))
                        <img src="{!! url('uploads/occasions') !!}/{!!$occasion->image_logo!!}" style="width: auto">
                        @else
                        <img src="{{ url('alchemy/images') }}/dinner-logo.svg">
                        @endif
                        <p>{!!$occasion->name!!}</p>
                    </div>
                </a></li>
            @endforeach
        </ul>
        <div class="event-menu">
            <button class="close-event-menu"></button>
            <div class="menu-wrap">
                <div class="menu-wrap-inner">
                    <h3 class="title" id="occasion_popup_heading">Dinner Party</h3>
                    <ul class="menu-child" id="occasion_popup_ul">

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endif