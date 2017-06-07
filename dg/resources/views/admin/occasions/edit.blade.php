@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/occasions.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{{--{{ Html::ul($errors->all()) }}--}}
{!! Form::model($occasions, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array('admin.occasions.update', $occasions->id))) !!}

<div class="form-group">
    {!! Form::label('name', 'Name*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name',$occasions->name), array('class'=>'form-control')) !!}
        
    </div>
</div>
<div class="form-group">
    {!! Form::label('parent_id', 'Parent Occasion', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('parent_id', $primaryOccasions, $occasions->parent_id, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('floating_text', 'Floating Text', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('floating_text', old('floating_text',$occasions->floating_text), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('sub_text', 'Sub Text', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('sub_text', old('sub_text',$occasions->sub_text), ['class' => 'form-control']) !!}
    </div>
</div>

@php

$sortOrder = config('sort_order.sort_order');

@endphp
<div class="form-group">
    {!! Form::label('sort_order', 'Sort Order', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('sort_order', $sortOrder, $occasions->sort_order, array('class' => 'form-control')) !!}
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
        {!! Form::checkbox('is_banner','', $occasions->is_banner, ['id' => 'setBanner']) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/occasions.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.occasions.index', trans('admin/occasions.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection