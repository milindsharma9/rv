@section('content')
<section class="store-content-section store-payout-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="order-header">
                    <h3 class="title">
                        @if (Auth::user()->fk_users_role == config('appConstants.vendor_role_id'))
                            <a href="{{ url()->previous() }}" class="btn-red">&lt; Back</a>
                        @endif
                        Bank Details
                    </h3>
                </div>
                @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
                @endif
        @if (!$isKYCComplete)
            <div class="disable-product-upload-div">
                <h3>Please complete Retailer details.</h3>
                {!! link_to_route('store.kyc.register', trans('Complete') , "", array('class' => 'btn-red')) !!}
            </div>
        @else
                <form action="<?php echo $formRoute; ?>" method="post" id="mango-payout-form" accept-charset="UTF-8">
                <div class="form-item form-type-select form-item-banktype col-sm-12 col-xs-12 form-group">
                    <label for="edit-banktype">Bank Account Type </label>
                    {!! Form::select('banktype', $aBankDetailType, old('banktype'), array('class' => 'form-select', 'id' => 'edit-banktype')) !!}
                </div>
                    <div class="form-item form-type-textfield form-item-owner-name col-sm-6 col-xs-12 form-group">
                        <label for="NewBankAccountVM_OwnerName">Owner Name </label>
                        <input type="text" id="NewBankAccountVM_OwnerName" name="owner_name" value="{{old('owner_name')}}" size="60" maxlength="128" class="form-text" />
                    </div>
                    <div class="form-item form-type-textfield form-item-owner-add col-sm-6 col-xs-12 form-group">
                        <label for="edit-owner-add">Owner Address </label>
                        <input type="text" id="edit-owner-add" name="owner_add" value="{{old('owner_add')}}" size="60" maxlength="128" class="form-text" />
                    </div>
            <div class="form-item form-type-textfield form-item-owner-City col-sm-6 col-xs-12 form-group">
                <label for="edit-owner-city">Owner City </label>
                <input type="text" id="edit-owner-city" name="owner_City" value="{{old('owner_City')}}" size="60" maxlength="128" class="form-text" />
            </div>
            <div class="form-item form-type-select form-item-owner-Country col-sm-6 col-xs-12 form-group">
                <label for="edit-owner-country">OwnerCountry </label>
                {!! Form::select('owner_Country', config('appConstants.country_select'), old('owner_Country'), array('class' => 'form-select', 'id' => 'edit-owner-country')) !!}
                
            </div>
            <div class="form-item form-type-textfield form-item-owner-PostalCode col-sm-6 col-xs-12 form-group">
                <label for="edit-owner-postalcode">Owner Postal Code </label>
                <input type="text" id="edit-owner-postalcode" name="owner_PostalCode" value="{{old('owner_PostalCode')}}" size="60" maxlength="128" class="form-text" />
            </div>
            <div class="form-item form-type-textfield form-item-IBAN col-sm-6 col-xs-12 form-group">
                <label for="edit-iban">IBAN </label>
                <input type="text" id="edit-iban" name="IBAN" value="{{old('IBAN')}}" size="60" maxlength="128" class="form-text" />
            </div>
            <div class="form-item form-type-textfield form-item-bic col-sm-6 col-xs-12 form-group">
                <label for="edit-bic">BIC </label>
                <input type="text" id="edit-bic" name="bic" value="{{old('bic')}}" size="60" maxlength="128" class="form-text" />
            </div>
            <div class="form-item form-type-textfield form-item-AccountNumber col-sm-6 col-xs-12 form-group">
                <label for="edit-accountnumber">Account Number </label>
                <input type="text" id="edit-accountnumber" name="AccountNumber" value="{{old('AccountNumber')}}" size="60" maxlength="128" class="form-text" />
            </div>
            <div class="form-item form-type-textfield form-item-SortCode col-sm-6 col-xs-12 form-group">
                <label for="edit-sortcode">Sort Code </label>
                <input type="text" id="edit-sortcode" name="SortCode" value="{{old('SortCode')}}" size="60" maxlength="128" class="form-text" />
            </div>
            <div class="form-item form-type-textfield form-item-ABA col-sm-6 col-xs-12 form-group">
                <label for="edit-aba">ABA </label>
                <input type="text" id="edit-aba" name="ABA" value="{{old('ABA')}}" size="60" maxlength="128" class="form-text" />
            </div>
            <div class="form-item form-type-select form-item-DepositAccountType col-sm-6 col-xs-12 form-group">
                <label for="edit-depositaccounttype">Deposit Account Type </label>
                <select id="edit-depositaccounttype" name="DepositAccountType" class="form-select">
                    <option value="CHECKING">CHECKING</option>
                    <option value="SAVINGS">SAVINGS</option>
                </select>
            </div>
            <div class="form-item form-type-textfield form-item-BankName col-sm-6 col-xs-12 form-group">
                <label for="edit-bankname">Bank Name </label>
                <input type="text" id="edit-bankname" name="BankName" value="{{old('BankName')}}" size="60" maxlength="128" class="form-text" />
            </div>
            <div class="form-item form-type-textfield form-item-InstitutionNumber col-sm-6 col-xs-12 form-group">
                <label for="edit-institutionnumber">Institution Number </label>
                <input type="text" id="edit-institutionnumber" name="InstitutionNumber" value="{{old('InstitutionNumber')}}" size="60" maxlength="128" class="form-text" />
            </div>
            <div class="form-item form-type-textfield form-item-BranchCode col-sm-6 col-xs-12 form-group">
                <label for="edit-branchcode">Branch Code </label>
                <input type="text" id="edit-branchcode" name="BranchCode" value="{{old('BranchCode')}}" size="60" maxlength="128" class="form-text" />
            </div>
            <div class="col-sm-12 col-xs-12">
                        <input type="submit" id="edit-submit" name="op" value="Save" class="form-submit" />
                </div>
        </form>
        @endif
            </div>
        </div>
    </div>
</section>
@endsection
@section('javascript')
<script src="{{ url('alchemy/js') }}/mangopay.js?v={{ env('ASSETS_VERSION_NUMBER') }}"></script>
@endsection