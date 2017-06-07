$(document).ready(function () {
    var id = 0;
    $('#addProd').click(function () {
        bundleProduct();
    });
    var bundleProduct = function(){
        $.ajax({
            url: myUrl,
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
            success: function (data) {
                var selectedId = [];
                var arrLen = s = '';
                $('[class=delrow]').each(function () {
                    //console.log($(this));
                     if(onLoad){
                         if($(this).find("option:selected").val()){
                             dishId = parseInt($(this).find("option:selected").val());
                             selectedId.push(dishId);
                         }
                     } else {
                         if($(this).find("option:selected").val()){
                             selectedId.push(parseInt($(this).find("option:selected").val()));
                         }
                     }
                     selectedId.push(parseInt($(this).attr('id')));
                     arrLen = selectedId.length;                    
                    
                });
                //console.log(selectedId);
                $lastId = parseInt($('.product-select-id:last').attr('data-id'));
                if ($lastId != 'undefined')
                    id = $lastId;
                id = id + 1;
                var str = s = '';
                var s = '<tr class="productRow">\n\
                           <td class="product-select-id" data-id="' + id + '">\n\
                            <select id="productSelect' + id + '" \n\
                            name="productSelect[]" class="selectBox" required>\n\
                            <option value="">-select product-</option>';
                $.each(data.products, function (i, item) {
                    if (($.inArray(item.id, selectedId)) == -1) {
                        str += '<option value=' + item.id + '>' + item.name + '</option>';
                    }
                });
                var idVal = "delrow";
                s +=('</select></td><td>\n\
                                <input type="text" name="quantity[]" required="required"></td>\n\
                                <td class="delrow"><a class="link">' + imgUrl + '</a></td></tr>');
                if (str != '') {
                    $('[id=product]').append(s);
                    $('#productSelect' + id + '.selectBox').append(str);
                }
            },
            error: function (e) {
                console.log('error', e);
            }
        });
    };
    
    $(document).on('change', "[id^=productSelect]", function () {
        $(this).closest('td').siblings('.delrow')
                .attr('id', parseInt($(this).find(":selected").val()));
    });
        
    $(document).on('click', ".delrow a", function () {
        //Deleting the Row (tr) Element
        var $productId = $(this).parent().attr('id');
        var $bundleId = $('input[name="bundleId"]').val();
        if ($productId > 0 && $bundleId > 0) {
            $.ajax({
                url: deleteUrl,
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                data: {bundleId: $bundleId, productId: $productId},
                success: function (data) {
                },
                error: function (e) {
                    alert("cannot remove");
                    console.log('error', e);
                }
            });
        }
        $(this).parent().parent().remove();
    });
});