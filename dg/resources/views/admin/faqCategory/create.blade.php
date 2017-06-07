@extends('admin.layouts.master')

@section('content')
{{--{{ Html::ul($errors->all()) }}--}}
<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/faqcategory.create-add_new') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::open(array('files' => true, 'route' => 'admin.faq.category.save', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}


<div class="form-group">
    {!! Form::label('category_name', 'Category Name*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('category_name', old('category_name'), ['class' => 'form-control']) !!}
    </div>
</div>



<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
    {!! Form::submit(trans('admin/faq.create'), ['class' => 'btn btn-primary']) !!}
    {!! link_to_route('admin.faq.category.list', trans('admin/faqcategory.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection


