@extends('store.layouts.products')
@section('header')
KYC Status
@endsection
@section('content')
<section class="store-content-section store-kyc-status">
    <div class="container">
        <div class="order-header hidden-xs">
            <h3 class="title"><a href="{{ route('store.dashboard') }}" class="btn-red">&lt; Back</a>KYC Status</h3>
        </div>
        <div class="row order-header visible-xs">
            <h3 class="title"><a href="{{ route('store.dashboard') }}" class="btn-red">&lt; Back</a></h3>
        </div>
        <div class="row">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
            </div>
            @endif
        </div>
    	<div class="col-xs-12">
    		<table class="table-kyc">
    			<thead>
                    <tr>
                        <!--<th class="sno">S. No.</th>-->
                        <th class="doc-type">Document Type</th>
                        <th class="doc-img">Image</th>
                        <th class="doc-status">Status</th>
                    </tr>
    			</thead>
    			<tbody>
                            @if(empty($userKycDetails))
                            <tr><td colspan="3">
                                    <p>No Documents Found</p>
                                </td></tr>
                            @endif
                            <?php
                                $aUserUploadedDocument = array();
                            ?>
                            @foreach($userKycDetails as $kyc)
                                <tr>
                                    <td>{{$kyc['type']}}</td>
                                    <td>@if($kyc['image'] != '')<img src="{{ asset('uploads/kyc/thumb') . '/'.  $kyc['image'] }}">@endif</td>
                                    <td>
                                        <span id ="status_span_{{$kyc['document_id']}}">{{$kyc['status']}}</span>
                                        <i class="fa fa-refresh kyc-status-refresh" data-document-id ="{{$kyc['document_id']}}" aria-hidden="true"></i>
                                    </td>
    				</tr>
                                <?php
                                    $aUserUploadedDocument[] = $kyc['type'];
                                ?>
                            @endforeach
    			</tbody>
    		</table>
            <!--
                If some error happens at KYC. Give user option to upload new documents here.
            -->
            <?php
                $aALLKYCImages              =  config('mangopay.kyc_users_image');
                $userBusinessType           = $userStoreDetails['business_type'];
                $imageReqForUserBusiness    = $aALLKYCImages[$userBusinessType];
                if (count($imageReqForUserBusiness) > count($userKycDetails)) {
                    foreach ($imageReqForUserBusiness as $key => $value) {
                        $reqDocumentMangoLabel = $value['label_mangopay'];
                        $reqDocumentLabel = $value['label'];
                        if (!in_array($reqDocumentMangoLabel, $aUserUploadedDocument)) {
                            ?>
                            {!! Form::open(array('files' => true,  'id' => 'image-upload-kyc-document', 'method' => 'POST','route' => array('store.kyc.upload.document'))) !!}
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label>Upload {{$reqDocumentLabel}}</label>
                                    <input type="file" onchange="this.form.submit()" name="{{$key}}"> 
                                </div>
                            </div>
                            
                            {!! Form::hidden('business_type', $userBusinessType) !!}
                            {!! Form::hidden('document_type', $key) !!}
                            {!! Form::hidden($key.'_w', 8192) !!}
                            {!! Form::hidden($key.'_h', 8192) !!}
                            {!! Form::close() !!}
                            <?php
                        }
                    }
                }
            ?>
            
            <input type="hidden" name="_token" value="{!! csrf_token() !!}" />
    	</div>
        <!-- New Starts-->
        <div class="panel-body user-kyc-details">
                <div class="col-xs-12">
                    <hr>
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
                            {!! Form::text('legal_person_email', old('legal_person_email', $userStoreDetails['legal_person_email']), array('class'=>'form-control', 'readonly' => 'readonly')) !!}
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
                            {!! Form::label('headquarters_address_1', 'Address Line 1', array()) !!}
                            {!! Form::text('headquarters_address_1', old('headquarters_address_1', $userStoreDetails['headquarters_address_1']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('headquarters_address_2', 'Address Line 2', array()) !!}
                            {!! Form::text('headquarters_address_2', old('headquarters_address_2', $userStoreDetails['headquarters_address_2']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('headquarters_city', 'City', array()) !!}
                            {!! Form::text('headquarters_city', old('headquarters_city', $userStoreDetails['headquarters_city']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('headquarters_region', 'Region', array()) !!}
                            {!! Form::text('headquarters_region', old('headquarters_region', $userStoreDetails['headquarters_region']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('headquarters_postcode', 'Postal Code', array()) !!}
                            {!! Form::text('headquarters_postcode', old('headquarters_postcode', $userStoreDetails['headquarters_postcode']), array('class'=>'form-control')) !!}
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
                            {!! Form::label('legal_representative_fname', 'First Name*', array()) !!}
                            {!! Form::text('legal_representative_fname', old('legal_representative_fname', $userStoreDetails['legal_representative_fname']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_lname', 'Last Name*', array()) !!}
                            {!! Form::text('legal_representative_lname', old('legal_representative_lname', $userStoreDetails['legal_representative_lname']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_address_1', 'Address Line 1', array()) !!}
                            {!! Form::text('legal_representative_address_1', old('legal_representative_address_1', $userStoreDetails['legal_representative_address_1']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_address_2', 'Address Line 2', array()) !!}
                            {!! Form::text('legal_representative_address_2', old('legal_representative_address_2', $userStoreDetails['legal_representative_address_2']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_city', 'City', array()) !!}
                            {!! Form::text('legal_representative_city', old('legal_representative_city', $userStoreDetails['legal_representative_city']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_region', 'Region', array()) !!}
                            {!! Form::text('legal_representative_region', old('legal_representative_region', $userStoreDetails['legal_representative_region']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_postcode', 'Postal Code', array()) !!}
                            {!! Form::text('legal_representative_postcode', old('legal_representative_postcode', $userStoreDetails['legal_representative_postcode']), array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_country', 'Country', array()) !!}
                            {!! Form::select('legal_representative_country', config('appConstants.country_select'), old('legal_representative_country', 'GB'), array('class' => '')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_dob', 'Birthday*', array()) !!}
                            <div class="row">
                                <div class="col-xs-4">
                                    {!! Form::select('legal_representative_dob_dd', config('dateConstant.days'), old('legal_representative_dob_dd', $userStoreDetails['legal_representative_dob_dd']), array('class' => '')) !!}
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
                            {!! Form::select('legal_representative_country_residence', config('appConstants.country_select'), old('legal_representative_country_residence', $userStoreDetails['legal_representative_country_residence']), array('class' => '')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            {!! Form::label('legal_representative_nationality', 'Nationality*', array()) !!}
                            {!! Form::select('legal_representative_nationality', config('appConstants.country_select'), old('legal_representative_nationality', $userStoreDetails['legal_representative_nationality']), array('class' => '')) !!}
                        </div>
                    </div>
                </div>
                <hr>
                <!-- Legal Person Region Ends -->
            </div>
            </div>
        </div>
        
        <!-- End -->
    </div>
</section>
@endsection
@section('javascript')

<script type="text/javascript">
    var getDocumentStatus     = "{!! route('store.kyc.document.status.get')!!}";
    $('.panel-body.user-kyc-details input , .panel-body.user-kyc-details select').attr('disabled','disabled');
    $(document).on('click touchstart', '.kyc-status-refresh', function () {
        var documentId      = $(this).data("document-id");
        $('#status_span_' + documentId).html("Fetching New Status");
        $('#status_span_' + documentId).siblings('.kyc-status-refresh').addClass('loading');
        $.ajax({
            url: getDocumentStatus,
            method: 'POST',
            dataType: 'json',
            data: {
                docId:documentId,
                _token: $('input[name=_token]').val()
            },
            success: function(result) {
                if (result.status) {
                    var newStatus = "Latest Status:" + result.data.Status;
                    $('#status_span_' + documentId).html(newStatus);
                } else {
                    alert("Some error. Please try refreshing page.");
                }
                $('#status_span_' + documentId).siblings('.kyc-status-refresh').removeClass('loading');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
    });
</script>
@endsection