@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/faq.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::model($faq, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array('admin.faq.update', $faq->id))) !!}

<div class="form-group">
    {!! Form::label('category', 'Category Group*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('category', $faqCat, $faq->category, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('user_group', 'User Group*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        @foreach($userGroup as $id => $name)
            <label>{{$name}}</label> {{ Form::checkbox('user_group[]',$id,  in_array($id, $userGroupId)) }}
        @endforeach
    </div>
</div>
<div class="form-group">
    {!! Form::label('title', 'Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', old('title',$faq->title), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('description', 'Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', old('description',$faq->description), ['class' => 'form-control']) !!}
    </div>
</div>



<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/faq.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.faq.index', trans('admin/faq.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection

@section('javascript')
{{--WYSIWYG editor--}}
<script src="{{ url('vendor/unisharp/laravel-ckeditor') }}/ckeditor.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script type="text/javascript">
        CKEDITOR.replace( 'description' );
    </script>
@endsection