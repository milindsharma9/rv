function initMap() {
    // Create the map.

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        scrollwheel: false,
        disableDoubleClickZoom: true,
        //zoomControl: false,
        center: { lat: 37.090, lng: -95.712 },
        mapTypeId: google.maps.MapTypeId.TERRAIN,
        styles: [{ "featureType": "all", "elementType": "geometry.fill", "stylers": [{ "weight": "2.00" }] }, { "featureType": "all", "elementType": "geometry.stroke", "stylers": [{ "color": "#9c9c9c" }] }, { "featureType": "all", "elementType": "labels.text", "stylers": [{ "visibility": "on" }] }, { "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [{ "color": "#626262" }] }, { "featureType": "landscape", "elementType": "all", "stylers": [{ "color": "#ffffff" }, { "lightness": "0" }] }, { "featureType": "landscape", "elementType": "geometry.fill", "stylers": [{ "color": "#f9f6f6" }] }, { "featureType": "poi", "elementType": "all", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.park", "elementType": "geometry.fill", "stylers": [{ "color": "#e0e0e0" }, { "visibility": "on" }] }, { "featureType": "road", "elementType": "all", "stylers": [{ "saturation": -100 }, { "lightness": 45 }] }, { "featureType": "road", "elementType": "geometry.fill", "stylers": [{ "color": "#efefef" }] }, { "featureType": "road", "elementType": "labels.text.fill", "stylers": [{ "color": "#7b7b7b" }] }, { "featureType": "road", "elementType": "labels.text.stroke", "stylers": [{ "color": "#ffffff" }] }, { "featureType": "road.highway", "elementType": "all", "stylers": [{ "visibility": "simplified" }] }, { "featureType": "road.arterial", "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] }, { "featureType": "transit", "elementType": "all", "stylers": [{ "visibility": "off" }] }, { "featureType": "water", "elementType": "all", "stylers": [{ "color": "#30bfb7" }, { "visibility": "on" }] }, { "featureType": "water", "elementType": "geometry.fill", "stylers": [{ "color": "#30bfb7" }] }, { "featureType": "water", "elementType": "labels.text.fill", "stylers": [{ "color": "#070707" }] }, { "featureType": "water", "elementType": "labels.text.stroke", "stylers": [{ "color": "#ffffff" }] }],
    });
    geocoder = new google.maps.Geocoder();

    //var address = '25 Saint John\'s Hill,London SW11 1TT';
    var address = map_address;
    var image1 = {
        url: map_marker_image
    };
    if (address) {
        geocoder.geocode({ 'address': address }, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                createInfo(results[0].geometry.location, address);
            } else {
                var allowedBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(85, -180),   // top left corner of map
                new google.maps.LatLng(-85, 180)    // bottom right corner
            );

            var k = 5.0; 
            var n = allowedBounds .getNorthEast().lat() - k;
            var e = allowedBounds .getNorthEast().lng() - k;
            var s = allowedBounds .getSouthWest().lat() + k;
            var w = allowedBounds .getSouthWest().lng() + k;
            var neNew = new google.maps.LatLng( n, e );
            var swNew = new google.maps.LatLng( s, w );
            boundsNew = new google.maps.LatLngBounds( swNew, neNew );
            map .fitBounds(boundsNew);
            }
        });
    }

    function createInfo(latlang, address) {
        //Create and open InfoWindow.
        var infoWindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
            map: map,
            icon: image1,
            title: address,
            position: latlang
        });

        //Attach click event to the marker.
        (function(marker, address) {
            google.maps.event.addListener(marker, "click", function(e) {
                //Wrap the content inside an HTML DIV in order to set height and width of InfoWindow.
                infoWindow.setContent("<div style = 'width:200px;min-height:40px'>" + address + "</div>");
                infoWindow.open(map, marker);
            });
        })(marker, address);
    }
}