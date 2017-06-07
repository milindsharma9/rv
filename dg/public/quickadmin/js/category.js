function populateSubcategories() {
    var parentId = $( "#parent_id" ).val();
    var currentCatId = $( "#current_cat_id" ).val();
    $.ajax({
        url: "/admin/categories/getSubcategory/" + parentId + "/" + currentCatId,
        method: 'GET',
        dataType: 'json',
        success: function(result) {
            var content = prepareSelectData(result);
        $("#sub_cat_div").html(content);
    }});
       
}

function prepareSelectData(result) {
    var html = '<select name="sub_category_id" id="sub_category_id" class="form-control">';
    $.each(result, function(key, value) {
        html = html + '<option value='+key+'>'+value+'</option>';
    });
    html = html + '</select>';
    return html;
}


