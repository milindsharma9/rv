@extends('store.layouts.products')
@section('header')
Edit Address
@endsection
@section('content')
<section class="store-content-section store-profile-edit-section store-address-edit-section">
    <div class="container">
        <div class="order-header hidden-xs">
            <h3 class="title"><a href="{{ route('store.profile') }}" class="btn-red">&lt; Back</a>Edit Details</h3>
        </div>
        <div class="row order-header visible-xs">
            <h3 class="title"><a href="{{ route('store.profile') }}" class="btn-red">&lt; Back</a></h3>
        </div>
        <div class="panel panel-default">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
            @endif
            <div class="panel-body">
                {!! Form::model($storeAddress, array('files' => true, 'class' => 'form-horizontal', 'id' => 'form-store-address-edit', 'method' => 'POST', 'route' => array('store.saveAddress'))) !!}
                
                <?php 
                    $address = $city = $state = $pin = ''; 
                ?>
                @if(isset($storeAddress->address))
                <?php 
                    $address    = $storeAddress->address; 
                    $city       = $storeAddress->city; 
                    $pin        = $storeAddress->pin; 
                    $state      = $storeAddress->state;
                    $storeId    = $storeAddress->fk_users_id;
                    
                ?>
                
                @endif
                <div class="col-xs-12">
                <div class="row">
                {!! Form::hidden('id', $storeId) !!}
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                    </ul>
                </div>
                @endif
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('address', 'Address', array('class'=>'control-label')) !!}
                        {!! Form::text('address', old('address',$address), array('class'=>'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('city', 'Town Name', array('class'=>'control-label')) !!}
                        {!! Form::text('city', old('city',$city), array('class'=>'form-control')) !!}
                    </div>
                </div>
                </div>
                <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('pin', 'Postcode', array('class'=>'control-label')) !!}
                        {!! Form::text('pin', old('pin',$pin), array('class'=>'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('state', 'Country', array('class'=>'control-label')) !!}
                        {!! Form::text('state', old('state',$state), array('class'=>'form-control')) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="stickyfooter">
                        {!! Form::submit('Update', array('class' => 'btn btn-submit-profile')) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('javascript')
<script src="{{ url('js') }}/jquery.validate.min.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
    $(function () {
        $("#form-store-address-edit").validate({
            focusInvalid: true,
            debug: true,
            rules: {
                email: {
                    required: true,
                    email: true
                },
                name: 'required',
                address: 'required',
                city: 'required',
                state: 'required',
                pin: 'required',
            },
            submitHandler: function (form) {
                form.submit();
            }

        });
    });
</script>
@endsection