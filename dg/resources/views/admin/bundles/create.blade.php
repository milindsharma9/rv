@extends('admin.layouts.master')

@section('content')
{{--{{ Html::ul($errors->all()) }}--}}
<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/bundles.create-add_new') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::open(array('files' => true, 'route' => 'admin.bundles.store', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}
<div class="form-group">
    {!! Form::label('name', 'Name*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('name', 'Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('serves', 'Serves Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('serves', old('serves'), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('is_recipe', 'Recipe', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::checkbox('is_recipe','1',  old('is_recipe'), []) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('is_popular', 'Is Popular', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::checkbox('is_popular','1',  old('is_popular'), []) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('is_gift', 'Is Gift', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::checkbox('is_gift','1',  old('is_gift'), []) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('image_thumb', 'Image Thumb', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('image_thumb') !!}
        {!! Form::hidden('image_thumb', 4096) !!}
        {!! Form::hidden('image_thumb', 4096) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('image', 'Image', array('class'=>'col-sm-2 control-label')) !!}
    <!--{!! csrf_field() !!}-->
    <div class="col-sm-10">
        {!! Form::file('image') !!}
        {!! Form::hidden('image_w', 4096) !!}
        {!! Form::hidden('image_h', 4096) !!}
        {!! Form::button('Add Product', ['id' => 'addProd']) !!}
        <table id="product" class="data display" border="2pt">
            <thead>
                <tr><th class="dish-select" rowspan="1" colspan="1">Products</th>
                    <th class="quantity" rowspan="1" colspan="1">Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
    {!! Form::submit(trans('admin/bundles.create'), ['class' => 'btn btn-primary']) !!}
        </div>
</div>

{!! Form::close() !!}

@stop
@section('javascript')
<script>
var myUrl = "{{ url('/admin/products/getProducts') }}";
var deleteUrl = "{{ url('/admin/bundles/removeMapping') }}";
var imgUrl = '{{ Html::image("image/red_cross.png", "Remove this row", array("width" => 20, "height" => 20)) }}';
var onLoad = true;
</script>
<script src="{{ url('js') }}/bundle.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
{{--WYSIWYG editor--}}
<script src="{{ url('vendor/unisharp/laravel-ckeditor') }}/ckeditor.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
    <script>
        CKEDITOR.replace( 'description' );
    </script>
@stop
