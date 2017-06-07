@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/manual.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>

{!! Form::open(array('files' => true,'method' => 'PATCH',  'route' => array('admin.clients.update',$clients->id), 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}
<div class="form-group">
    {!! Form::label('title', 'Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
	
        {!! Form::text('title', old('title',$clients->title), ['class' => 'form-control']) !!}
    </div>
</div>
<!--div class="form-group">
    {!! Form::label('description', 'Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
    </div>http://ckeditor.com/apps/ckeditor/4.4.0/samples/plugins/toolbar/toolbar.html#currentToolbar
</div-->



<div class="form-group">
    {!! Form::label('filename', 'image File*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('filename') !!}
       @if($clients->filename!='')
		
	{{ Html::link(url('/') . "/" . $filepath . "/" .$clients->filename  , 'View File',['target'=>'_blank'])}}
		
		@endif
    </div>
	
</div>


<div class="form-group">
    {!! Form::label('published', 'Publish', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">        
        {!! Form::checkbox('published', '1', 1, ['class' => '']) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/client.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.clients.index', trans('admin/client.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>


{!! Form::close() !!}

@endsection


