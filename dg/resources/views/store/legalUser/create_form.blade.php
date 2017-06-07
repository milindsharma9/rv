@extends('store.layouts.products')
@section('header')
KYC Details
@endsection
@section('content')
<section class="store-content-section store-profile-edit-section store-address-edit-section">
    <div class="container">
        <div class="order-header hidden-xs">
            <h3 class="title"><a href="{{ route('store.dashboard') }}" class="btn-red">&lt; Back</a>KYC Details</h3>
        </div>
        <div class="row order-header visible-xs">
            <h3 class="title"><a href="{{ route('store.dashboard') }}" class="btn-red">&lt; Back</a></h3>
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
            <div class="row">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                    </ul>
                </div>
                @endif
                </div>
            <div class="panel-body">
                {!! Form::open(array('files' => true, 'id' => 'form-store-kyc-detail', 'method' => 'POST', 'route' => array('store.kyc.register.new'))) !!}
                <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('store_name', 'Store Name*', array()) !!}
                            {!! Form::text('store_name', old('store_name', $userStoreDetails['store_name']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_person_email', 'Store Email*', array()) !!}
                            {!! Form::text('legal_person_email', old('legal_person_email', Auth::user()['email']), array('class'=>'form-control', 'readonly' => 'readonly')) !!}
                        </div>
                    </div>
                </div>
                <hr>
                <!-- Headquarter Region Start -->

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('headquarters_address', 'REGISTERED BUSINESS Address', array()) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            @if(!isset($registeredAddress['premises']))
                            {{--*/ $registeredAddress['premises'] = '' /*--}}
                            @endif
                            {!! Form::label('headquarters_address_1', 'Address Line 1', array()) !!}
                            {!! Form::text('headquarters_address_1', old('headquarters_address_1', $registeredAddress['premises']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            @if(!isset($registeredAddress['address_line_1']))
                            {{--*/ $registeredAddress['address_line_1'] = '' /*--}}
                            @endif
                            {!! Form::label('headquarters_address_2', 'Address Line 2', array()) !!}
                            {!! Form::text('headquarters_address_2', old('headquarters_address_2', $registeredAddress['address_line_1']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            @if(!isset($registeredAddress['locality']))
                            {{--*/ $registeredAddress['locality'] = '' /*--}}
                            @endif
                            {!! Form::label('headquarters_city', 'City', array()) !!}
                            {!! Form::text('headquarters_city', old('headquarters_city', $registeredAddress['locality']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            @if(!isset($registeredAddress['region']))
                            {{--*/ $registeredAddress['region'] = '' /*--}}
                            @endif
                            {!! Form::label('headquarters_region', 'Region', array()) !!}
                            {!! Form::text('headquarters_region', old('headquarters_region', $registeredAddress['region']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                             @if(!isset($registeredAddress['postal_code']))
                            {{--*/ $registeredAddress['postal_code'] = '' /*--}}
                            @endif
                            {!! Form::label('headquarters_postcode', 'Postal Code', array()) !!}
                            {!! Form::text('headquarters_postcode', old('headquarters_postcode', $registeredAddress['postal_code']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('headquarters_country', 'Country', array()) !!}
                            {!! Form::select('headquarters_country', config('appConstants.country_select'), old('headquarters_country', 'GB'), array('class' => '')) !!}
                        </div>
                    </div>
                </div>

                <!-- Headquarter Region Ends -->
                <hr>
                <!-- Legal Person Region Start -->

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('legal_representative_address', 'Legal Representative Address', array()) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            @if(!isset($userStoreDetails['legal_fname']))
                            {{--*/ $userStoreDetails['legal_fname'] = '' /*--}}
                            @endif
                            {!! Form::label('legal_representative_fname', 'First Name*', array()) !!}
                            {!! Form::text('legal_representative_fname', old('legal_representative_fname', $userStoreDetails['legal_representative_fname']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            @if(!isset($userStoreDetails['legal_lname']))
                            {{--*/ $userStoreDetails['legal_lname'] = '' /*--}}
                            @endif
                            {!! Form::label('legal_representative_lname', 'Last Name*', array()) !!}
                            {!! Form::text('legal_representative_lname', old('legal_representative_lname', $userStoreDetails['legal_representative_lname']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {{--*/ $address1 = isset($legalAddress['address_line_1'])?$legalAddress['address_line_1']:''; /*--}}
                            @if(isset($legalAddress['premises']))
                            {{--*/ $address1 = $legalAddress['premises']; /*--}}
                            @endif
                            {!! Form::label('legal_representative_address_1', 'Address Line 1', array()) !!}
                            {!! Form::text('legal_representative_address_1', old('legal_representative_address_1', $address1), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                             {{--*/ $address2 = isset($legalAddress['address_line_2'])?$legalAddress['address_line_2']:''; /*--}}
                            @if(isset($legalAddress['premises']))
                            {{--*/ $address2 = $legalAddress['address_line_1']; /*--}}
                            @endif
                            {!! Form::label('legal_representative_address_2', 'Address Line 2', array()) !!}
                            {!! Form::text('legal_representative_address_2', old('legal_representative_address_2', $address2), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            @if(!isset($legalAddress['locality']))
                            {{--*/ $legalAddress['locality'] = '' /*--}}
                            @endif
                            {!! Form::label('legal_representative_city', 'City', array()) !!}
                            {!! Form::text('legal_representative_city', old('legal_representative_city',$legalAddress['locality']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            @if(!isset($legalAddress['region']))
                            {{--*/ $legalAddress['region'] = '' /*--}}
                            @endif
                            {!! Form::label('legal_representative_region', 'Region', array()) !!}
                            {!! Form::text('legal_representative_region', old('legal_representative_region',$legalAddress['region']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            @if(!isset($legalAddress['postal_code']))
                            {{--*/ $legalAddress['postal_code'] = '' /*--}}
                            @endif
                            {!! Form::label('legal_representative_postcode', 'Postal Code', array()) !!}
                            {!! Form::text('legal_representative_postcode', old('legal_representative_postcode',$legalAddress['postal_code']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            @if(!isset($legalAddress['country']))
                            {{--*/ $legalAddress['country'] = '' /*--}}
                            @endif
                            {!! Form::label('legal_representative_country', 'Country', array()) !!}
                            {!! Form::select('legal_representative_country', config('appConstants.country_select'), old('legal_representative_country', 'GB'), array('class' => '')) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        @if(!isset($userStoreDetails['legal_representative_dob_dd']))
                        {{--*/ $userStoreDetails['legal_representative_dob_dd'] = '' /*--}}
                        @endif
                         @if(!isset($userStoreDetails['legal_representative_dob_mm']))
                        {{--*/ $userStoreDetails['legal_representative_dob_mm'] = '' /*--}}
                        @endif
                         @if(!isset($userStoreDetails['legal_representative_dob_yy']))
                        {{--*/ $userStoreDetails['legal_representative_dob_yy'] = '' /*--}}
                        @endif
                        <div class="form-group">
                            {!! Form::label('legal_representative_dob', 'Birthday*', array()) !!}
                            <div class="row">
                                <div class="col-xs-4">
                                    {!! Form::select('legal_representative_dob_dd', config('dateConstant.days'), old('legal_representative_dob_dd'), array('class' => '')) !!}
                                </div>
                                <div class="col-xs-4" style="padding-left:0;">
                                    {!! Form::select('legal_representative_dob_mm', config('dateConstant.months'), old('legal_representative_dob_mm', $userStoreDetails['legal_representative_dob_mm']), array('class' => '')) !!}
                                </div>
                                <div class="col-xs-4" style="padding-left:0;">
                                    {!! Form::select('legal_representative_dob_yy', config('dateConstant.years'), old('legal_representative_dob_yy', $userStoreDetails['legal_representative_dob_yy']), array('class' => '')) !!}
                                </div>
                            </div>                          
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_country_residence', 'Country Of Residence*', array()) !!}
                            {!! Form::select('legal_representative_country_residence', config('appConstants.country_select'), old('legal_representative_country_residence', 'GB'), array('class' => '')) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_nationality', 'Nationality*', array()) !!}
                            {!! Form::select('legal_representative_nationality', config('appConstants.country_select'), old('legal_representative_nationality', 'GB'), array('class' => '')) !!}
                        </div>
                    </div>
                </div>
                <hr>
                <!-- Legal Person Region Ends -->

                <!-- Commenting Image Upload Fields -->
                {{--<div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('image_passport', 'PassPort', array('class'=>'control-label')) !!}
                        {!! Form::file('image_passport') !!}
                        {!! Form::hidden('image_passport_w', 4096) !!}
                        {!! Form::hidden('image_passport_h', 4096) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        {!! Form::label('image_registration', 'Registration Proof', array('class'=>'control-label')) !!}
                        {!! Form::file('image_registration') !!}
                        {!! Form::hidden('image_registration_w', 4096) !!}
                        {!! Form::hidden('image_registration_h', 4096) !!}
                    </div>
                </div>
                @if($userStoreDetails['business_type'] == $businessType['BUSINESS'])
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('image_association', 'Articles of association', array('class'=>'control-label')) !!}
                            {!! Form::file('image_association') !!}
                            {!! Form::hidden('image_association_w', 4096) !!}
                            {!! Form::hidden('image_association_h', 4096) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('image_shareholder', 'ShareHolder Declaration', array('class'=>'control-label')) !!}
                            {!! Form::file('image_shareholder') !!}
                            {!! Form::hidden('image_shareholder_w', 4096) !!}
                            {!! Form::hidden('image_shareholder_h', 4096) !!}
                        </div>
                    </div>
                @endif--}}
                {!! Form::hidden('business_type', $userStoreDetails['business_type']) !!}
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
<script src="{{ url('alchemy/js') }}/mangopay.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
<script>
    /*$(function () {
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
    });*/
</script>
@endsection