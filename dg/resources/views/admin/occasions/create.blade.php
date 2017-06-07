@extends('admin.layouts.master')

@section('content')
{{--{{ Html::ul($errors->all()) }}--}}
<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/occasions.create-add_new') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::open(array('files' => true, 'route' => 'admin.occasions.store', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}
<div class="form-group">
    {!! Form::label('name', 'Name*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('parent_id', 'Parent Occasion', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('parent_id', $primaryOccasions, null, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('floating_text', 'Floating Text', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('floating_text', old('floating_text'), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('sub_text', 'Sub Text', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('sub_text', old('sub_text'), ['class' => 'form-control']) !!}
    </div>
</div>

@php

$sortOrder = config('sort_order.sort_order');

@endphp
<div class="form-group">
    {!! Form::label('sort_order', 'Sort Order', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('sort_order', $sortOrder, null, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('image', 'Image (Preferred Size : 600 * 400)', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('image') !!}
        {!! Form::hidden('image_w', 4096) !!}
        {!! Form::hidden('image_h', 4096) !!}
        
    </div>
</div>

<div class="form-group">
    {!! Form::label('image_banner', 'Image Banner (Preferred Size : 1024 * 260)', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('image_banner') !!}
        {!! Form::hidden('image_banner_w', 4096) !!}
        {!! Form::hidden('image_banner_h', 4096) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('image_logo', 'Logo Image', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('image_logo') !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('is_banner', 'Set as banner', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::checkbox('is_banner','',  old('is_banner'), ['id' => 'setBanner']) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
    {!! Form::submit(trans('admin/occasions.create'), ['class' => 'btn btn-primary']) !!}
        </div>
</div>

{!! Form::close() !!}

@stop

