@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/brand.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::model($brand, array('files' => true, 'class' => 'form-horizontal', 'id' => 'brand-form-with-validation', 'method' => 'PATCH', 'route' => array('admin.brand.update', $brand->id))) !!}

<div class="form-group">
    {!! Form::label('title', 'Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', old('title', $brand->title), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('url_path', 'Url Path*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('url_path', old('url_path', $brand->url_path), ['class' => 'form-control']) !!}
    </div>
</div>


<div class="form-group">
    {!! Form::label('sub_title', 'Sub Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('sub_title', old('sub_title', $brand->sub_title), ['class' => 'form-control']) !!}
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
    {!! Form::label('image', 'Image1*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-3">
        {!! Form::file('image') !!}
        {!! Form::hidden('image_w', 4096) !!}
        {!! Form::hidden('image_h', 4096) !!}
    </div>
    <div class="col-sm-2">@if($brand->image != '')
        <img src="{{ asset('uploads/'.$fileSubDir.'/thumb') . '/'.  $brand->image }}">
<!--        <div id="image-delete-{{str_replace('.', '-', $brand->image)}}" data-name="image" data-brandId="{{$brand->id}}" data-imageName="{{$brand->image}}">X</div>-->
        @endif</div>
</div>
<div class="form-group">
    {!! Form::label('image2', 'Image2', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-3">
        {!! Form::file('image2') !!}
        {!! Form::hidden('image2_w', 4096) !!}
        {!! Form::hidden('image2_h', 4096) !!}
    </div>
    <div class="col-sm-2">@if($brand->image2 != '')<img src="{{ asset('uploads/'.$fileSubDir.'/thumb') . '/'.  $brand->image2 }}">
        <div id="image-delete-{{str_replace('.', '-',$brand->image2)}}" data-name="image2" data-brandId="{{$brand->id}}" data-imageName="{{$brand->image2}}">X</div>
        @endif</div>
</div>
<div class="form-group">
    {!! Form::label('image3', 'Image3', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-3">
        {!! Form::file('image3') !!}
        {!! Form::hidden('image3_w', 4096) !!}
        {!! Form::hidden('image3_h', 4096) !!}
    </div>
    <div class="col-sm-2">@if($brand->image3 != '')<img src="{{ asset('uploads/'.$fileSubDir.'/thumb') . '/'.  $brand->image3 }}">
        <div id="image-delete-{{str_replace('.', '-',$brand->image3)}}" data-name="image3" data-brandId="{{$brand->id}}" data-imageName="{{$brand->image3}}">X</div>
        @endif</div>
</div>
<div class="form-group">
    {!! Form::label('image4', 'Image4', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-3">
        {!! Form::file('image4') !!}
        {!! Form::hidden('image4_w', 4096) !!}
        {!! Form::hidden('image4_h', 4096) !!}
    </div>
    <div class="col-sm-2">@if($brand->image4 != '')<img src="{{ asset('uploads/'.$fileSubDir.'/thumb') . '/'.  $brand->image4 }}">
        <div id="image-delete-{{str_replace('.', '-',$brand->image4)}}" data-name="image4" data-brandId="{{$brand->id}}" data-imageName="{{$brand->image4}}">X</div>
        @endif</div>
</div>

<div class="form-group">
    {!! Form::label('image_background', 'Image Background (Preferred Size : 1200 * 768)', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('image_background') !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('meta_title', 'Meta Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('meta_title', old('meta_title', $brand->meta_title), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('meta_description', 'Meta Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('meta_description', old('meta_description', $brand->meta_description), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('meta_keywords', 'Meta Keywords', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('meta_keywords', old('meta_keywords', $brand->meta_keywords), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('button_text', 'Button Text', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('button_text', old('button_text', $brand->button_text), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('button_url', 'Button Url', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('button_url', old('button_url', $brand->button_url), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('is_external', 'Open Link In New Window', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">        
        {!! Form::checkbox('is_external', '1', $brand->is_external, ['class' => '']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('active', 'Active', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">        
        {!! Form::checkbox('active', '1', $brand->active, ['class' => '']) !!}
    </div>
</div>



<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/brand.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.brand.index', trans('admin/brand.cancel'), null, array('class' => 'btn btn-default')) !!}
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
    var productSavedURL = "{{ route('admin.brand.get.saved.products')}}";
    var bundleURL       = "{{ route('admin.bundles.get.matching')}}?recipe=0";
    var recipeURL       = "{{ route('admin.bundles.get.matching')}}?recipe=1";
    var bundleSavedURL  = "{{ route('admin.brand.get.saved.bundles')}}?bundle=1";
    var recipeSavedURL  = "{{ route('admin.brand.get.saved.bundles')}}?bundle=0";
    var localeId        = "{{ $brand->id}}";
    var basePath        = "{{ url('public')}}";
    var deleteImageURL  = "{{ route('admin.brand.deleteImage')}}";
</script>
@endsection