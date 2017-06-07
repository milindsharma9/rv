@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/users.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::model($users, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array('admin.users.update', $users->id))) !!}

<div class="form-group">
    {!! Form::label('fullname', 'Name*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('fullname', old('fullname',$users->fullname), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('companyname', 'Company Name*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('companyname', old('companyname',$users->companyname), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('address', 'Address*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('address', old('address',$users->address), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('email', 'Email*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
      {!! Form::email('email', old('email',$users->email), ['class' => 'form-control']) !!} 
    </div>
</div>

<div class="form-group">
    {!! Form::label('mobile', 'Mobile*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('mobile', old('mobile',$users->mobile), ['class' => 'form-control']) !!}
    </div>
</div>



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
<div class="form-group">
    {!! Form::label('activated', 'Active', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">        
        {!! Form::checkbox('activated', '1', $users->activated, ['class' => '']) !!}
    </div>
</div>
<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/users.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.users.index', trans('admin/users.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>


{!! Form::close() !!}

@endsection