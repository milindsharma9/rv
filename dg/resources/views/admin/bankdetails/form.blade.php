@extends('admin.layouts.master')
@section('content')
{{--{{ Html::ul($errors->all()) }}--}}
<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('Provide Bank Account Details') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::open(array('files' => true, 'route' => 'admin.bank.update', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}
<div class="form-group">
    {!! Form::label('name', 'OwnerName*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('owner_name', '', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('name', 'AccountNumber*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('account_number', '', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('name', 'BIC*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('bic', '', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('name', 'Card Country*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('card_country', $country, null, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('name', 'AddressLine1*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('address_1', '', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('name', 'AddressLine2*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('address_2', '', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('name', 'City*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('city', '', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('name', 'Region*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('region', '', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('name', 'PostalCode*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('postal_code', '', ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('name', 'Country*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('country', $country, null, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
        {!! Form::submit(trans('admin/events.create'), ['class' => 'btn btn-primary']) !!}
    </div>
</div>

{!! Form::close() !!}

@stop

