@extends('admin.layouts.master')

@section('content')
<?php $vendors = (isset($vendors[0])? $vendors[0]: $vendors); ?>

<div class="form-group">
    {!! Form::label('sel_sub_store_id', 'Selected Vendor Store', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('sel_sub_store_id', $aSubStores, $vendors->id, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/vendors.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
    
    <div class="col-sm-4 col-sm-12 col-sm-offset-2">
        <p>{!! link_to_route('admin.store.time', trans('Set Store Opening Time') ,  array($vendors->id) , array('class' => 'btn btn-success')) !!}</p>
        <p>{!! link_to_route('admin.vendors.manage.products', trans('Manage Store Products') ,  array($vendors->id) , array('class' => 'btn btn-success')) !!}</p>
        <p>&nbsp;</p>
    </div>
</div>
{!! Form::model($vendors, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array('admin.vendors.update', $vendors->id))) !!}
<div class="form-group">
    {!! Form::label('first_name', 'First Name*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('first_name', old('first_name',$vendors->first_name), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('last_name', 'Surname*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('last_name', old('last_name',$vendors->last_name), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        <input type="email" class="form-control" name="email" value="{{ old('email',$vendors->email) }}">
    </div>
</div>
@if (!strpos($vendors->email, config('appConstants.vendor_store_default_email_suffix')))
<div class="form-group">
    {!! Form::label('password', 'Password', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        <input type="password" class="form-control" name="password">
    </div>
</div>
<div class="form-group">
    {!! Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        <input type="password" class="form-control" name="password_confirmation">
    </div>
</div>
@endif
<div class="form-group">
    {!! Form::label('phone', 'Phone No', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('phone', old('phone',$vendors->phone), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('store_name', 'Store Name*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">        
        {!! Form::text('store_name', old('store_name',$vendors->store_name), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('address', 'Store Address*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('address', old('address',$vendors->address), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('city', 'Store Town*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('city', old('town',$vendors->town), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('state', 'Store country*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('state', old('country',$vendors->country), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('pin', 'Store PostCode*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('pin', old('post_code',$vendors->post_code), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('total_order', 'Orders', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('total_order', old('total_order',$vendors->salesData->total_order), ['class' => 'form-control', 'readonly' => 'readonly']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('total_value', 'TVOP (Total value of Order Processed)', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('total_value', old('total_value',$vendors->salesData->total_value), ['class' => 'form-control', 'readonly' => 'readonly']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('avg_order', 'AOV (Average Order value)', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('avg_order', old('avg_order',$vendors->salesData->avg_order), ['class' => 'form-control', 'readonly' => 'readonly']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('product_listed', 'Products', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('product_listed', old('phone',$vendors->product_listed), ['class' => 'form-control', 'readonly' => 'readonly']) !!}
    </div>
</div>
<?php 
if($vendors->store_status == 'ON') {
    $status = 1;
} else {
    $status = 0;
}

if($vendors->activte_status == 'ACTIVATED') {
    $storeActivateStatus = 1;
} else {
    $storeActivateStatus = 0;
}
?>
<div class="form-group">
    {!! Form::label('store_status', 'OFF/ON*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">        
        {!! Form::checkbox('store_status', '', $status, ['class' => '']) !!}
    </div>
</div>
@if (!strpos($vendors->email, config('appConstants.vendor_store_default_email_suffix')))
    <div class="form-group">
        {!! Form::label('activated', 'Activate Status*', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">        
            {!! Form::checkbox('activated', '1', $storeActivateStatus, ['class' => '']) !!}
        </div>
    </div>
@endif

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/vendors.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.vendors.index', trans('admin/vendors.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection

@section('javascript')
<script>
    var adminBaseUrl       = "{!! url('admin') !!}";
$(document).ready(function () {
    $(document).on('change', '#sel_sub_store_id', function () {
        var vendorId    = $(this).val();
        var url         = adminBaseUrl + "/vendors/" + vendorId + '/edit';
        window.location = url;
    });
});
</script>    
    
@endsection
