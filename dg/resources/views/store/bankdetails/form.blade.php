@extends('store.layouts.products')
@section('header')
Edit Profile
@endsection
@section('content')
<section class="store-content-section store-payment-edit-section">
    <div class="container">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
            </ul>
        </div>
        @endif
        
        {!! Form::open(array('files' => true, 'route' => 'store.bank.update', 'id' => 'form-with-validation', 'class' => '')) !!}
        <div class="row">
            <div class="col-xs-12 col-sm-6 form-group">
                    {!! Form::label('name', 'OwnerName*', ['class' => 'control-label']) !!}
                    {!! Form::text('owner_name', '', ['class' => 'form-control']) !!}
            </div>
            <div class="col-xs-12 col-sm-6 form-group">
                {!! Form::label('name', 'AccountNumber*', ['class' => 'control-label']) !!}
                {!! Form::text('account_number', '', ['class' => 'form-control']) !!}
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-12 col-sm-6 form-group">
                {!! Form::label('name', 'BIC*', ['class' => 'control-label']) !!}
                {!! Form::text('bic', '', ['class' => 'form-control']) !!}
            </div>
            <div class="col-xs-12 col-sm-6 form-group">
                {!! Form::label('name', 'Card Country*', ['class' => 'control-label']) !!}
                {!! Form::select('card_country', $country, null, array('class' => '')) !!}
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-12 col-sm-6 form-group">
                {!! Form::label('name', 'AddressLine1*', ['class' => 'control-label']) !!}
                {!! Form::text('address_1', '', ['class' => 'form-control']) !!}
            </div>
            <div class="col-xs-12 col-sm-6 form-group">
                {!! Form::label('name', 'AddressLine2*', ['class' => 'control-label']) !!}
                {!! Form::text('address_2', '', ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6 form-group">
                {!! Form::label('name', 'City*', ['class' => 'col-sm-2 control-label']) !!}
                {!! Form::text('city', '', ['class' => 'form-control']) !!}
            </div>
            <div class="col-xs-12 col-sm-6 form-group">
                {!! Form::label('name', 'Region*', ['class' => 'col-sm-2 control-label']) !!}
                {!! Form::text('region', '', ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6 form-group">
                {!! Form::label('name', 'PostalCode*', ['class' => 'col-sm-2 control-label']) !!}
                {!! Form::text('postal_code', '', ['class' => 'form-control']) !!}
            </div>
            <div class="col-xs-12 col-sm-6 form-group">
                {!! Form::label('name', 'Country*', ['class' => 'col-sm-2 control-label']) !!}
                {!! Form::select('country', $country, null, array('class' => '')) !!}
            </div>
        </div>

        <div class="stickyfooter">
            {!! Form::submit(trans('Create'), ['class' => 'btn-red']) !!}
        </div>

        {!! Form::close() !!}

    </div>
</section>
@endsection