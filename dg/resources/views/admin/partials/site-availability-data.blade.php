@if(isset($timings))
@php
    $is_24hrs = "";
    $is_closed = "";
    $set_time = "";
@endphp
@if($timings['is_24hrs'] == 0 && $timings['is_closed'] == 0) 
    @php
     $set_time = "checked";
     @endphp
@else
    @if ($timings['is_24hrs'] == 1)
        @php    
          $is_24hrs = "checked";
        @endphp
    @elseif ($timings['is_closed'] == 1)
        @php    
          $is_closed = "checked";
        @endphp
    @endif
@endif
<div id="info_div">
    <ul class="time-table">
        <li>
            <span class="opening-hrs">
                <label class="radio-option"><input type="radio" name="schedule" value="set_time" <?php echo $set_time; ?> >Set Time</label>
                <label class="radio-option"><input type="radio" name="schedule" value="is_24hrs" <?php echo $is_24hrs; ?> >24h</label>
                <label class="radio-option"><input type="radio" name="schedule" value="is_closed" <?php echo $is_closed; ?> >Closed</label>
            </span>
        </li>
    </ul>
</div>

<div id="tt">
<ul class="time-table">
    @foreach($time as $key => $value) 
    @if(in_array($key, $timings['schedule']))
    <li><label class="check-option"><input type="checkbox" name="site_time[{{ $key }}]" checked id="time-{{$key}}"> {{$value}}</label></li>
    @else
    <li><label class="check-option"><input type="checkbox" name="site_time[{{ $key }}]"  id="time-{{$key}}"> {{$value}}</label></li>
    @endif
    @endforeach  
</ul>
</div>
@endif