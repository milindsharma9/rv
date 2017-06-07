@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/events.edit') }}</h1>

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
{!! Form::model($events, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array('admin.events.update', $events->id))) !!}

<div class="form-group">
    {!! Form::label('name', 'Name*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name',$events->name), array('class'=>'form-control')) !!}
        
    </div>
</div>
<div class="form-group">
    {!! Form::label('parent_id', 'Parent Theme', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('parent_id', $primaryEvents, $events->parent_id, array('class' => 'form-control')) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('floating_text', 'Floating Text', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('floating_text', old('floating_text',$events->floating_text), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('sub_text', 'Sub Text', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('sub_text', old('sub_text',$events->sub_text), ['class' => 'form-control']) !!}
    </div>
</div>

@php

$sortOrder = config('sort_order.sort_order');

@endphp
<div class="form-group">
    {!! Form::label('sort_order', 'Sort Order', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('sort_order', $sortOrder, $events->sort_order, array('class' => 'form-control')) !!}
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
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/events.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.events.index', trans('admin/events.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection