@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/faqcategory.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::model($faq, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'POST', 'route' => array('admin.faq.category.update'))) !!}

<div class="form-group">
    {!! Form::label('category_name', 'Category Name*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('category_name', old('category_name',$faq->category_name), ['class' => 'form-control']) !!}
    </div>
</div>
{!! Form::hidden('id', $faq->id) !!}
<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/faqcategory.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.faq.category.list', trans('admin/faqcategory.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection
