$(function () {
    $('a[title="BotDetect CAPTCHA Library for Laravel"]').remove();
    if ($('select[name="business_type"]').val() == 'SOLETRADER') {
        //track intercom.io Select business type event.
        var metaData = {business_type: 'SOLETRADER'};
        trackIntercomEvent('select-business-type', metaData);
        $('div[name="cname-block"]').hide();
        $('div[name="director-block"]').hide();
    } else if ($('select[name="business_type"]').val() == 'BUSINESS') {
        //track intercom.io Select business type event.
        var metaData = {business_type: 'BUSINESS'};
        trackIntercomEvent('select-business-type', metaData);
        $('div[name="cname-block"]').show();
        if($('select[name="cname"]').length != 0)
            $('div[name="director-block"]').show();
    }
    $('select[name="director"]').selectpicker();
    $(document).on('keyup', '#cname', function () {
        //track intercom.io registered business name event.
        var metaData = {};
        trackIntercomEvent('typed-business-name', metaData);
        if ($(this).val().length >= 3) {
            $(this).addClass('ui-autocomplete-loading');
        }
        else {
            $(this).removeClass('ui-autocomplete-loading');
        }
        $('div[name="director-block"]').hide();
    });
    $("input[name='legal_representative_fname']").on('inputchange, blur',function(e){
        //track intercom.io registered business name event.
        var metaData = {};
        trackIntercomEvent('typed-legal-representative-first-name', metaData);
    });
    $("input[name='legal_representative_lname']").on('input, blur',function(e){
        //track intercom.io registered business name event.
        var metaData = {};
        trackIntercomEvent('typed-legal-representative-last-name', metaData);
    });
    $("select[name='legal_representative_dob_mm']").change(function (){
        //track intercom.io registered business name event.
        var metaData = {};
        trackIntercomEvent('legal-representative-dob-month', metaData);
    });
    $("select[name='legal_representative_dob_yy']").change(function (){
        //track intercom.io registered business name event.
        var metaData = {};
        trackIntercomEvent('legal-representative-dob-year', metaData);
    });
    $("select[name='legal_representative_dob_dd']").change(function (){
        //track intercom.io registered business name event.
        var metaData = {};
        trackIntercomEvent('legal-representative-dob-day', metaData);
    });
    $("input[name='store_name'], input[name='email'], input[name='address']\n\
    , input[name='city'], input[name='pin'], input[name='pln'], \n\
    input[name='dps'], input[name='licence_number'], input[name='CaptchaCode']")
            .on('input',function(){
        var attributeSelected = $(this).attr('name');
        if(attributeSelected == 'pin'){
            attributeSelected = 'postcode';
        } else if(attributeSelected == 'pln'){
            attributeSelected = 'premise-licence-number';
        }else if(attributeSelected == 'dps'){
            attributeSelected = 'designated-premise-supervisor';
        }else if(attributeSelected == 'licence_number'){
            attributeSelected = 'personal-licence-number';
        }
        var eventName = 'typed-'+ attributeSelected;
        var metaData = {};
        trackIntercomEvent(eventName, metaData);
    });
    $('button[type="submit"]').click(function(){
        //track intercom.io.
        var metaData = {};
        trackIntercomEvent('clicked-register', metaData);
    });
    $("#form-register-vendor").validate({
        focusInvalid: true,
        debug: true,
        rules: {
            email: {
                required: true,
                email: true
            },
            store_name: 'required',
            address: 'required',
            city: 'required',
            state: 'required',
            pin: 'required',
            legal_representative_fname: 'required',
            legal_representative_lname: 'required',
            legal_representative_country_residence: 'required',
            legal_representative_nationality: 'required',
            legal_representative_dob_dd: 'required',
            legal_representative_dob_mm: 'required',
            legal_representative_dob_yy: 'required',
            CaptchaCode: 'required',
        },
        submitHandler: function (form) {
            //form.submit();
            
            $("#myModalTerms").modal('show');
            $('#agree-btn').click(function () {
                form.submit();
            });
        }

    });


    var scrollingHeight = 0;
    var scroll = 0;
    var isScrolled = false;

    function checkPartAggr() {
        if ($('#confirm_agr').is(':checked') && $('#confirm_auth').is(':checked') && isScrolled == true) {
            //track intercom.io.
            var metaData = {};
            trackIntercomEvent('ticked-checkbox', metaData);
            return true;
        }
        else {
            return false;
        }
    }

    $('#confirm_agr,#confirm_auth').on('click', function () {
        var result = checkPartAggr();
        if (result === true) {
            $("#agree-btn").addClass('enabled-btn').removeAttr("disabled");
        }
        else {
            $("#agree-btn").removeClass('enabled-btn').attr("disabled", "");
        }
    });

    $('#myModalTerms .modal-body').scroll(function () {
        if ($(this).scrollTop() > ($(this).find('.cms_content').outerHeight() - 600)) {
            isScrolled = true;
            var result = checkPartAggr();
            if (result === true) {
                $("#agree-btn").addClass('enabled-btn').removeAttr("disabled");
            }
            else {
                $("#agree-btn").removeClass('enabled-btn').attr("disabled", "");
            }
        }
    });

    $('select[name="business_type"]').bind('change load', function () {
        //track intercom.io Select business type event.
        var metaData = {business_type: $(this).val()};
        trackIntercomEvent('select-business-type', metaData);
        if ($(this).val() == 'SOLETRADER') {
            $('div[name="cname-block"]').hide();
            $('div[name="director-block"]').hide();
        } else if ($(this).val() == 'BUSINESS') {
            $('div[name="cname-block"]').show();
        }
    });
    var itemName = 'cname';
    var _jScrollPane, _jScrollPaneAPI, _jSheight = 200;
    var settings = {
        showArrows: true
    };

    var month_name = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    $("#" + itemName).autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                url: companyDetailsUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    //data:JSON.stringify(productArr),
                    _token: $('input[name=_token]').val(),
                    store_name: request.term
                },
                success: function (result) {
                    if (result.status == true) {
                        var $data = result.data;
                        var companyList = [];
                        $.each($data, function (i, v) {
                            if (v.company_status == 'active') {
                                companyList.push({name: v.title,
                                    value: v.title,
                                    company_number: v.company_number,
                                    address: v.address_snippet,
                                    address_line_1: v.address.address_line_1,
                                    address_line_2: v.address.address_line_2,
                                    locality: v.address.locality,
                                    postal_code: v.address.postal_code,
                                    premises: v.address.premises,
                                    region: v.address.region,
                                    country: v.address.country, });
                            }
                        });
                        response(companyList);
                    } else {
                        alert(result.message);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("Some error. Please try refreshing page.");
                },
                complete: function () {
                    $("#" + itemName).removeClass('ui-autocomplete-loading');
                }
            });
        },
        focus: function (event, ui) {
            //$("#" + itemName).val(ui.item.name);
            return false;
        },
        /*close: function(event, ui) {
         _jScrollPaneAPI.destroy();
         _jScrollPane = undefined;
         },*/
        select: function (event, ui) {
            $("#" + itemName).val(ui.item.value);
            
            //track intercom.io Select business type event.
            var metaData = {business_name: ui.item.value};
            trackIntercomEvent('select-business-name', metaData);
            $('[name="company_number"]').val(ui.item.company_number);

            $("#" + itemName).addClass('ui-autocomplete-loading');
            $('[name="headquarters_address_1"]').val(ui.item.address_line_1);
            $('[name="headquarters_address_2"]').val(ui.item.address_line_2);
            $('[name="headquarters_region"]').val(ui.item.region);
            $('[name="headquarters_postcode"]').val(ui.item.postal_code);
            $('[name="headquarters_city"]').val(ui.item.locality);
            $('[name="headquarters_country"]').val(ui.item.country);
            $('[name="headquarters_premises"]').val(ui.item.premises);
            $.ajax({
                url: officerDetailsUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    //data:JSON.stringify(productArr),
                    _token: $('input[name=_token]').val(),
                    store_number: ui.item.company_number
                },
                success: function (result) {
                    if (result.status == true) {
                        var $data = result.data;
                        var companyList = [];
                        $('select[name="director"]').html('');
                        $.each($data, function (i, v) {
                            var optionVal = '|';
                            var showVal = officerNationality = '';
                            if (v.date_of_birth) {
                                optionVal = v.date_of_birth.year + '|' + v.date_of_birth.month;
                                showVal = month_name[v.date_of_birth.month] + ' ' + v.date_of_birth.year;
                            }
                            var officerAddress = addr1 = addr2 = region = pin = country = premises = locality = '';
                            if (v.address.premises) {
                                officerAddress += v.address.premises + ",";
                                premises = v.address.premises;
                            }
                            if (v.address.address_line_1) {
                                officerAddress += v.address.address_line_1 + ",";
                                addr1 = v.address.address_line_1;
                            }
                            if (v.address.address_line_2) {
                                addr2 = officerAddress += v.address.address_line_2 + ",";
                                addr2 = v.address.address_line_2;
                            }
                            if (v.address.region) {
                                region = officerAddress += v.address.region + ",";
                                region = v.address.region;
                            }
                            if (v.address.postal_code) {
                                pin = officerAddress += v.address.postal_code + ",";
                                pin = v.address.postal_code;
                            }
                            if (v.address.country) {
                                officerAddress += v.address.country + ",";
                                country = v.address.country;
                            }
                            if (v.nationality) {
                                officerNationality += v.nationality + ",";
                            }
                            if (v.address.locality) {
                                locality = v.address.locality;
                            }
                            officerAddress = officerAddress.slice(0, -1);
                            $('select[name="director"]').
                                    append('<option value="' + v.name + '\|' +
                                            optionVal + '" data-subtext="' + showVal +
                                            '" data-address="' + officerAddress +
                                            '" data-nationality="' + officerNationality
                                            + '" data-premises="' + premises + '" data-addr1="'
                                            + addr1 + '" data-addr2="' + addr2 +
                                            '" data-region="' + region + '" data-pin="'
                                            + pin + '" data-country="'
                                            + country + '" data-locality="'
                                            + locality + '">' + v.name + '</option>')
                        });

                    } else {
                        alert(result.message);
                    }
                    $('div[name="director-block"]').show();
                    $selectedOption = $('select[name="director"] option').filter(":selected");
                    updateTextValues($selectedOption);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("Some error. Please try refreshing page.");
                },
                complete: function () {
                    $("#" + itemName).removeClass('ui-autocomplete-loading');
                    $('select[name="director"]').selectpicker('refresh');
                }
            });


            return false;
        }
    })
            .autocomplete("instance")._renderItem = function (ul, item) {
        $(ul).addClass('custom-autocomplete-search');
        return $("<li>")
                .append("<div class='item-list'><div class='item-title'>" + item.name + "</div><div class='item-content'>Address - " + item.address + '<br>Company number - ' + item.company_number + "</div></div></div>")
                .appendTo(ul);
    };
    $(document).on('change', 'select[name="director"]', function () {
        $selectedOption = $('select[name="director"] option').filter(":selected");
        updateTextValues($selectedOption);
    });

    var updateTextValues = function (selectedOption) {
        $('[name="company_address_temp"]').val($(selectedOption).attr('data-address'));
        $nationality = $(selectedOption).attr('data-nationality');
        $string = $(selectedOption).val().split(',');
        $lname = (typeof $string[0] !== 'undefined') ? $string[0] : '';
        $string = (typeof $string[1] !== 'undefined') ? $string[1].split('|') : '';
        $fname = (typeof $string[0] !== 'undefined') ? $string[0] : '';
        $year = (typeof $string[1] !== 'undefined') ? $string[1] : '2016';
        $month = (typeof $string[2] !== 'undefined') ? $string[2] : '1';
        if ($month < 10)
            $month = '0' + $month;
        $('input[name="legal_representative_fname"]').val($fname);
        if($fname != ''){
            var metaData = {};
            trackIntercomEvent('typed-legal-representative-first-name', metaData);
        }
        $('input[name="legal_representative_lname"]').val($lname);
        if($lname != ''){
            var metaData = {};
            trackIntercomEvent('typed-legal-representative-last-name', metaData);
        }
        $('select[name="legal_representative_dob_mm"]').val($month);
        if($month != ''){
            var metaData = {};
            trackIntercomEvent('legal-representative-dob-month', metaData);
        }
        $('select[name="legal_representative_dob_yy"]').val($year);
        if($year != ''){
            var metaData = {};
            trackIntercomEvent('legal-representative-dob-year', metaData);
        }
        $('[name="legal_representative_address_1"]').val($(selectedOption).attr('data-addr1'));
        $('[name="legal_representative_address_2"]').val($(selectedOption).attr('data-addr2'));
        $('[name="legal_representative_region"]').val($(selectedOption).attr('data-region'));
        $('[name="legal_representative_postcode"]').val($(selectedOption).attr('data-pin'));
        $('[name="legal_representative_country"]').val($(selectedOption).attr('data-country'));
        $('[name="legal_representative_city"]').val($(selectedOption).attr('data-locality'));
        $('[name="legal_representative_region"]').val($(selectedOption).attr('data-region'));
        $('[name="legal_premises"]').val($(selectedOption).attr('data-premises'));
    };

    var cms_content = '';

    $(document).on('show.bs.modal', '#myModalTerms', function () {
        cms_content = $(".cms_content").html();
        if ($('[name="business_type"]').val() === "BUSINESS") {
            var replaced = $(".cms_content").html().replace('@COMPANY_NAME@', $('#cname').val());
            replaced = replaced.replace('@COMPANY_NUMBER@', $('[name="company_number"]').val());
            replaced = replaced.replace('@COMPANY_ADDRESS@', $('[name="company_address_temp"]').val());
            replaced = replaced.replace('@STORE_NAME@', '');
            replaced = replaced.replace('@STORE_ADDRESS@', '');
            $(".cms_content").html(replaced);
        }
        if ($('[name="business_type"]').val() === "SOLETRADER") {
            var store_address = "";
            var replaced = $(".cms_content").html().replace('@COMPANY_NAME@', '');
            replaced = replaced.replace('@COMPANY_NUMBER@', '');
            replaced = replaced.replace('@COMPANY_ADDRESS@', '');
            replaced = replaced.replace('@STORE_NAME@', $('[name="store_name"]').val());
            store_address = $('[name="address"]').val() + ',' + $('[name="city"]').val() + ',' + $('[name="pin"]').val();
            replaced = replaced.replace('@STORE_ADDRESS@', store_address);
            $(".cms_content").html(replaced);
        }
    });

    $(document).on('hide.bs.modal', '#myModalTerms', function () {
        $(".cms_content").html(cms_content);
    });

});