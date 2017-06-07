@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/locale.add_new') }}</h1>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
            </ul>
        </div>
        @endif
    </div>
</div>
{!! Form::open(array('files' => true, 'route' => 'admin.locale.store', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}
<div class="form-group">
    {!! Form::label('title', 'Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', old('title'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('url_path', 'Url Path*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('url_path', old('url_path'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('sub_title', 'Sub Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('sub_title', old('sub_title'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('description', 'Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('keywords', 'Keywords*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('keywords', old('keywords'), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('products', 'Products', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('products[]', [],old('products[]'), ['class' => 'tokenize-sample', 'multiple' => 'multiple', 'id' => 'products']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('bundles', 'Bundles', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('bundles', old('bundles'), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('recipies', 'Recipies', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('recipies', old('recipies'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('image', 'Image* (Preferred Size : 1024 * 270)', array('class'=>'col-sm-2 control-label')) !!}
    <!--{!! csrf_field() !!}-->
    <div class="col-sm-10">
        {!! Form::file('image') !!}
        {!! Form::hidden('image_w', 4096) !!}
        {!! Form::hidden('image_h', 4096) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('meta_title', 'Meta Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('meta_title', old('meta_title'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('meta_description', 'Meta Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('meta_description', old('meta_description'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('meta_keywords', 'Meta Keywords', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('meta_keywords', old('meta_keywords'), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('active', 'Active', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">        
        {!! Form::checkbox('active', '1', null, ['class' => '']) !!}
    </div>
</div>



<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
        {!! Form::submit(trans('admin/locale.create'), array('class' => 'btn btn-primary')) !!}
        {!! link_to_route('admin.locale.index', trans('admin/locale.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection

@section('javascript')
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="{{ url('css') }}/jquery-ui-timepicker-addon.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
<link rel="stylesheet" href="{{ url('css') }}/token-input.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
<link rel="stylesheet" href="{{ url('css') }}/token-input-facebook.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
<link rel="stylesheet" href="{{ url('css') }}/tokenize.css?v={{ env('ASSETS_VERSION_NUMBER') }}">

{{--WYSIWYG editor--}}
<script src="{{ url('vendor/unisharp/laravel-ckeditor') }}/ckeditor.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery-ui-sliderAccess.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery-ui-timepicker-addon.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery.tokeninput.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/tokenize.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/keyword_urlPath.js?v={{ env('ASSETS_VERSION_NUMBER') }}" type="text/javascript"></script>
<script src="{{ url('js') }}/locale.js?v={{ env('ASSETS_VERSION_NUMBER') }}" type="text/javascript"></script>
<script type="text/javascript">
    var keywordURL  = "{{ route('admin.blog.getkeyword')}}";
    var productURL  = "{{ route('admin.products.get.matching.products')}}";
    var bundleURL  = "{{ route('admin.bundles.get.matching')}}?recipe=0";
    var recipeURL  = "{{ route('admin.bundles.get.matching')}}?recipe=1";
    var basePath    = "{{ url('public')}}";
</script>
@endsection