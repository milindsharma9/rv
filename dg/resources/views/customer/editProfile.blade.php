@section('title')
Alchemy - Edit Profile
@endsection
@extends('customer.layouts.customer')
@section('header')
<a href="{{ route('customer.dashboard') }}">Edit Profile</a>
@endsection
<style>
    .customer-template .store-description img {
    display: none;
}
</style>
@section('content')
<section class="customer-content-section store-profile-edit-section customer-profile-edit-section">
    <div class="container">
        {!! Form::open(array('files' => true,  'id' => 'image-upload', 'method' => 'POST','route' => array('customer.uploadImage'))) !!}
        <span class="btn-pro-pic">
            <input type="file" onchange="this.form.submit()" name="image">
        </span>
        {!! Form::hidden('image_w', 8192) !!}
        {!! Form::hidden('image_h', 8192) !!}
        {!! Form::close() !!}
        <div class="panel panel-default">
            <div class="panel-body">
                <?php $storeBannerImage = asset('alchemy/images/profile-banner.jpg'); ?>
                {!! Form::model($user, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-customer-profile', 'method' => 'POST', 'route' => array('customer.saveProfile'))) !!}
                {!! Form::hidden('id', $user->id) !!}
                <div class="row store-description">
                    {!! Html::image($storeBannerImage, 'a picture', array('id' => 'cropimageBanner', 'class' => 'store-banner-image')) !!}
                    @if(isset($user->image) && $user->image != '')
                    <img src="{{ asset('uploads/'.$fileSubDir) . '/'. $user->image }}" class="store-image">
                    @elseif ($user->image == '')
                    <img src="{{ asset('alchemy/images/default-store.png')}}" class="store-image">
                    @endif
                </div>
                <div class="order-header hidden-xs">
                    <h3 class="title"><a href="{{ route('customer.dashboard') }}" class="btn-red">&lt; Back</a>Edit Details</h3>
                </div>
                <div class="row order-header visible-xs">
                    <h3 class="title"><a href="{{ route('customer.dashboard') }}" class="btn-red">&lt; Back</a>Edit Details</h3>
                </div>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                    </ul>
                </div>
                @endif
                <div class="form-group">
                    {!! Form::label('first_nameL', 'First Name*', array('class'=>'col-xs-12 control-label')) !!}
                    <div class="col-xs-12">
                        {!! Form::text('first_name', old('name',$user->first_name), array('class'=>'form-control')) !!}

                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('last_nameL', 'Last Name*', array('class'=>'col-xs-12 control-label')) !!}
                    <div class="col-xs-12">
                        {!! Form::text('last_name', old('name',$user->last_name), array('class'=>'form-control')) !!}

                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('email', 'Email', array('class'=>'col-xs-12 control-label')) !!}
                    <div class="col-xs-12">
                        {!! Form::text('email', old('email',$user->email), array('class'=>'form-control', 'readonly' => 'readonly')) !!}

                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('phone', 'Phone', array('class'=>'col-xs-12 control-label')) !!}
                    <div class="col-xs-12">
                        {!! Form::text('phone', old('phone',$user->phone), array('class'=>'form-control')) !!}

                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        {!! Form::submit('Update', array('class' => 'btn btn-submit-profile')) !!}
                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</section>
@endsection
@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
    $(function () {
        $("#form-customer-profile").validate({
            focusInvalid: true,
            debug: true,
            rules: {
                first_name: 'required',
                last_name: 'required',
                phone: {
                    digits:true,
                    rangelength:[7,15],
                    required:true,
                },
            },
            submitHandler: function (form) {
                form.submit();
            }

        });
    });
</script>
@stop