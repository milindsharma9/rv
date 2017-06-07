<form action="{!! $cardRegistrationDetails->CardRegistrationURL; !!}" method="post">
    <input type="hidden" name="data" value="{!! $cardRegistrationDetails->PreregistrationData; !!}" />
    <input type="hidden" name="accessKeyRef" value="{!! $cardRegistrationDetails->AccessKey; !!}" />
    <input type="hidden" name="returnURL" value="{!! $returnUrl; !!}" />

    <label for="cardNumber">Card Number</label>
    <input type="text" name="cardNumber" value="" />
    <div class="clear"></div>

    <label for="cardExpirationDate">Expiration Date</label>
    <input type="text" name="cardExpirationDate" value="" />
    <div class="clear"></div>

    <label for="cardCvx">CVV</label>
    <input type="text" name="cardCvx" value="" />
    <div class="clear"></div>

    <input type="submit" value="Pay" />
</form>