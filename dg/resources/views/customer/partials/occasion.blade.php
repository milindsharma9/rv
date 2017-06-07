<section class="create-event-wrap occasion-wrap featured-wrapper single-type-page">
    <div class="big-banner hidden-xs">
        <h1>what’s the occasion?</h1>
    </div>
    <div class="banner-caption hidden-xs">
        <p>There's always a reason to have fun, and here we've selected some of our favourite occasions. Each one comes with a tailored list of the best products for the moment, to provide you with a  little inspiration.</p>
    </div>
    <h3 class="title center visible-xs">what’s the occasion?</h3>
    <?php
        if (is_array($primaryOccasion)) {
            $primaryOccasion = json_decode(json_encode($primaryOccasion));
        }
    ?>
    <div class="create-event-inner-wrap">
        @foreach($primaryOccasion as $occasion)
            <div class="occation-item occasion-item-div" data-occasion-id="{!! $occasion->id !!}" data-occasion-name="{!! $occasion->name !!}">
                    <img class="bgImg" src="{!! url('uploads/occasions') !!}/{!!$occasion->image!!}">
                    <div class="caption">
                        @if (!empty($occasion->image_logo))
                            <img src="{!! url('uploads/occasions') !!}/{!!$occasion->image_logo!!}" style="width: auto">
                        @else
                            <img src="{{ url('alchemy/images') }}/dinner-logo.svg">
                        @endif
                        <h3>{!!$occasion->name!!}</h3>
                    </div>
            </div>
        @endforeach
        <div class="occation-item occasion-item-div occation-static hidden-xs">
            <p>One day your life will flash before your eyes. Make sure it's worth watching.<br/>
                <strong style="text-align: right">-- Gerard Way</strong></p>
        </div>
        <!-- Create Event Submenus Visible on Click -->
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
</section>
