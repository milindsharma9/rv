@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('admin/bundles.edit') }}</h1>

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
{!! Form::model($bundles, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array('admin.bundles.update', $bundles->id))) !!}

<div class="form-group">
    {!! Form::label('name', 'Name*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name',$bundles->name), array('class'=>'form-control')) !!}
        
    </div>
</div>

<div class="form-group">
    {!! Form::label('name', 'Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('description', old('description', $bundles->description), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('serves', 'Serves Description*', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('serves', old('serves', $bundles->serves), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('is_recipe', 'Recipe', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::checkbox('is_recipe','1',  $bundles->is_recipe, []) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('is_popular', 'Is Popular', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::checkbox('is_popular','1',  $bundles->is_popular, []) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('is_gift', 'Is Gift', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::checkbox('is_gift','1',  $bundles->is_gift, []) !!}
    </div>
</div>

{{--<div class="form-group">
    {!! Form::Label('item', 'Item:') !!}
    {!! Form::select('item_id', $items, null, ['class' => 'form-control']) !!}
</div>--}}

<div class="form-group">
    {!! Form::label('image_thumb', 'Image Thumb', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('image_thumb') !!}
        {!! Form::hidden('image_thumb_w', 4096) !!}
        {!! Form::hidden('image_thumb_h', 4096) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('image', 'Image', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::file('image') !!}
        {!! Form::hidden('image_w', 4096) !!}
        {!! Form::hidden('image_h', 4096) !!}
         {!! Form::hidden('bundleId', $bundles->id) !!}
        {!! Form::button('Add Product', ['id' => 'addProd']) !!}
        <table id="product" class="data display" border="2pt">
            <thead>
                <tr><th class="dish-select" rowspan="1" colspan="1">Products</th>
                    <th class="quantity" rowspan="1" colspan="1">Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            @if(isset($prodMapping))
            <?php $counter = 1; ?>
            @foreach ($prodMapping['productSelect'] as $key => $product)
            <tr>
                 <td class="product-select-id" data-id="{!! $counter !!}">{!! $prodMapping['name'][$key] !!}</td><?php $counter++; ?>
                 <td>{!! $prodMapping['quantity'][$key] !!}</td>
                 <td class="delrow" id={!! $product !!}><a class="link">
                         {!! Html::image("image/red_cross.png", "Remove this row", array("width" => 20, "height" => 20, 'class' => '')) !!}</a></td>
             </tr>
            @endforeach
            @endif
        </table>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('admin/bundles.update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route('admin.bundles.index', trans('admin/bundles.cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection
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