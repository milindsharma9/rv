@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/categories.edit') }}</h1>

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
{!! Form::model($categories, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array('admin.categories.update', $categories->id))) !!}

<div class="form-group">
    {!! Form::label('name', 'Name*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name',$categories->name), array('class'=>'form-control')) !!}
        
    </div>
</div>
{!! Form::hidden('current_cat_id', $categories->id, array('id' => 'current_cat_id')) !!}

<div class="form-group">
    {!! Form::label('parent_id', 'Parent Category [L1]', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('parent_id', $primaryCategories, $selParentId, array('class' => 'form-control',
        'onchange' => 'populateSubcategories(this)')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('sub_category_id', 'Sub Category [L2]', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10" id="sub_cat_div">
        {!! Form::select('sub_category_id', $defaultSubCategory, $selSubcatId, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('image', 'Image', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('image') !!}
        {!! Form::hidden('image_w', 4096) !!}
        {!! Form::hidden('image_h', 4096) !!}
        
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/categories.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.categories.index', trans('admin/categories.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection
@section('javascript')
<script src="{{ url('quickadmin/js') }}/category.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>

@endsection