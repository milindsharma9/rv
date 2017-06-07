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

{!! Form::open(array('files' => true,'method' => 'PATCH',  'route' => array('admin.manuals.update',$manuals->id), 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}
<div class="form-group">
    {!! Form::label('title', 'Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
	
        {!! Form::text('title', old('title',$manuals->title), ['class' => 'form-control']) !!}
    </div>
</div>
<!--div class="form-group">
    {!! Form::label('description', 'Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
    </div>http://ckeditor.com/apps/ckeditor/4.4.0/samples/plugins/toolbar/toolbar.html#currentToolbar
</div-->


<div class="form-group">
 <script src="{{ url('/') }}/ckeditor/ckeditor.js"></script>
  {!! Form::label('description', 'Description*', ['class' => 'col-sm-2 control-label']) !!}
 <div class="col-sm-10">
  <textarea name="description" id="description" rows="10" cols="30" >@if (Input::old('description')!='') {{ Input::old('description') }} @else {{ $manuals->description }} @endif</textarea>
			</div>
    <script>
               CKEDITOR.replace( 'description', {
	toolbarGroups: [
		{ name: 'document',	   groups: [ 'mode', 'document' ] },			// Displays document group with its two subgroups.
 		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },			// Group's name will be used to create voice label.
 		'/',																// Line break - next group will be placed in new line.
 		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
 		{ name: 'links' }
	] });
            </script>
    
    
</div>

<div class="form-group">
    {!! Form::label('filename', 'Pdf File*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('filename') !!}
       @if($manuals->filename!='')
		
	{{ Html::link(url('/') . "/" . $filepath . "/" .$manuals->filename  , 'View File',['target'=>'_blank'])}}
		
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
      {!! Form::submit(trans('admin/manual.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.manuals.index', trans('admin/manual.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>


{!! Form::close() !!}

@endsection


