$(document).ready(function () {
    $('#customer-profile, #customer-address, #customer-password').hide();
    $("#form-customer-profile").validate({
        focusInvalid: true,
        debug: true,
        rules: {
            first_name: 'required',
            last_name: 'required',
            phone: {
                digits: true,
                rangelength: [7, 15],
                required: true,
            },
        },
        submitHandler: function (form) {
            $('#customer-profile').hide();
            $('#customer-profile').html('');
            var request;
            // bind to the submit event of our form
            // let's select and cache all the fields
            var $inputs = $(form).find("input, select, button, textarea");
            // serialize the data in the form
            var serializedData = $(form).serialize();
            // let's disable the inputs for the duration of the ajax request
            $inputs.prop("disabled", true);
            // fire off the request to /form.php
            request = $.ajax({
                url: saveProfileURL,
                type: "POST",
                data: serializedData,
            });
            // callback handler that will be called on success
            request.done(function (response, textStatus, jqXHR) {
                $('#customer-profile').show();
                if (response.status) {
                    $('#customer-profile').removeClass('alert-danger').addClass('alert-success').html('<button type="button" class="close" data-dismiss="alert">×</button><strong>' + response.message + '</strong>');
                    $('#phone_display_input').val(response.data.phone);
                } else {
                    $('#customer-profile').removeClass('alert-success').addClass('alert-danger').html('');
                    $.each(response.data, function (k, val) {
                        $('#customer-profile').append(k + ' => ' + val + '<br>');
                    });
                }
            });
            // callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // log the error to the console
                $('#customer-profile').removeClass('alert-success').addClass('alert-danger').html('Some Error occured. Please Try again');
                console.error(
                        "The following error occured: " + textStatus, errorThrown);
            });
            // callback handler that will be called regardless
            // if the request failed or succeeded
            request.always(function () {
                // reenable the inputs
                $inputs.prop("disabled", false);
            });
        }

    });

    $("#form-customer-delivery-address").validate({
        focusInvalid: true,
        debug: true,
        rules: {
            address: 'required',
            city: 'required',
            state: 'required',
            pin: 'required',
        },
        submitHandler: function (form) {
            $('#customer-address').hide();
            $('#customer-address').html('');
            var request;
            // bind to the submit event of our form
            // let's select and cache all the fields
            var $inputs = $(form).find("input, select, button, textarea");
            // serialize the data in the form
            var serializedData = $(form).serialize();
            // let's disable the inputs for the duration of the ajax request
            $inputs.prop("disabled", true);
            // fire off the request to /form.php
            request = $.ajax({
                url: saveAddressURL,
                type: "POST",
                data: serializedData,
            });
            // callback handler that will be called on success
            request.done(function (response, textStatus, jqXHR) {
                $('#customer-address').show();
                if (response.status) {
                    $('#customer-address').removeClass('alert-danger').addClass('alert-success').html('<button type="button" class="close" data-dismiss="alert">×</button><strong>' + response.message + '</strong>');
                } else {
                    $('#customer-address').removeClass('alert-success').addClass('alert-danger').html('');
                    $.each(response.data, function (k, val) {
                        $('#customer-address').append(k + ' => ' + val + '<br>');
                    });
                }
            });
            // callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // log the error to the console
                $('#customer-address').removeClass('alert-success').addClass('alert-danger').html('Some Error occured. Please Try again');
                console.error(
                        "The following error occured: " + textStatus, errorThrown);
            });
            // callback handler that will be called regardless
            // if the request failed or succeeded
            request.always(function () {
                // reenable the inputs
                $inputs.prop("disabled", false);
            });
        }

    });

    $("#form-customer-change-password").validate({
        focusInvalid: true,
        debug: true,
        rules: {
            password: 'required',
            password_confirmation: 'required',
        },
        submitHandler: function (form) {
            $('#customer-password').hide();
            $('#customer-password').html('');
            var request;
            // bind to the submit event of our form
            // let's select and cache all the fields
            var $inputs = $(form).find("input, select, button, textarea");
            // serialize the data in the form
            var serializedData = $(form).serialize();
            // let's disable the inputs for the duration of the ajax request
            $inputs.prop("disabled", true);
            // fire off the request to /form.php
            request = $.ajax({
                url: savePasswordURL,
                type: "POST",
                data: serializedData,
                dataType: 'json',
            });
            // callback handler that will be called on success
            request.done(function (response, textStatus, jqXHR) {
                $('#customer-password').show();
                if (response.status) {
                    $('#customer-password').removeClass('alert-danger').addClass('alert-success').html('<button type="button" class="close" data-dismiss="alert">×</button><strong>' + response.message + '</strong>');
                } else {
                    $('#customer-password').removeClass('alert-success').addClass('alert-danger').html('');
                    $.each(response.data, function (k, val) {
                        $('#customer-password').append(k + ' => ' + val + '<br>');
                    });
                }
            });
            // callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus, errorThrown) {
                // log the error to the console
                $('#customer-password').removeClass('alert-success').addClass('alert-danger').html('Some Error occured. Please Try again');
                console.error(
                        "The following error occured: " + textStatus, errorThrown);
            });
            // callback handler that will be called regardless
            // if the request failed or succeeded
            request.always(function () {
                // reenable the inputs
                $inputs.prop("disabled", false);
            });
        }

    });

    function log(message) {
        $("<div>").text(message).prependTo("#postcode_selected");
        $("#postcode_selected").scrollTop(0);
    }
    $(document).on('keydown.autocomplete', '#postcode_selected', function () {
        $(this).autocomplete({
            source: validPostCodeUrl,
            minLength: 1,
            select: function (event, ui) {
                log(ui.item);
            }
        });
    });
});