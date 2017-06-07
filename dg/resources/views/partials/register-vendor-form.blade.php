@if(!Auth::user())
<form id="form-register-vendor" role="form" method="POST" action="{{ $formUrl }}">
    {!! csrf_field() !!}
    <input type="hidden" class="form-control" name="fk_users_role" value="{!! Config::get('appConstants.vendor_role_id')!!}">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>Registered business type*</label>
                <div class="{{ $errors->has('business_type') ? ' has-error' : '' }}">
                    <!--<input type="text" class="form-control" name="business_type" value="{{ old('business_type') }}">-->
                    {!! Form::select('business_type', $businessType, null, array()) !!}
                    @if ($errors->has('business_type'))
                    <span class="help-block">
                        <strong>{{ $errors->first('business_type') }}</strong>
                    </span>
                    @endif
                </div>
            </div>  
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group" name="cname-block">
                <label>Registered business name*</label>
                <div class="{{ $errors->has('cname') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="cname" value="{{ old('cname') }}" id="cname" required="required">

                    @if ($errors->has('cname'))
                    <span class="help-block">
                        <strong>{{ $errors->first('cname') }}</strong>
                    </span>
                    @endif
                </div>
            </div>  
        </div>
    </div>
    {!! Form::hidden('company_number', '') !!}
    {!! Form::hidden('company_address_temp', '') !!}
    {!! Form::hidden('headquarters_address_1', '') !!}
    {!! Form::hidden('headquarters_address_2', '') !!}
    {!! Form::hidden('headquarters_city', '') !!}
    {!! Form::hidden('headquarters_region', '') !!}
    {!! Form::hidden('headquarters_postcode', '') !!}
    {!! Form::hidden('headquarters_country', '') !!}
    {!! Form::hidden('headquarters_premises', '') !!}
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group" name="director-block" style="display: none;">
                <label>Registered business director*</label>
                <div class="{{ $errors->has('director') ? ' has-error' : '' }}">
                    <!--<input type="text" class="form-control" name="director" value="{{ old('director') }}">-->
                    {!! Form::select('director', [], null, array('id' => 'director')) !!}
                    @if ($errors->has('director'))
                    <span class="help-block">
                        <strong>{{ $errors->first('director') }}</strong>
                    </span>
                    @endif
                </div>
            </div>  
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                {!! Form::label('legal_representative_information', 'Legal Representative Information', array()) !!}
            </div>
        </div>
    </div>
    {!! Form::hidden('legal_representative_address_1', '') !!}
    {!! Form::hidden('legal_representative_address_2', '') !!}
    {!! Form::hidden('legal_representative_city', '') !!}
    {!! Form::hidden('legal_representative_region', '') !!}
    {!! Form::hidden('legal_representative_postcode', '') !!}
    {!! Form::hidden('legal_representative_country', '') !!}
    {!! Form::hidden('legal_premises', '') !!}
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>First Name*</label>
                <div class="{{ $errors->has('legal_representative_fname') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="legal_representative_fname" value="{{ old('legal_representative_fname') }}">

                    @if ($errors->has('legal_representative_fname'))
                    <span class="help-block">
                        <strong>{{ $errors->first('legal_representative_fname') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>Last Name*</label>
                <div class="{{ $errors->has('legal_representative_lname') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="legal_representative_lname" value="{{ old('legal_representative_lname') }}">
                    @if ($errors->has('legal_representative_lname'))
                    <span class="help-block">
                        <strong>{{ $errors->first('legal_representative_lname') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                {!! Form::label('legal_representative_dob', 'Birthday*', array('class' => '')) !!}
                <div class="row">
                    <div class="col-xs-4">
                        {!! Form::select('legal_representative_dob_dd', config('dateConstant.days'), old('legal_representative_dob_dd'), array('class' => '')) !!}
                    </div>
                    <div class="col-xs-4" style="padding-left:0;">
                        {!! Form::select('legal_representative_dob_mm', config('dateConstant.months'), old('legal_representative_dob_mm', ''), array('class' => '')) !!}
                    </div>
                    <div class="col-xs-4" style="padding-left:0;">
                        {!! Form::select('legal_representative_dob_yy', config('dateConstant.years'), old('legal_representative_dob_yy', ''), array('class' => '')) !!}
                    </div> 
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                {!! Form::label('legal_representative_country_residence', 'Country Of Residence*', array('class' => '')) !!}

                <div class="{{ $errors->has('legal_representative_country_residence') ? ' has-error' : '' }}">
                    {!! Form::select('legal_representative_country_residence', config('appConstants.country_select'), old('legal_representative_country_residence', 'GB'), array('class' => '')) !!}
                    @if ($errors->has('legal_representative_country_residence'))
                    <span class="help-block">
                        <strong>{{ $errors->first('legal_representative_country_residence') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                {!! Form::label('legal_representative_nationality', 'Nationality*', array('class' => '')) !!}

                <div class="{{ $errors->has('legal_representative_nationality') ? ' has-error' : '' }}">
                    {!! Form::select('legal_representative_nationality', config('appConstants.country_select'), old('legal_representative_nationality', 'GB'), array('class' => '')) !!}
                    @if ($errors->has('legal_representative_nationality'))
                    <span class="help-block">
                        <strong>{{ $errors->first('legal_representative_nationality') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>Store name*</label>

                <div class="{{ $errors->has('store_name') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="store_name" value="{{ old('store_name') }}">

                    @if ($errors->has('store_name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('store_name') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>Store e-mail*</label>
                <div class="{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>Store street address*</label>
                <div class="{{ $errors->has('address') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                    @if ($errors->has('address'))
                    <span class="help-block">
                        <strong>{{ $errors->first('address') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>Store city*</label>
                <div class="{{ $errors->has('city') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                    @if ($errors->has('city'))
                    <span class="help-block">
                        <strong>{{ $errors->first('city') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>Store Postcode*</label>
                <div class="{{ $errors->has('pin') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="pin" value="{{ old('pin') }}">
                    @if ($errors->has('pin'))
                    <span class="help-block">
                        <strong>{{ $errors->first('pin') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>STORE PREMISE LICENCE NUMBER</label>
                <div class="{{ $errors->has('pln') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="pln" value="{{ old('pln') }}">
                    @if ($errors->has('pln'))
                    <span class="help-block">
                        <strong>{{ $errors->first('pln') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>Designated Premise Supervisor (DPS)</label>
                <div class="{{ $errors->has('dps') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="dps" value="{{ old('dps') }}">
                    @if ($errors->has('dps'))
                    <span class="help-block">
                        <strong>{{ $errors->first('dps') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                <label>Personal Licence Number (of DPS)</label>
                <div class="{{ $errors->has('licence_number') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="licence_number" value="{{ old('licence_number') }}">
                    @if ($errors->has('licence_number'))
                    <span class="help-block">
                        <strong>{{ $errors->first('licence_number') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group">
                {!! captcha_image_html('RetailerCaptcha') !!}
            </div>
            <div class="form-group">
                <input type="text"id="CaptchaCode" name="CaptchaCode">
            </div>
        </div>
    </div>

    <!--<div class="form-group">
        <label class="col-md-12 control-label">Country</label>
        <div class="col-md-12 {{ $errors->has('state') ? ' has-error' : '' }}">
            <input type="text" class="form-control" name="state" value="United Kingdom">
            @if ($errors->has('state'))
            <span class="help-block">
                <strong>{{ $errors->first('state') }}</strong>
            </span>
            @endif
        </div>
    </div>-->
    
    <!-- New Fields Ends
    -->
    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-btn fa-user"></i>Register
        </button>
    </div>
</form>
@endif
@if(Auth::user())
You are currently Logged in. <br />
To register as store please logout and retype above URL.
@endif
<!-- Modal popup terms & conditions -->
<div id="myModalTerms" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Participation Agreement between Alchemy Wings and Sellers</h4>
            </div>
            <div class="modal-body">
                @include('store.partials.seller_agreement')
            </div>
            <div class="modal-footer">
                <label class="check-option">
                    <input type="checkbox" id="confirm_agr"> I confirm I have read the agreement and agree to be bound by these terms.
                </label>
                <label class="check-option">
                    <input type="checkbox" id="confirm_auth"> I confirm I am authorised to sign this agreement on the behalf of this business.
                </label>
                <div class="action-buttons btn-count-2">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="agree-btn" disabled="">Accept</button>
                </div>
            </div>
        </div>
    </div>
</div>