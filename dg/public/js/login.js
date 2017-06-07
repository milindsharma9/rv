$(document).ready(function () {

    $('#login-form input[type=submit]').click(function (e) {
        e.preventDefault();
        if ($('#login-form #login-email').hasClass('has-error')) {
            $('#login-form #login-email').removeClass('has-error');
        }

        if ($('#login-form #login-password').hasClass('has-error')) {
            $('#login-form #login-password').removeClass('has-error');
        }

        var form = jQuery(this).parents("form:first");
        var dataString = form.serialize();
        var formAction = form.attr('action');

        $.ajax({
            type: "POST",
            url: formAction,
            data: dataString,
            headers: {'X-CSRF-TOKEN': $('input[name="_token"]').value},
            success: function (data) {
                if (data.status == '1')
                    window.location.href = data.url;
                else if (data.status == '0') {
                    $('#login-password ~ span').html(data.message).css('color', '#a94442');
                }else{
                    alert("Some error. Please try refreshing page.");
                    window.location = window.location.href;
                }
            },
            error: function (data) {
                if(data.status == 500 || data.status == 301){
                    alert("Some error. Please try refreshing page.");
                    window.location = window.location.href;
                }
                if (typeof (data.responseText) != "undefined" && data.responseText !== null) {
                    var errors = $.parseJSON(data.responseText);
                    //console.log('errors' + errors);
                    if (errors.email) {
                        $('#login-form #login-email').addClass('has-error');
                        $('#login-email ~ span').html(errors.email).css('color', '#a94442');
                    }
                    if (errors.password) {
                        $('#login-form #login-password').addClass('has-error');
                        $('#login-password ~ span').html(errors.password).css('color', '#a94442');
                    }
                } else {
                    //console.log('failed');
                }
            }
        }, "json");
    });

    $('#register-form input[type=submit]').click(function (e) {
        e.preventDefault();
        if ($('#register-form #register-email').hasClass('has-error')) {
            $('#register-form #register-email').removeClass('has-error');
            $('#register-email ~ span').html('');
        }
        
        if ($('#register-form #register-fname').hasClass('has-error')) {
            $('#register-form #register-fname').removeClass('has-error');
            $('#register-fname ~ span').html('');
        }
        
        if ($('#register-form #register-lname').hasClass('has-error')) {
            $('#register-form #register-lname').removeClass('has-error');
            $('#register-lname ~ span').html('');
        }
        
        if ($('#register-form #register-phone').hasClass('has-error')) {
            $('#register-form #register-phone').removeClass('has-error');
            $('#register-phone ~ span').html('');
        }

        if ($('#register-form #register-password').hasClass('has-error')) {
            $('#register-form #register-password').removeClass('has-error');
            $('#register-password ~ span').html('');
        }

        if(!$('#term-condition').is(':checked')){
            $('#term-condition').siblings('.help-block').text('Please accept terms and conditions.');
            return false;
        }
        else {
            $('#term-condition').siblings('.help-block').text('');
        }

        var form = jQuery(this).parents("form:first");
        var dataString = form.serialize();
        var formAction = form.attr('action');
        $.ajax({
            type: "POST",
            url: formAction,
            data: dataString,
            headers: {'X-CSRF-TOKEN': $('input[name="_token"]').value},
            success: function (data) {
                if (data.status == '1') {
                    window.location.href = data.url;
                } else {
                    window.location.href = registerUrl;
                }
            },
            error: function (data) {
                if (typeof (data.responseText) != "undefined" && data.responseText !== null) {
                    var errors = $.parseJSON(data.responseText);
                    //console.log('errors' + errors);
                    if (errors.email) {
                        $('#register-form #register-email').addClass('has-error');
                        $('#register-email ~ span').html(errors.email).css('color', '#a94442');
                    }
                    if (errors.password) {
                        $('#register-form #register-password').addClass('has-error');
                        $('#register-password ~ span').html(errors.password).css('color', '#a94442');
                    }
                    if (errors.fname) {
                        $('#register-form #register-fname').addClass('has-error');
                        $('#register-fname ~ span').html(errors.fname).css('color', '#a94442');
                    }
                    if (errors.lname) {
                        $('#register-form #register-lname').addClass('has-error');
                        $('#register-lname ~ span').html(errors.lname).css('color', '#a94442');
                    }
                    if (errors.phone) {
                        $('#register-form #register-phone').addClass('has-error');
                        $('#register-phone ~ span').html(errors.phone).css('color', '#a94442');
                    }
                } else {
                    //console.log('failed');
                }
            }
        }, "json");
    });
});