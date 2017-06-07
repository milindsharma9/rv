// Transfer Balance to Bank

base_path = 'http://localhost/diageo/public/';

/* Value is number yes/not */
function isNumber(evt, element) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if ((charCode != 45 || $(element).val().indexOf('-') != -1) && // â€œ-â€ CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) && // â€œ.â€ CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57) && (charCode != 8))
        return false;

    return true;
}

$("#mango-payout-form #edit-accountnumber,#mango-payout-form #edit-aba,#mango-payout-form #edit-institutionnumber,#mango-payout-form #edit-branchcode,#mango-payout-form #edit-sortcode").keypress(function(event) {
    return isNumber(event, this)
});

var removeSymbol = function() {
    txtInput = this.value,
    lastChar = txtInput.charAt(txtInput.length-1)
    if (lastChar=='-'){
          this.value = txtInput.substr(0, txtInput.length-1);
    }
    return;
};

$(document).on('input','#edit-sortcode',removeSymbol);

banktype = $("#mango-payout-form #edit-banktype");
ownername = $("#mango-payout-form .form-item-owner-name");
address = $("#mango-payout-form .form-item-owner-add");
city = $("#mango-payout-form .form-item-owner-City");
country = $("#mango-payout-form .form-item-owner-Country");
postalcode = $("#mango-payout-form .form-item-owner-PostalCode");
bic = $("#mango-payout-form .form-item-bic");
accountnumber = $("#mango-payout-form .form-item-AccountNumber");
sortcode = $("#mango-payout-form .form-item-SortCode");
iban = $("#mango-payout-form .form-item-IBAN");
aba = $("#mango-payout-form .form-item-ABA");
depositeAccountType = $("#mango-payout-form .form-item-DepositAccountType");
bankName = $("#mango-payout-form .form-item-BankName");
institutionNumber = $("#mango-payout-form .form-item-InstitutionNumber");
branchCode = $("#mango-payout-form .form-item-BranchCode");

$(document).ready(function(){
    $('#mango-payout-form #edit-submit').before('<div class="error-msg"></div>');
    $('#mango-payout-form  > .form-group').hide();
    $('#mango-payout-form  > .form-group').removeClass('active-require');
    $(banktype).trigger('change');
});

$(banktype).on('change',function(){
    var selectedAccType = banktype.val().trim().toLowerCase();
    $('#mango-payout-form  > .form-group').hide();
    $('#mango-payout-form  > .form-group').removeClass('active-require');
    $('#mango-payout-form  > .form-group *').removeClass('error');
    $('#mango-payout-form .error-msg').text('');
    if(selectedAccType=='iban'){
        $(ownername).show().addClass('active-require');
        $(address).show().addClass('active-require');
        $(city).show().addClass('active-require');
        $(country).show().addClass('active-require');
        $(postalcode).show().addClass('active-require');
        $(iban).show().addClass('active-require');
        $(bic).show().addClass('active-require');
    }
    else if(selectedAccType=='gb'){
       $(ownername).show().addClass('active-require');
        $(address).show().addClass('active-require');
        $(city).show().addClass('active-require');
        $(country).show().addClass('active-require');
        $(postalcode).show().addClass('active-require');
        $(accountnumber).show().addClass('active-require');
        $(sortcode).show().addClass('active-require');
    }
    else if(selectedAccType=='us'){
        $(ownername).show().addClass('active-require');
        $(address).show().addClass('active-require');
        $(city).show().addClass('active-require');
        $(country).show().addClass('active-require');
        $(postalcode).show().addClass('active-require');
        $(accountnumber).show().addClass('active-require');
        $(aba).show().addClass('active-require');
        $(depositeAccountType).show().addClass('active-require');
    }
    else if(selectedAccType=='ca'){
        $(ownername).show().addClass('active-require');
        $(address).show().addClass('active-require');
        $(city).show().addClass('active-require');
        $(country).show().addClass('active-require');
        $(postalcode).show().addClass('active-require');
        $(bankName).show().addClass('active-require');
        $(accountnumber).show().addClass('active-require');
        $(branchCode).show().addClass('active-require');
        $(institutionNumber).show().addClass('active-require');
    }
    else if(selectedAccType=='other'){
        $(ownername).show().addClass('active-require');
        $(address).show().addClass('active-require');
        $(city).show().addClass('active-require');
        $(country).show().addClass('active-require');
        $(postalcode).show().addClass('active-require');
        $(country).show().addClass('active-require');
        $(accountnumber).show().addClass('active-require');
        $(bic).show().addClass('active-require');
    }
});

$('#mango-payout-form .form-submit').click(function() {
    $('#mango-payout-form input[type="text"], form[id^="mango-payout-form"] .selectboxit-container .selectboxit').removeClass('error');
    //$('#mango-payout-form  > .form-group').hide();
    error = 0;

    if (!banktype.val().trim() == '') {
        $('#mango-payout-form .active-require').find('input').each(function(){

            if ($(this).val().trim()=='') {
                $(this).addClass('error');
                $("#mango-payout-form .error-msg").text('Please fill/select all the required fileds');
                error++;
                return false;
            }

            if($(this).attr('id')=="edit-aba"){
                if($(this).val().length>9||$(this).val().length<9){
                    $(this).addClass('error');
                    $("#mango-payout-form .error-msg").text('ABA should be of 9 digit');
                    error++;
                    return false;
                }
            }
            if($(this).attr('id')=="edit-iban"){
                var str = /^[a-zA-Z]{2}\d{2}\s*(\w{4}\s*){2,7}\w{1,4}\s*$/;
                if(!$(this).val().match(str)){
                    $(this).addClass('error');
                    $("#mango-payout-form .error-msg").html('Invalid IBAN. IBAN should follow the below format<br><img src="'+base_path+'alchemy/images/iban-format.png"/>');
                    error++;
                    return false;
                }
            }
            if($(this).attr('id')=="edit-bic"){
                var str = /^[a-zA-Z]{6}\w{2}(\w{3})?$/;
                if(!$(this).val().match(str)){
                    $(this).addClass('error');
                    $("#mango-payout-form .error-msg").html('Invalid BIC. BIC should follow the below format<br><img src="'+base_path+'alchemy/images/bicFormat.png"/>');
                    error++;
                    return false;
                }
            }

            if($(this).attr('id')=="edit-branchcode"){
                if($(this).val().length>5||$(this).val().length<5){
                    $(this).addClass('error');
                    $("#mango-payout-form .error-msg").text('Branch Code should be of 5 digit');
                    error++;
                    return false;
                }
            }

            if($(this).attr('id')=="edit-owner-postalcode"){
                var str = /^[A-Za-z\d\s]+$/;
                if(!$(this).val().match(str)){
                    $(this).addClass('error');
                    $("#mango-payout-form .error-msg").text('Only Alphanumeric character and spaces are required.');
                    error++;
                    return false;
                }
            }

            if($(this).attr('id')=="edit-institutionnumber"){
                if($(this).val().length>4||$(this).val().length<3){
                    $(this).addClass('error');
                    $("#mango-payout-form .error-msg").text('Institution Number should be of 3 to 4 digit');
                    error++;
                    return false;
                }
            }

            if($(this).attr('id')=="edit-sortcode"){
                if($(this).val().length>6||$(this).val().length<6){
                    $(this).addClass('error');
                    $("#mango-payout-form .error-msg").text('Sort Code should be of 6 digit');
                    error++;
                    return false;
                }
                
            }

            if(banktype.val().trim().toLowerCase()=='ca'){
                if($(this).attr('id')=="edit-accountnumber"){
                    if($(this).val().length>20){
                        $(this).addClass('error');
                        $("#mango-payout-form .error-msg").text('Account number can only be 20 digit long');
                        error++;
                        return false;
                    }
                }
            }
        });
    }
    else {
        $("#mango-payout-form #edit-banktypeSelectBoxIt").addClass('error');
        $("#mango-payout-form .error-msg").text('Please select the Bank Account Type.');
        error = 1;
    }

    if (error!=0) {
        return false;
    }

    else if (error==0){
        return true;
    }
});


/*==== KYC Details =====*/

$('#legal_representative_dob').datepicker({
    maxDate: new Date(),
    changeYear: true,
    yearRange: "-100:+0"
});
