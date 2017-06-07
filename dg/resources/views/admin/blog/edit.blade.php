@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/blog.edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>
{!! Form::model($blog, array('files' => true, 'class' => 'form-horizontal', 'id' => 'blog-form-with-validation', 'method' => 'PATCH', 'route' => array('admin.blog.update', $blog->id))) !!}

<div class="form-group">
    {!! Form::label('type', 'Blog Type*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('type', config('blog.type'), $blog->type, ['class' => 'form-control', 'disabled' => 'true']) !!}
    </div>
</div>
{!! Form::hidden('blog_type', $blog->type) !!}

<div class="form-group">
    {!! Form::label('title', 'Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', old('title', $blog->title), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('sub_title', 'Sub Title*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('sub_title', old('sub_title', $blog->sub_title), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('url_path', 'Url Path*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('url_path', old('url_path', $blog->url_path), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('location', 'Location*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('location', old('location',$blog->location), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('address', 'Address*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('address', old('address',$blog->address), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('city', 'Town', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('city', old('town',$blog->city), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('state', 'country', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('state', old('country',$blog->state), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('pin', 'PostCode', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('pin', old('post_code',$blog->pin), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('event_ticket_text', 'Event Ticket Text*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('event_ticket_text', old('event_ticket_text',$blog->event_ticket_text), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('event_ticket_url', 'Event Ticket URL', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('event_ticket_url', old('event_ticket_url',$blog->event_ticket_url), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('places_drink_text', 'Place Text/Phone', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('places_drink_text', old('places_drink_text',$blog->places_drink_text), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('places_drink_url', 'Place URL', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('places_drink_url', old('places_drink_url',$blog->places_drink_url), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('places_food_text', 'Places Food Text', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('places_food_text', old('places_food_text',$blog->places_food_text), ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('places_food_url', 'Places Drink Text', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('places_food_url', old('places_food_url',$blog->places_food_url), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('start_date', 'Start Date*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('start_date', old('start_date', $blog->start_date), array('id' => 'date_start_date','class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('end_date', 'End Date', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('end_date', old('end_date', $blog->end_date), array('id' => 'date_end_date','class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('description', 'Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', old('description',$blog->description), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('keywords', 'Keywords', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('keywords', old('keywords'), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('image_thumb', 'Image Thumb (Preferred Size : 400 * 400)', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('image_thumb') !!}
        {!! Form::hidden('image_thumb_w', 4096) !!}
        {!! Form::hidden('image_thumb_h', 4096) !!}
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
        {!! Form::text('meta_title', old('meta_title', $blog->meta_title), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('meta_description', 'Meta Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('meta_description', old('meta_description', $blog->meta_description), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('meta_keywords', 'Meta Keywords', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('meta_keywords', old('meta_keywords', $blog->meta_keywords), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('published', 'Published', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">        
        {!! Form::checkbox('published', '1', $blog->published, ['class' => '']) !!}
    </div>
</div>



<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/blog.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.blog.index', trans('admin/blog.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection

@section('javascript')
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="{{ url('css') }}/jquery-ui-timepicker-addon.min.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
<link rel="stylesheet" href="{{ url('css') }}/token-input.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
<link rel="stylesheet" href="{{ url('css') }}/token-input-facebook.css?v={{ env('ASSETS_VERSION_NUMBER') }}">
{{--WYSIWYG editor--}}
<script src="{{ url('vendor/unisharp/laravel-ckeditor') }}/ckeditor.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery-ui-sliderAccess.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery-ui-timepicker-addon.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/jquery.tokeninput.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/keyword_urlPath.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script src="{{ url('js') }}/blog-master.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script type="text/javascript">
    var blogType        = "{{ $blog->type }}";
    var keywordURL      = "{{ route('admin.blog.getkeyword')}}";
    var keywordeditURL  = "{{ route('admin.blog.getsavedkeyword')}}";
    var blogId          = "{{ $blog->id}}";
    var typeBlog        = "{{ config('blog.type_blog')}}";
    var typeEvent       = "{{ config('blog.type_event')}}";
    var typePlace       = "{{ config('blog.type_place')}}";
    var basePath        = "{{ url('public')}}";
</script>
@endsection