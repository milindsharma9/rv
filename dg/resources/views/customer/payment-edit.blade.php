@section('title', 'Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@extends('customer.layouts.customer')
@section('content')
<section class="customer-content-section customer-payment-edit-section">
    <div class="order-header visible-xs"><h3 class="title">

            @if(isset($noHeader) && $noHeader == 'true')
            @else
            <a href="{{ url()->previous() }}" class="btn-red">< Back</a>
            @endif
            Payment Details</h3></div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
        </ul>
    </div>
    @endif
    <span class="help-block" id="error-popup"></span>
    {!! Form::model($userData, array('id' => 'form-saveCard', 'method' => 'POST','url' => $cardRegistrationDetails->CardRegistrationURL)) !!}
    <?php $userId = $userData['id']; ?>
    @if(isset($userData['cardAddress']->fk_users_id))
    <?php $userId = $userData['cardAddress']->fk_users_id; ?>
    @endif
    {!! Form::hidden('id', $userId) !!}
    @if(isset($cardId))
    {!! Form::hidden('mango_users_card_id', $cardId) !!}
    @endif
    <input type="hidden" name="data" value="{!! $cardRegistrationDetails->PreregistrationData; !!}" />
    <input type="hidden" name="accessKeyRef" value="{!! $cardRegistrationDetails->AccessKey; !!}" />
    <input type="hidden" name="returnURL" value="{!! $returnUrl; !!}" />
    <div class="payment-details">
        <div class="container">
            <div class="order-header hidden-xs ">
                @if(isset($noHeader) && $noHeader == 'true')
                @else
                <h3 class="title"><a href="{{ url()->previous() }}" class="btn-red">< Back</a>
                    @endif
                    Payment Details</h3></div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label>Cart Type</label>
                        <select name="cardType">
                            <option>Visa</option>
                            <option>Master</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Name On Card</label>
                        <input type="text" name="cardName" placeholder="Name as it appears on the card">
                    </div>
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="number" name="cardNumber" placeholder="card number">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="text" name="cardExpirationDate" placeholder="MMYY">
                    </div>
                    <div class="form-group">
                        <label>Security Code</label>
                        <input type="password" name="cardCvx" placeholder="CVV">
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="payment-address-details">
        <div class="container">
            <h3 class="title">Payment Card Address</h3>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label>Address</label>
                        <?php $address = $city = $state = $pin = ''; ?>
                        @if(isset($userData['cardAddress']->address))
                        <?php $address = $userData['cardAddress']->address; ?>
                        @endif
                        {!! Form::text('address', old('address',$address), array('placeholder' => 'address name')) !!}
                    </div>
                    <div class="form-group">
                        <label>Town</label>
                        @if(isset($userData['cardAddress']->city))
                        <?php $city = $userData['cardAddress']->city; ?>
                        @endif
                        {!! Form::text('city', old('city',$city), array('placeholder' => 'town name')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label>Country</label>
                        @if(isset($userData['cardAddress']->state))
                        <?php $state = $userData['cardAddress']->state; ?>
                        @endif
                        {!! Form::text('state', old('state',$state), array('placeholder' => 'country name')) !!}
                    </div>
                    <div class="form-group">
                        <label>PostCode</label>
                        @if(isset($userData['cardAddress']->pin))
                        <?php $pin = $userData['cardAddress']->pin; ?>
                        @endif
                        {!! Form::text('pin', old('pin',$pin), array('placeholder' => 'postcode')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="stickyfooter">
        <button>Save Details</button>
    </div>
    {!! Form::close() !!}
</section>
@endsection
@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
var savePaymentAddress = "{{ route('customer.savePayment') }}";
$(function () {
    //track intercom.io Add Payment Details event.
    var metaData = {};
    trackIntercomEvent('add-payment', metaData);
    $('input[name=cardExpirationDate]').on('keyup', function () {
        $(this).val($(this).val().replace(/\//, ''));
        var trim = this.value.substr(0, 4);  // "foo".substr(0, 140) == "foo"
        if (this.value != trim) {
            this.value = trim;
        }
    });
    $('input[name=cardNumber]').on('keyup', function (e) {
        var trim = this.value.substr(0, 16);  // "foo".substr(0, 140) == "foo"
        if (this.value != trim) {
            this.value = trim;
        }
    });
    $('input[name=cardCvx]').on('keyup', function (e) {
        var trim = this.value.substr(0, 3);  // "foo".substr(0, 140) == "foo"
        if (this.value != trim) {
            this.value = trim;
        }
    });

    $.validator.addMethod("validExpiryDate", function (value, element) {
        var today = new Date();
        var startDate = new Date(today.getFullYear(), today.getMonth(), 1, 0, 0, 0, 0);
        var expDate = value;
        expDate = expDate.substr(0, 2) + '/01/' + expDate.substr(2, 4);
        return Date.parse(startDate) <= Date.parse(expDate);
    },
            "Expiration Date must be set to future date.");
    $("#form-saveCard").validate({
        focusInvalid: true,
        debug: true,
        rules: {
            address: "required",
            city: 'required',
            state: 'required',
            pin: 'required',
            cardName: 'required',
            cardNumber: {
                required: true,
                minlength: 16,
                digits: true
            },
            cardExpirationDate: {
                required: true,
                //validExpiryDate: true,
            },
            cardCvx: {
                required: true,
                digits: true
            },
        },
        messages: {
            address: "Address missing",
            city: 'Town missing',
            state: 'Country missing',
            pin: 'Post code missing',
            cardName: 'Name missing',
            cardNumber: {
                required: 'Card number missing',
                minlength: "Invalid Card",
                digits: "this field can only contain numbers"
            },
            cardExpirationDate: {
                required: 'Expiration date missing'},
            cardCvx: {
                required: 'CVV missing',
                digits: "this field can only contain numbers"
            },
        },
        submitHandler: function (form) {
            var $form = $(form).serialize();
            $.ajax({
                url: savePaymentAddress,
                type: 'POST',
                data: $form,
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                success: function (data) {
                    if (data.status == 'success')
                        form.submit();
                    else if (data.status == 'error') {
                        $('#error-popup').html(data.error);
                        console.log(data.error);
                    } else
                        alert("some error occured please try again!! + Invalid condition");
                },
                error: function (data) {
                    console.log(data);
                    alert("some error occured please try again!!");
                }
            });
        }

    });
});
</script>
@endsection