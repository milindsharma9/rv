$(function() {
    $(document).on('click', '.store-unavail-error a', function() { 
        $('#openingModal').modal({});
        $.ajax({
            url: checkStoreTimeUrl,
            method: 'GET',
            success: function(result) {
                $('#store_timing_ul').html(result.html_content);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
    })
})