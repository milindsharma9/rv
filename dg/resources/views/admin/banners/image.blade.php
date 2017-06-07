@extends('admin.layouts.master')

@section('content')

@php
    $aBannerTypeMaster = config('banner.type');
    $selectedbannerLabel = $aBannerTypeMaster[$type];
@endphp

<h1>{{ trans('admin/banners.manage_banner_images') }} {{$selectedbannerLabel}} {{ trans('admin/banners.images') }}</h1>


<div class="form-group row">
    {!! Form::label('banner_type_master', 'Banner Type', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10" id="sub_cat_div">
        {!! Form::select('banner_type_master', $aBannerTypeMaster, $type, array('class' => 'form-control')) !!}
    </div>
</div>

@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
        </ul>
        </div>
@endif
<div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">{{ trans('admin/banners.list') }}</div>
        </div>
        <div class="portlet-body">
            @php
                $bannerTypeLanding = config('banner.banner_type_landing');
            @endphp
            @if($type == $bannerTypeLanding)
                <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                    <thead>
                        <tr>
                            <th>Mobile Image</th>
                            <th>Change Mobile Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                @if (!empty($mobileImage))
                                    <img src="{{ asset('uploads/'.$fileSubDir) . '/thumb/'.  $mobileImage->image }}">
                                @endif
                            </td>
                            <td>
                                {!! Form::open(array('files' => true,  'id' => 'image-upload-product', 'method' => 'POST','route' => array('admin.banners.upload'))) !!}
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="form-group">
                                            @if (!empty($mobileImage))
                                            <label>Change Image</label>
                                            @else
                                            <label>Add Image</label>
                                            @endif
                                            <input type="file" onchange="this.form.submit()" name="image"> 
                                        </div>
                                    </div>
                                    {!! Form::hidden('is_mobile', 1) !!}
                                    {!! Form::hidden('banner_type', $type) !!}
                                    @if (!empty($mobileImage))
                                        {!! Form::hidden('image_name', $mobileImage->image) !!}
                                        {!! Form::hidden('image_id', $mobileImage->id) !!}
                                        {!! Form::hidden('is_update', 1) !!}
                                    @else
                                        {!! Form::hidden('is_update', 0) !!}
                                    @endif
                                    {!! Form::hidden('image_w', 8192) !!}
                                    {!! Form::hidden('image_h', 8192) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endif
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
                                @foreach ($bannerImages as $row)
                                <tr>
                                    <td>@if($row->image != '')<img src="{{ asset('uploads/'.$fileSubDir.'/thumb') . '/'.  $row->image }}">@endif</td>
                                    <td>{{ $row->primary }}</td>
                                    <td>
                                        {!! Form::open(array('files' => true,  'id' => 'image-upload-product', 'method' => 'POST','route' => array('admin.banners.upload'))) !!}
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Upload Image</label>
                                                    <input type="file" onchange="this.form.submit()" name="image"> 
                                                </div>
                                            </div>
                                            {!! Form::hidden('is_update', 1) !!}
                                            {!! Form::hidden('banner_type', $type) !!}
                                            {!! Form::hidden('image_name', $row->image) !!}
                                            {!! Form::hidden('image_id', $row->id) !!}
                                            {!! Form::hidden('image_w', 8192) !!}
                                            {!! Form::hidden('image_h', 8192) !!}
                                        {!! Form::close() !!}
                                    </td>
                                    <td>
                                        {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('".trans("admin/banners.are_you_sure")."');",  'route' => array('admin.banners.setprimary', $row->id))) !!}
                                            {!! Form::hidden('banner_type', $type) !!}
                                            {!! Form::hidden('image_id', $row->id) !!}
                                            {!! Form::submit(trans('admin/banners.set_primary'), array('class' => 'btn btn-xs btn-danger')) !!}
                                        {!! Form::close() !!}
                                        {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("admin/banners.are_you_sure")."');",  'route' => array('admin.banners.destroy', $row->id))) !!}
                                            {!! Form::hidden('banner_type', $type) !!}
                                            {!! Form::hidden('image_name', $row->image) !!}
                                            {!! Form::hidden('image_id', $row->id) !!}
                                            {!! Form::hidden('primary', $row->primary) !!}
                                            {!! Form::submit(trans('admin/banners.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                                 @endforeach
                            </tbody>
            </table>
            <div class="row">
                <div class="col-xs-12">
                    {!! Form::open(array('files' => true,  'id' => 'image-upload-product', 'method' => 'POST','route' => array('admin.banners.upload'))) !!}
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label>Upload Image</label>
                                <input type="file" onchange="this.form.submit()" name="image"> 
                            </div>
                        </div>
                        {!! Form::hidden('is_update', 0) !!}
                        {!! Form::hidden('banner_type', $type) !!}
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
        var bannerBaseUrl = "{!! route('admin.banners.list')!!}";
        
        $(document).ready(function () {
            $('#delete').click(function () {
                if (window.confirm('{{ trans('admin/banners.are_you_sure') }}')) {
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
                if (window.confirm('{{ trans('admin/banners.are_you_sure') }}')) {
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

            $('#banner_type_master').on('change',function(){
                var bannerType = $(this).val();
                var url = bannerBaseUrl + "/" + bannerType;
                window.location = url;
            });
        });
    </script>
@stop