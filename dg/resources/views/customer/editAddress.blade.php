@section('title', 'Alchemy Wings - Online Store - Alcohol, Liquor & Food Delivery')
@section('meta_description', 'All day & late night London delivery of alcohol, drinks, food, snacks & tobacco. Order online now! We bring the bottle. You make the fun.')
@section('meta_keywords', '')
@section('title')
Alchemy - Address
@endsection
@extends('customer.layouts.customer')
@section('header')
@endsection
@section('content')
<section class="customer-content-section customer-address-edit-section">
    <div class="container">
    <div class="order-header">
    <h3 class="title"><a href="{{ url()->previous() }}" class="btn-red">< Back</a>Delivery Address</h3></div>
    @if($cartEmptyWarning)
        <div class="alert alert-danger">
            {{trans('messages.postcode_change_cart_empty_warning')}}<br />
            {{trans('messages.postcode_change_cart_empty_warning_postcode').$cartPostcode}}
        </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
        </ul>
    </div>
    @endif
    {!! Form::model($userData, array('files' => true, 'id' => 'form-customer-delivery-address', 'method' => 'POST', 'route' => array('customer.saveAddress'))) !!}
    <?php $userId = $userData['id']; ?>
    @if(isset($userData['userAddress']->fk_users_id))
    <?php $userId = $userData['userAddress']->fk_users_id; ?>
    @endif
    {!! Form::hidden('id', $userId) !!}
    <div class="payment-address-fields">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Address</label>
                    <?php $address = $city = $state = $pin = ''; ?>
                    @if(isset($userData['userAddress']->address))
                    <?php $address = $userData['userAddress']->address; ?>
                    @endif
                    {!! Form::text('address', old('address',$address), array('placeholder' => 'address name')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Town Name</label>
                    @if(isset($userData['userAddress']->city))
                    <?php $city = $userData['userAddress']->city; ?>
                    @endif
                    {!! Form::text('city', old('city',$city), array('placeholder' => 'town name')) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Country</label>
                    @if(isset($userData['userAddress']->state))
                    <?php $state = $userData['userAddress']->state; ?>
                    @endif
                    {!! Form::text('state', old('state',$state), array('placeholder' => 'country name')) !!}
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label>Postcode</label>
                    @if(isset($userData['userAddress']->pin))
                    <?php $pin = $userData['userAddress']->pin; ?>
                    @endif
                    {!! Form::text('pin', old('pin',$pin), array('placeholder' => 'postcode', 'id' => 'postcode_selected')) !!}
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
    var validPostCodeUrl              = "{!! route('customer.postcode.get'); !!}";
    $(function () {
        $("#form-customer-delivery-address").validate({
            focusInvalid: true,
            debug: true,
            rules: {
                address: 'required',
                city: 'required',
                state: 'required',
                pin: 'required',
            },
            submitHandler: function (form) {
                form.submit();
            }

        });
    });
    $(function() {
        function log( message ) {
            $( "<div>" ).text( message ).prependTo( "#postcode_selected" );
            $( "#postcode_selected" ).scrollTop( 0 );
        }
        $(document).on('keydown.autocomplete','#postcode_selected',function(){
            $(this).autocomplete({
                source: validPostCodeUrl,
                minLength: 1,
                select: function( event, ui ) {
                  log( ui.item);
                }
            });
        })
    });
</script>
@endsection