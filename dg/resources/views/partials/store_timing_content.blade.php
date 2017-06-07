
@if(isset($openingTime)) 
    @foreach($openingTime as $key => $val)        
        <li>
             @if(count($val['schedule']) > 1)
             <a class="day">{{ $val['theDay'] }}</a>
            <ul>
                @foreach($val['schedule'] as $key1 => $val1)             
                <li><span class="time">{{ $val1 }} </span> </li>            
                @endforeach
            </ul>
             @else
                <a class="day single-time">{{ $val['theDay'] }}<span>{{ $val['schedule'][0] }} </span></a>
            @endif
        </li> 
    @endforeach
@endif
