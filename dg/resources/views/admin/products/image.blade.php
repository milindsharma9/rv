@extends('admin.layouts.master')

@section('content')
<h1>{{ trans('admin/products.manage_products_image') }}</h1>
@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
        </ul>
        </div>
@endif
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/products.list') }}</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>Thumb Image</th>
                                    <th>Change Thumb</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>@if($thumb)<img src="{{ asset('uploads/'.$fileSubDir) . '/'.  $thumb->image }}">@endif</td>
                                    <td>
                                        {!! Form::open(array('files' => true,  'id' => 'image-upload-product', 'method' => 'POST','route' => array('admin.products.image.upload'))) !!}
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    @if($thumb)
                                                    <label>Change Image</label>
                                                    @else
                                                    <label>Add Image</label>
                                                    @endif
                                                    <input type="file" onchange="this.form.submit()" name="image"> 
                                                </div>
                                            </div>
                                            {!! Form::hidden('is_update', 1) !!}
                                            {!! Form::hidden('is_thumb', 1) !!}
                                            {!! Form::hidden('product_id', $productId) !!}
                                            @if($thumb)
                                            {!! Form::hidden('image_name', $thumb->image) !!}
                                            {!! Form::hidden('image_id', $thumb->id) !!}
                                            @endif
                                            {!! Form::hidden('image_w', 75) !!}
                                            {!! Form::hidden('image_h', 75) !!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            </tbody>
            </table>
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Primary</th>
                                    <th>Upload New</th>
                                    <th>Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productImages as $row)
                                <tr>
                                    <td>@if($row->image != '')<img src="{{ asset('uploads/'.$fileSubDir.'/thumb') . '/'.  $row->image }}">@endif</td>
                                    <td>{{ $row->primary }}</td>
                                    <td>
                                        {!! Form::open(array('files' => true,  'id' => 'image-upload-product', 'method' => 'POST','route' => array('admin.products.image.upload'))) !!}
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Upload Image</label>
                                                    <input type="file" onchange="this.form.submit()" name="image"> 
                                                </div>
                                            </div>
                                            {!! Form::hidden('is_update', 1) !!}
                                            {!! Form::hidden('product_id', $productId) !!}
                                            {!! Form::hidden('image_name', $row->image) !!}
                                            {!! Form::hidden('image_id', $row->id) !!}
                                            {!! Form::hidden('image_w', 8192) !!}
                                            {!! Form::hidden('image_h', 8192) !!}
                                        {!! Form::close() !!}
                                    </td>
                                    <td>
                                        {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('".trans("admin/products.are_you_sure")."');",  'route' => array('admin.products.image.setprimary', $row->id))) !!}
                                            {!! Form::hidden('product_id', $productId) !!}
                                            {!! Form::hidden('image_id', $row->id) !!}
                                            {!! Form::submit(trans('admin/products.set_primary'), array('class' => 'btn btn-xs btn-danger')) !!}
                                        {!! Form::close() !!}
                                        {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("admin/products.are_you_sure")."');",  'route' => array('admin.products.image.destroy', $row->id))) !!}
                                            {!! Form::hidden('product_id', $productId) !!}
                                            {!! Form::hidden('image_name', $row->image) !!}
                                            {!! Form::hidden('image_id', $row->id) !!}
                                            {!! Form::submit(trans('admin/products.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
            <div class="row">
                <div class="col-xs-12">
                    {!! Form::open(array('files' => true,  'id' => 'image-upload-product', 'method' => 'POST','route' => array('admin.products.image.upload'))) !!}
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label>Upload Image</label>
                                <input type="file" onchange="this.form.submit()" name="image"> 
                            </div>
                        </div>
                        {!! Form::hidden('is_update', 0) !!}
                        {!! Form::hidden('is_thumb', 0) !!}
                        {!! Form::hidden('product_id', $productId) !!}
                        {!! Form::hidden('image_w', 8192) !!}
                        {!! Form::hidden('image_h', 8192) !!}
                    {!! Form::close() !!}
                </div>
            </div><br /><br/>
            {!! Form::open(['route' => 'admin.products.massDelete', 'method' => 'post', 'id' => 'massDelete']) !!}
                <input type="hidden" id="send" name="toDelete">
                <input type="hidden" id="type" name="type">
            {!! Form::close() !!}
        </div>
</div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            $('#delete').click(function () {
                if (window.confirm('{{ trans('admin/products.are_you_sure') }}')) {
                    var send = $('#send');
                    var mass = $('.mass').is(":checked");
                    if (mass == true) {
                        send.val('mass');
                    } else {
                        var toDelete = [];
                        $('.single').each(function () {
                            if ($(this).is(":checked")) {
                                toDelete.push($(this).data('id'));
                            }
                        });
                        send.val(JSON.stringify(toDelete));
                        if (toDelete.length == 0) {
                            alert('Please select atleast one checkbox.');
                            return false;
                        }
                    }
                    $('#massDelete').submit();
                }
            });
            $('#active').click(function () {
                if (window.confirm('{{ trans('admin/products.are_you_sure') }}')) {
                    var send = $('#send');
                    var mass = $('.mass').is(":checked");
                    var type = $('#type').val('activate');
                    var toDelete = [];
                    $('.single').each(function () {
                        if ($(this).is(":checked")) {
                            toDelete.push($(this).data('id'));
                        }
                    });
                    send.val(JSON.stringify(toDelete));
                    if (toDelete.length == 0) {
                        alert('Please select atleast one checkbox.');
                        return false;
                    }
                    $('#massDelete').submit();
                }
            });
        });
    </script>
@stop