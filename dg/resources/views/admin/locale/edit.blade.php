@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/locale.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::model($locale, array('files' => true, 'class' => 'form-horizontal', 'id' => 'locale-form-with-validation', 'method' => 'PATCH', 'route' => array('admin.locale.update', $locale->id))) !!}

<div class="form-group">
    {!! Form::label('title', 'Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', old('title', $locale->title), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('url_path', 'Url Path*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('url_path', old('url_path', $locale->url_path), ['class' => 'form-control']) !!}
    </div>
</div>


<div class="form-group">
    {!! Form::label('sub_title', 'Sub Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('sub_title', old('sub_title', $locale->sub_title), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('description', 'Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', old('description', $locale->description), ['class' => 'form-control']) !!}
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
        <select class="tokenize-sample" multiple="multiple" id="products" name="products[]">
            @foreach ($products as $product)
            <option value="{{$product->id}}" selected="selected">{{$product->description}}</option>
            @endforeach
        </select>
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
    {!! Form::label('image', 'Image (Preferred Size : 1024 * 270)', array('class'=>'col-sm-2 control-label')) !!}
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
        {!! Form::text('meta_title', old('meta_title', $locale->meta_title), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('meta_description', 'Meta Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('meta_description', old('meta_description', $locale->meta_description), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('meta_keywords', 'Meta Keywords', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('meta_keywords', old('meta_keywords', $locale->meta_keywords), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('active', 'Active', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">        
        {!! Form::checkbox('active', '1', $locale->active, ['class' => '']) !!}
    </div>
</div>



<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/locale.update'), array('class' => 'btn btn-primary')) !!}
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
<script src="{{ url('js') }}/jquery-ui-sliderAccess.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery-ui-timepicker-addon.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery.tokeninput.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/tokenize.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/keyword_urlPath.js?v={{ env('ASSETS_VERSION_NUMBER') }}" type="text/javascript"></script>
<script src="{{ url('js') }}/locale.js?v={{ env('ASSETS_VERSION_NUMBER') }}" type="text/javascript"></script>
<script type="text/javascript">
    var keywordURL      = "{{ route('admin.blog.getkeyword')}}";
    var keywordeditURL  = "{{ route('admin.blog.getsavedkeyword')}}";
    var productURL      = "{{ route('admin.products.get.matching.products')}}";
    var productSavedURL = "{{ route('admin.locale.get.saved.products')}}";
    var bundleURL       = "{{ route('admin.bundles.get.matching')}}?recipe=0";
    var recipeURL       = "{{ route('admin.bundles.get.matching')}}?recipe=1";
    var bundleSavedURL = "{{ route('admin.locale.get.saved.bundles')}}?bundle=1";
    var recipeSavedURL = "{{ route('admin.locale.get.saved.bundles')}}?bundle=0";
    var localeId            = "{{ $locale->id}}";
    var basePath            = "{{ url('public')}}";
</script>
@endsection