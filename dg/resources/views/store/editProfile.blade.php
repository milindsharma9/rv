@extends('store.layouts.products')
@section('header')
Edit Profile
@endsection
@section('content')
<section class="store-description-section store-profile-edit-section">
    <div class="container">
        {!! Form::open(array('files' => true,  'id' => 'image-upload', 'method' => 'POST','route' => array('store.uploadImage'))) !!}
        <span class="btn-pro-pic">
            <input type="file" onchange="this.form.submit()" name="image">
        </span>
        {!! Form::hidden('image_w', 8192) !!}
        {!! Form::hidden('image_h', 8192) !!}
        {!! Form::close() !!}
        <div class="panel panel-default">
            <div class="panel-body">
                {!! Form::model($userData, array('files' => true, 'class' => '', 'id' => 'form-store-profile', 'method' => 'POST', 'route' => array('store.saveProfile'))) !!}
                {!! Form::hidden('id', $userData->id) !!}
                <div class="row store-description">
                    <img src="{{ asset('alchemy/images/profile-banner.jpg') }}" class="store-banner-image">
                    @if($userData->image != '')
                        <img src="{{ asset('uploads/'.$fileSubDir) . '/'.  $userData->image }}" id="cropimage" alt="pic" class="store-image">
                        @else
                        <img src="{{ asset('alchemy/images/default-store.png')}}" class="store-image">
                        @endif
                </div>
                <div class="row order-header">
                    <h3 class="title"><a href="{{ route('store.profile') }}" class="btn-red">&lt; Back</a>Edit Details</h3>
                </div>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                    </ul>
                </div>
                @endif
                <div class="form-group row">
                    {!! Form::label('first_name', 'Owner\'s First Name', array('class'=>'col-xs-12 control-label')) !!}
                    <div class="col-xs-12">
                        {!! Form::text('first_name', old('first_name',$userData->first_name), array('class'=>'form-control')) !!}

                    </div>
                </div>

                <div class="form-group row">
                    {!! Form::label('last_name', 'Owner\'s Last Name', array('class'=>'col-xs-12 control-label')) !!}
                    <div class="col-xs-12">
                        {!! Form::text('last_name', old('last_name',$userData->last_name), array('class'=>'form-control')) !!}

                    </div>
                </div>
                
                <div class="form-group row">
                    {!! Form::label('email', 'Email', array('class'=>'col-xs-12 control-label')) !!}
                    <div class="col-xs-12">
                        {!! Form::text('email', old('email',$userData->email), array('class'=>'form-control', 'readonly' => 'readonly')) !!}

                    </div>
                </div>
                
                <?php $storeName = ''; ?>
                @if(isset($userData['subStoreDetails']->store_name))
                <?php $storeName = $userData['subStoreDetails']->store_name; ?>
                @endif
                <div class="form-group row">
                    {!! Form::label('store_name', 'Store Name', array('class'=>'col-xs-12 control-label')) !!}
                    <div class="col-xs-12">
                        {!! Form::text('store_name', old('store_name',$storeName), array('class'=>'form-control')) !!}
                    </div>
                </div>
                <div class="form-group row">
                    {!! Form::label('phone', 'Phone', array('class'=>'col-xs-12 control-label')) !!}
                    <div class="col-xs-12">
                        {!! Form::text('phone', old('phone',$userData->phone), array('class'=>'form-control')) !!}

                    </div>
                </div>
                <div class="form-group row">
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
        $("#form-store-profile").validate({
            focusInvalid: true,
            debug: true,
            rules: {
                store_name: 'required',
                phone: {
                    digits:true,
                    rangelength:[8,25],
                },
            },
            submitHandler: function (form) {
                form.submit();
            }

        });
    });
</script>
@stop