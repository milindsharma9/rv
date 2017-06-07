<html>
    <head>
    </head>
    <body style="font-family: Helvetica,Arial;">
        <div class="email-template-wrap" style="font-family: Helvetica,Arial;background-color: #EFEFEF;">
            <div class="header" style="font-family: Helvetica,Arial;padding: 5px 15px;border-bottom: solid 4px #efefef;background: #FFF;">
                <img src="{{ url('alchemy/images') }}/logo.png" style="font-family: Helvetica,Arial;">
            </div>
            <div class="content-container" style="font-family: Helvetica,Arial;padding: 0 15px;">
                <div class="wrap" style="font-family: Helvetica,Arial;max-width: 620px;background: #FFF;margin: 0 auto;padding: 25px 20px;">
                    <h1 style="font-family: Helvetica,Arial;margin: 0 0 10px;font-size: 16px;border-bottom: solid 1px #efefef;padding-bottom: 10px;">Thank you for your order, {{$userName}}</h1>
                    <h3 style="font-family: Helvetica,Arial;font-size: 14px;">ORDER #{{$orderNumber}} IS CONFIRMED</h3>
                    <h3 style="font-family: Helvetica,Arial;font-size: 14px;">Your Order Details</h3>
                    <ul class="product-group" style="font-family: Helvetica,Arial;padding: 0;list-style: none;">
                        @foreach($cartViewData as $cartItem)
                        @if(isset($cartItem['bundleId']))
                        <li style="font-family: Helvetica,Arial;overflow: hidden;padding: 10px;border-bottom: solid 1px #efefef;">
                            <div class="col-left" style="font-family: Helvetica,Arial;width: 70%;float: left;">
                                <div class="prod-name" style="font-family: Helvetica,Arial;font-size: 14px;font-weight: bold;line-height: 1.4;">{{$cartItem['bundleName']}}</div>
                                <div class="prod-price" style="font-family: Helvetica,Arial;font-size: 12px;line-height: 1.4;">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($cartItem['bundleDefaultTotalPrice'])}}</div>
                            </div>
                            <div class="col-right" style="font-family: Helvetica,Arial;width: 30%;text-align: right;float: left;">
                                <div class="prod-bought-quantity" style="text-align:center; font-family: Helvetica,Arial;display: inline-block;padding: 5px;width: 30px;background: #EAEAEA;margin-bottom: 5px;">{{$cartItem['bundleQty']}}</div>
                                <div class="single-total" style="font-family: Helvetica,Arial;font-size: 12px;">Total {{Config::get('appConstants.currency_sign')}} {{CommonHelper::formatPrice($cartItem['bundleTotalPrice'])}}</div>
                            </div>
                            <ul class="product-group" style="font-family: Helvetica,Arial;padding: 0;list-style: none; clear:both;">
                                @foreach($cartItem['bundleProducts'] as $bundleProducts)
                                <li style="font-family: Helvetica,Arial;overflow: hidden;padding: 10px;">
                                    <div class="col-left" style="font-family: Helvetica,Arial;width: 70%;float: left;">
                                        <div class="prod-name" style="font-family: Helvetica,Arial;font-size: 14px;font-weight: bold;line-height: 1.4;">{{ CommonHelper::formatProductDescription($bundleProducts['options']['0']['alcohol_content']) }}</div>
                                        <div class="prod-info" style="font-family: Helvetica,Arial;font-size: 12px;color: #777;line-height: 1.4;">{{$bundleProducts['name']}}</div>
                                        <div class="prod-price" style="font-family: Helvetica,Arial;font-size: 12px;line-height: 1.4;">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($bundleProducts['price'])}}</div>
                                    </div>
                                    <div class="col-right" style="font-family: Helvetica,Arial;width: 30%;text-align: right;float: left;">
                                        <div class="prod-bought-quantity" style="text-align: center;font-family: Helvetica,Arial;display: inline-block;padding: 5px;width: 30px;background: #EAEAEA;margin-bottom: 5px;">{{$bundleProducts['qty']}}</div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        @else
                        <li style="font-family: Helvetica,Arial;overflow: hidden;padding: 10px;border-bottom: solid 1px #efefef;">
                            <div class="col-left" style="font-family: Helvetica,Arial;width: 70%;float: left;">
                                <div class="prod-name" style="font-family: Helvetica,Arial;font-size: 14px;font-weight: bold;line-height: 1.4;">{{ CommonHelper::formatProductDescription($cartItem['options']['0']['alcohol_content']) }}</div>
                                <div class="prod-info" style="font-family: Helvetica,Arial;font-size: 12px;color: #777;line-height: 1.4;">{{$cartItem['name']}}</div>
                                <div class="prod-price" style="font-family: Helvetica,Arial;font-size: 12px;line-height: 1.4;">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($cartItem['price'])}}</div>
                            </div>
                            <div class="col-right" style="font-family: Helvetica,Arial;width: 30%;text-align: right;float: left;">
                                <div class="prod-bought-quantity" style="text-align: center;font-family: Helvetica,Arial;display: inline-block;padding: 5px;width: 30px;background: #EAEAEA;margin-bottom: 5px;">{{$cartItem['qty']}}</div>
                                <div class="single-total" style="font-family: Helvetica,Arial;font-size: 12px;">Total {{Config::get('appConstants.currency_sign')}} {{CommonHelper::formatPrice($cartItem['subtotal'])}}
                                </div>
                            </div>
                        </li>
                        @endif
                        @endforeach
                        <li style="font-family: Helvetica,Arial;overflow: hidden;padding: 10px;border-bottom: solid 1px #efefef;">
                            <div class="col-left" style="font-family: Helvetica,Arial;width: 70%;float: left;">
                                Driver Charge
                            </div>
                            <div class="col-right" style="font-family: Helvetica,Arial;width: 30%;text-align: right;float: left;">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($driverCharge)}}</div>
                        </li>
                    </ul>
                    <div class="grand-total" style="font-family: Helvetica,Arial;padding: 10px;overflow: hidden;font-size: 18px;">
                        <div class="col-left" style="font-family: Helvetica,Arial;width: 70%;float: left;">Total</div>
                        <div class="col-right" style="font-family: Helvetica,Arial;width: 30%;text-align: right;float: left;color: #FF6C61;">{{Config::get('appConstants.currency_sign')}}{{CommonHelper::formatPrice($cartTotal)}}</div>
                    </div>
                </div>
            </div>
            <div class="footer" style="font-family: Helvetica,Arial;background: #1c1c1c;padding-top: 15px;padding-bottom: 15px;">
                <ul style="font-family: Helvetica,Arial;list-style: none;padding: 0;margin: 0;text-align: center;">
                    <li style="font-family: Helvetica,Arial;display: inline-block;margin: 0 10px;"><a href="{!! config('appConstants.twitter'); !!}" target="_blank" style="font-family: Helvetica,Arial;"><img src="{{ url('alchemy/images') }}/twitter.png" style="font-family: Helvetica,Arial;max-width: 50px;"></a></li>
                    <li style="font-family: Helvetica,Arial;display: inline-block;margin: 0 10px;"><a href="{!! config('appConstants.facebook'); !!}" target="_blank" style="font-family: Helvetica,Arial;"><img src="{{ url('alchemy/images') }}/facebook.png" style="font-family: Helvetica,Arial;max-width: 50px;"></a></li>
                    <li style="font-family: Helvetica,Arial;display: inline-block;margin: 0 10px;"><a href="{!! config('appConstants.instagram'); !!}" target="_blank" style="font-family: Helvetica,Arial;"><img src="{{ url('alchemy/images') }}/instagram.png" style="font-family: Helvetica,Arial;max-width: 50px;"></a></li>
                    <li style="font-family: Helvetica,Arial;display: inline-block;margin: 0 10px;"><a href="{!! config('appConstants.pinterest'); !!}" target="_blank" style="font-family: Helvetica,Arial;"><img src="{{ url('alchemy/images') }}/pinterest.png" style="font-family: Helvetica,Arial;max-width: 50px;"></a></li>
                    <li style="font-family: Helvetica,Arial;display: inline-block;margin: 0 10px;"><a href="mailto:{!! config('appConstants.mailto'); !!}" style="font-family: Helvetica,Arial;"><img src="{{ url('alchemy/images') }}/email.png" style="font-family: Helvetica,Arial;max-width: 50px;"></a></li>
                </ul>
                <div class="copyright" style="font-family: Helvetica,Arial;text-align: center;font-size: 11px;color: #777777;margin-top: 10px;">©2016 Alchemy Wings.</div>
            </div>
        </div>
    </body>
</html>
