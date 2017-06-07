@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/postcode.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::model($validPostcodes, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array('admin.postcode.update', $validPostcodes->id))) !!}

<div class="form-group">
    {!! Form::label('postcode', 'Postcode*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('postcode', old('postcode',$validPostcodes->postcode), array('class'=>'form-control')) !!}
        
    </div>
</div>


<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/postcode.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.postcode.index', trans('admin/postcode.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection