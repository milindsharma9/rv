$(function () {
    getdataWithForToken(productURL, '', 'products');
    if (typeof (localeId) !== "undefined") {
        $.ajax({
            url: bundleSavedURL,
            type: "POST",
            data: {'id': localeId, 'type': 'locale'},
            headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
            dataType: 'json',
            success: function (data) {
                getdataWithForToken(bundleURL, data, 'bundles');
            },
            error: function (e) {
                console.log('error', e);
                getdataWithForToken(bundleURL, '', 'bundles');
            }
        });
    } else {
        getdataWithForToken(bundleURL, '', 'bundles');
    }
    if (typeof (localeId) !== "undefined") {
        $.ajax({
            url: recipeSavedURL,
            type: "POST",
            data: {'id': localeId, 'type': 'locale'},
            headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
            dataType: 'json',
            success: function (data) {
                getdataWithForToken(recipeURL, data, 'recipies');
            },
            error: function (e) {
                console.log('error', e);
                getdataWithForToken(recipeURL, '', 'recipies');
            }
        });
    } else {
        getdataWithForToken(recipeURL, '', 'recipies');
    }
    if (typeof (localeId) !== "undefined") {
            $.ajax({
                url: keywordeditURL,
                type: "POST",
                data: {'id': localeId, 'type': 'locale'},
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                dataType: 'json',
                success: function (data) {
                    getdataWithForToken(keywordURL, data, 'keywords', 1);
                },
                error: function (e) {
                    console.log('error', e);
                    getdataWithForToken(keywordURL, '', 'keywords', 1);
                }
            });
        } else {
            getdataWithForToken(keywordURL, '', 'keywords', 1);
        }
      
    if (typeof (deleteImageURL) !== "undefined") {
        $('div[id^="image-delete-"]').click(function () {
            var imageName = $(this).attr('data-imageName');
            var brandId = $(this).attr('data-brandId');
            var name = $(this).attr('data-name');
            var elem = $(this);
            $.ajax({
                url: deleteImageURL,
                type: "POST",
                data: {'imageName': imageName, brandId: brandId, name:name},
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
                dataType: 'json',
                success: function (data) {
                    if(data.status == true){
                        $(elem).parent('div').remove();
                    }else{
                        alert('some error occured');
                    }
                    //console.log(data);
                },
                error: function (e) {
                    console.log('error', e);
                }
            });
        });
    }
    ;
});