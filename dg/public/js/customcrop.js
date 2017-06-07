$(document).ready(function () {
    var jcrop_api;
    var i, ac;

    initJcrop();

    function initJcrop() {
        jcrop_api = $.Jcrop('#cropimage', {
            onSelect: storeCoords,
            onChange: storeCoords
        });
        jcrop_api.setOptions({aspectRatio: 1 / 1});
        jcrop_api.setOptions({
            minSize: [180, 180],
            maxSize: [180, 250]
        });
        jcrop_api.setSelect([140, 180, 160, 180]);
    }
    ;
    function storeCoords(c) {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
    }
    ;
});