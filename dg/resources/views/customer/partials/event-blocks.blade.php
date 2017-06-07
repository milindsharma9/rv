<div class="create-event-inner-wrap">
    <div class="occation-item occasion-item-div occation-static hidden-xs">
        <p>The secret of life is enjoying the passage of time.<br/>
                <strong>-- James Taylor </strong></p>
    </div>
    <?php 
        $listCount = 0;
        if (is_array($primaryEvents)) {
            $primaryEvents = json_decode(json_encode($primaryEvents));
        }
    ?>
    @foreach($primaryEvents as $event)
    <div class="occation-item" data-event-id="{{$event->id}}" data-event-name="{{$event->name}}">
        <img class="bgImg" src="{{ url('uploads/events') }}/{{$event->image}}">
        <div class="occation-desc">
            <img src="{{ url('uploads/events') }}/{{$event->image_logo}}" style="width: auto">
            <h3>{{$event->name}}</h3>
        </div>
    </div>
    @if($listCount == 4)
       <div class="occation-item occation-static hidden-xs">
           <p>Just play. Have fun. Enjoy the game.<br/>
               <strong>-- Michael Jordan </strong></p></div>
    @endif
    <?php $listCount++ ?>
    @endforeach
    <!-- Create Event Submenus Visible on Click -->
    <div class="event-menu">
        <button class="close-event-menu"></button>
        <div class="menu-wrap">
            <div class="menu-wrap-inner">
                <h3 class="title" id="event_popup_heading">Gifting</h3>
                <ul class="menu-child" id="event_popup_ul">
                </ul>
            </div>
        </div>
    </div>
</div>