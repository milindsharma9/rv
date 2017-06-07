<li class="menu-payment tree-view" id="change-payment">
    <a>My Payment Method</a>
    <div class="tree-child payment-options">
        <div class="card-view">
            @if($hasCard)
            @foreach ($card as $row)
            @php $checked = ''; @endphp
            @if ($row['default'] == '1')
            @php $checked = 'checked=checked'; @endphp
            @endif
            <div class="card-info">
                {{ Form::radio('card', $row['id'], $checked) }}
                {!! Form::hidden('cardId', $row['id']) !!}
                <span class="icon-payment"></span>
                <span class="card-cont">{{$row['cardDetails']}}</span>
                {!! link_to_route('customer.payment', trans('labels.change_payment'), array('cardId' => $row['id']), array('class' => 'btn btn-xs btn-info')) !!}
            </div>
            @endforeach
            @endif
            @if((empty($card) || count($card) < 2) )
            <div class="card-info add-payment-card">
                <span class="icon-payment"></span>
                <span class="card-cont"></span>
                {!! link_to_route('customer.payment', trans('labels.add_payment'), '', array('class' => 'btn btn-xs btn-info')) !!}
            </div>
            @endif
        </div>	
    </div>
</li>