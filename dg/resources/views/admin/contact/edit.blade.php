@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/contact.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::model($contact, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array('admin.contact.update', $contact->id))) !!}

<div class="form-group">
    {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name',$contact->name), ['class' => 'form-control', 'disabled' => true]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('email', old('email',$contact->email), ['class' => 'form-control', 'disabled' => true]) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('message', 'Message', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('message', old('message', $contact->message), ['class' => 'form-control', 'disabled' => true]) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('feedback', 'Feedback', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('feedback', old('feedback', $contact->feedback), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/contact.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.contact.index', trans('admin/contact.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection
