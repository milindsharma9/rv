@extends('admin.layouts.master')

@section('content')
{{--{{ Html::ul($errors->all()) }}--}}
<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/faq.create-add_new') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::open(array('files' => true, 'route' => 'admin.faq.store', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}
<div class="form-group">
    {!! Form::label('category', 'Category Group*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('category', $faqCat, null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('user_group', 'User Group*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        @foreach($userGroup as $id => $name)
            <label>{{$name}}</label> {!! Form::checkbox('user_group[]',$id,  '') !!}
        @endforeach
    </div>
</div>
<div class="form-group">
    {!! Form::label('title', 'Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', old('title'), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('description', 'Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
    </div>
</div>


<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
    {!! Form::submit(trans('admin/faq.create'), ['class' => 'btn btn-primary']) !!}
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

