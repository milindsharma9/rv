$("#driver-apply-form").validate({
    focusInvalid: true,
    debug: true,
    rules: {
        email: {
            required: true,
            email: true
        },
        city: 'required',
        phone: 'required',
        name: 'required',
        last_name: 'required',
        nationality: 'required',
        address_line_1: 'required',
        region: 'required',
        country: 'required',
        pin: 'required',
        fk_occupation_id: 'required',
        delivery_area : 'required',
        availability: 'required',
    },
    submitHandler: function (form) {
        form.submit();
    }
});

/*======= Signup For Driver ======*/
$(document).on('change','.sign-up [name="vehicle"]',function(){
    if($(this).val()==='bicycle'){
        $('#selected_vehicle_type_span').text('bicycle');
        $('.vehical-type-scooter').hide();
        $('.vehical-type-bicycle').show();
    }
    else if($(this).val()==='scooter'){
        $('#selected_vehicle_type_span').text('scooter');
        $('.vehical-type-scooter').show();
        $('.vehical-type-bicycle').hide();
    }
});

$(document).ready(function(){
    if($('.sign-up [name="vehicle"]').val()==='bicycle'){
        $('#selected_vehicle_type_span').text('bicycle');
        $('.vehical-type-scooter').hide();
        $('.vehical-type-bicycle').show();
    }
    else if($('.sign-up [name="vehicle"]').val()==='scooter'){
        $('#selected_vehicle_type_span').text('scooter');
        $('.vehical-type-scooter').show();
        $('.vehical-type-bicycle').hide();
    }
});

/*==== Availability for signup ======*/
/*$(document).on('change','.table-availability input[type="checkbox"]',function(){
    console.log($(this).val().split('_').reverse()[0]);
    if($(this).val().split('_').reverse()[0]==4){
        if($(this).){

        }
    }
})*/