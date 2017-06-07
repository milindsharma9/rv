<section class="occation-wrap">
    <h3 class="title center">What’s the occasion?</h3>
    <p class="subTitle hidden-xs">There's always a reason to have fun, and here we've selected some of our favourite occasions. Each one comes with a tailored list of the best products for the moment, to provide you with a  little inspiration.</p>
    <div class="occation-inner-wrap">
        <?php
        if (is_array($primaryOccasion)) {
            $primaryOccasion = json_decode(json_encode($primaryOccasion));
        }
        ?>
        <!--        //banner-->
        @if(isset($primaryOccasion))
            @foreach($primaryOccasion as $occasion)
                @if($occasion->is_banner == 1)
                <div class="featured-occation visible-xs">
                    <div class="occation-item occasion-item-div" data-occasion-id="{!! $occasion->id !!}" data-occasion-name="{!! $occasion->name !!}">
                        <img class="bgImg" src="{!! url('uploads/occasions') !!}/{!!$occasion->image!!}">
                        <div class="occation-desc">
                            @if (!empty($occasion->image_logo))
                                <img src="{!! url('uploads/occasions') !!}/{!!$occasion->image_logo!!}" style="width: auto">
                            @else
                                <img src="{!! url('alchemy/images') !!}/party-logo.svg">
                            @endif
                            <h3>{!!$occasion->name!!}</h3>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
            <!--rest images-->
            <div class="occation-slider">
                @foreach($primaryOccasion as $occasion)
                    <div class="occation-item occasion-item-div" data-occasion-id="{!!$occasion->id !!}" data-occasion-name="{!!$occasion->name !!}">
                        <img class="bgImg" src="{!! url('uploads/occasions') !!}/{!! $occasion->image !!}">
                        <div class="occation-desc">
                            @if (!empty($occasion->image_logo))
                                <img src="{!! url('uploads/occasions') !!}/{!!$occasion->image_logo!!}" style="width: auto">
                            @else
                                <img src="{!! url('alchemy/images') !!}/party-logo.svg">
                            @endif
                            <h3>{!!$occasion->name !!}</h3>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Create Event Submenus Visible on Click -->
            <div class="occasion-menu">
                <button class="close-occasion-menu"></button>
                <div class="menu-wrap">
                    <div class="menu-wrap-inner">
                        <h3 class="title" id="occasion_popup_heading">Gifting</h3>
                        <ul class="menu-child" id="occasion_popup_ul">
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="see-more-link hidden-xs">
        {{ link_to_route('customer.occasions', 'See all the occasions')}}
    </div>
</section>