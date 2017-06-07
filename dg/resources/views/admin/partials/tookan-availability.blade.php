<div id="tt">
    <ul class="time-table">
        @foreach($time as $key => $value)
            @php
                $partnerCheckedString   = 'checked';
                $partnerChecked         = 'checked="checked"';
                $internalCheckedString  =  $internalChecked = '';
            @endphp
            
            @if (isset($timings['schedule'][$key]) && $timings['schedule'][$key] == 'internal')
                @php
                    $internalCheckedString = 'checked';
                    $internalChecked = 'checked="checked"';
                    $partnerCheckedString   = '';
                    $partnerChecked         = '';
                @endphp
            @endif
        <li>
            <label class="check-option checked">
                <input type="checkbox" checked name="site_time[{{ $key }}]"  id="time-{{$key}}"> {{$value}}
            </label>
            <label class="radio-option {{$partnerCheckedString}}">
                <input type="radio" {{$partnerChecked}} value='partner' name="partner_{{$key}}"> Partner
            </label>
            <label class="radio-option {{$internalCheckedString}}">
                <input type="radio" {{$internalChecked}} value='internal' name="partner_{{$key}}"> Internal
            </label>
        </li>
        @endforeach
    </ul>
</div>