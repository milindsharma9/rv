$(document).ready(function () {
    
    $('button[id^=remove_]').unbind("click").click(function(){
        var $itemId = $(this).attr('data-id');
        var $existQty = parseInt($('#dispatched_' + $itemId).text());
        if($existQty > 0){
            var $totalQty = $('.product-count').text();
            var $newQty = parseInt($totalQty.replace(' items', '')) - 1 ;
            $('#dispatched_' + $itemId).text(parseInt($existQty-1));
            //update session with price &qty
            $.ajax({
                url: updateSession,
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                data: {itemId: $itemId, newQty: $existQty-1, action: 'sub'},
                success: function (data) {
                    // update Total.
                    $('.total-price').text('£' + parseFloat(data).toFixed(2));
                },
                error: function (e) {
                    alert("cannot remove");
                    console.log('error', e);
                }
            });
            $('.product-count').text($newQty + ' items');
        }
    }); 
    
    $('button[id^=add_]').unbind("click").click(function(){
        var $itemId = $(this).attr('data-id');
        var $existQty = parseInt($('#dispatched_' + $itemId).text());
        var $limit = $(this).attr('data-limit');
        if($existQty < $limit){
            var $totalQty = $('.product-count').text();
            var $newQty = parseInt($totalQty.replace(' items', '')) + 1 ;
            $('#dispatched_' + $itemId).text(parseInt($existQty+1));
            //update session with price &qty
            $.ajax({
                url: updateSession,
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                data: {itemId: $itemId, newQty: $existQty+1, action: 'add'},
                success: function (data) {
                    // update Total.
                    $('.total-price').text('£' + parseFloat(data).toFixed(2));
                },
                error: function (e) {
                    alert("cannot remove");
                    console.log('error', e);
                }
            });
            $('.product-count').text($newQty + ' items');
        }
    }); 
    
    $('#confirmProduct').click(function(e){
        var $totalQty = $('.product-count').text();
        if($totalQty == '0 items'){
            alert("Please Select one item atleast")
            e.preventDefault();
        }else{
            $('#vendor-confirm').trigger('submit');
        }
    });
    
    if (orderItem.length == 0) {
        $('#error-popup').modal({backdrop: 'static', keyboard: false});
    }
});