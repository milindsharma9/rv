@if(isset($charges))
    @foreach($charges as $charge)
        @if(!empty($charge['value']) && $charge['value']  != '0.00')
            <div class="product-name">{!!$charge['label']!!} <span>{!! config('appConstants.currency_sign') !!}{!!CommonHelper::formatPrice($charge['value'])!!}</span></div>
        @endif
    @endforeach
@endif