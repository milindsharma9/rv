$(function () {
     $('#title').bind('keypress keyup blur', function (event) {
        var press = jQuery.Event(event.type);
        var code = event.keyCode || event.which;
        press.which = code ;   
        // trigger 
        $('#url_path').val($(this).val()).trigger(event.type, {'event': press});
    });

    $('#url_path').on('keypress keyup blur', function (event) {
        $(this).val($(this).val().replace(/[^a-z0-9 \-\s]/gi, "")
                .replace(/[_\s]/g, "-").replace(/-+/g,'-').toLowerCase());
    });
    
});

var getdataWithForToken = function (dataURL, data, id, tokenLimit) {
    if (id == 'products') {
           var instances = $("#" + id).tokenize({
            datas: dataURL,
            sortable: true,
            searchParam: 'q',
            dataType: 'json',
            valueField: 'id',
            textField: 'description',
            onAddToken:function(value, text, e){
                if (isNaN(value)) {
                    $("#" + id).tokenize().tokenRemove(value);
                }
            },
        });
    } else {
        var token = null;
        if(tokenLimit != '' || tokenLimit  !== 'undefined'){
            token = tokenLimit;
        }
        $("#" + id).tokenInput(dataURL, {
            theme: "facebook",
            prePopulate: data,
            preventDuplicates: true,
            hintText: "Select " + id,
            noResultsText: "No results",
            searchingText: "Searching...",
            tokenLimit: token,
        });
    }
};