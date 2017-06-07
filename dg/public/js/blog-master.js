$(function () {

var path = basePath.split('/');
path[ path.length-1 ] = 'myupload.php';
CKEDITOR.replace( 'description', {
  filebrowserUploadUrl: path.join('/').replace(/\/+$/, ''),
  extraPlugins : 'filebrowser',
});

    var startDateTextBox = $('#date_start_date');
    var endDateTextBox = $('#date_end_date');

    startDateTextBox.datetimepicker({
        timeFormat: 'HH:mm',
        dateFormat: "yy-mm-dd",
        minDate: 0,
        onClose: function (dateText, inst) {
            if (endDateTextBox.val() != '') {
                var testStartDate = startDateTextBox.datetimepicker('getDate');
                var testEndDate = endDateTextBox.datetimepicker('getDate');
                if (testStartDate > testEndDate)
                    endDateTextBox.datetimepicker('setDate', testStartDate);
            }
            else {
                endDateTextBox.val(dateText);
            }
        },
        onSelect: function (selectedDateTime) {
            endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate'));
        }
    });
    endDateTextBox.datetimepicker({
        timeFormat: 'HH:mm',
        dateFormat: "yy-mm-dd",
        minDate: 0,
        onClose: function (dateText, inst) {
            if (startDateTextBox.val() != '') {
                var testStartDate = startDateTextBox.datetimepicker('getDate');
                var testEndDate = endDateTextBox.datetimepicker('getDate');
                if (testStartDate > testEndDate)
                    startDateTextBox.datetimepicker('setDate', testEndDate);
            }
            else {
                startDateTextBox.val(dateText);
            }
        },
        onSelect: function (selectedDateTime) {
            startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate'));
        }
    });
    if (typeof (blogType) !== "undefined") {
        showFields(blogType);
        if (typeof (blogId) !== "undefined") {
            $.ajax({
                url: keywordeditURL,
                type: "POST",
                data: {'id': blogId},
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                dataType: 'json',
                success: function (data) {
                    getdataWithForToken(keywordURL, data, 'keywords');
                },
                error: function (e) {
                    console.log('error', e);
                    getdataWithForToken(keywordURL, '', 'keywords');
                }
            });
        }
    }
    else {
        $('form input[type="text"]')
                .not('#title, #url_path, #keywords, #meta_title, #meta_description, #meta_keywords')
                .each(function () {
                    $(this).closest('[class="form-group"]').hide();
                });
        blogType = $('#type :selected').val();
        $("#type")
                .change(function () {
                    blogType = $(this).val();
                    showFields(blogType);
                }).change();
                    getdataWithForToken(keywordURL, '', 'keywords');
    }
});
var showFields = function (blogType) {
    switch (blogType) {
        case typeBlog:
            $('#sub_title, #date_end_date').closest('div[class^="form-group"]').show();
            $('#address, #city, #state, #pin, #event_ticket_text,\n\
             #event_ticket_url, #places_drink_text, #places_drink_url, \n\
            #places_food_text, #places_food_url, #location,\n\
             #date_start_date').closest('div[class^="form-group"]').hide();
            break;
        case typeEvent:
            $('form label[for="location"]').html('Event Location*');
            $('#address, #city, #state, #pin, \n\
             #event_ticket_text, #event_ticket_url, #location, #date_end_date\n\
            ,#date_start_date').closest('div[class^="form-group"]').show();
            $('#sub_title, #places_drink_text, #places_drink_url, \n\
            #places_food_text, #places_food_url').closest('div[class^="form-group"]').hide();
            break;
        case typePlace:
            $('form label[for="location"]').html('Place Name*');
            $('#address, #city, #state, #pin, \n\
             #places_food_text, #places_food_url, #location, #places_drink_text,\n\
             #places_drink_url').closest('div[class^="form-group"]').show();
            $('#sub_title, #event_ticket_text, #event_ticket_url, #date_end_date, #date_start_date').closest('div[class^="form-group"]').hide();
            break;
        default:
            alert('Something went wrong please try again');
            break;
    }
};