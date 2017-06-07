<?php
    if (empty($mapAddress)) {
        $mapAddress = '332 Ladbroke Grove, London, W10 5AS';
        $deliveryPostcode = App\Http\Helper\CommonHelper::getUserCartDeliveryPostcode();
        if (!empty($deliveryPostcode)) {
            $mapAddress = $deliveryPostcode;
        }
    }
    //$mapAddress = urlencode($mapAddress);
?>
<!--<iframe width="640" height="480" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.it/maps?q=<?php echo $mapAddress; ?>&output=embed"></iframe>-->
<div id="map" class="map"></div>