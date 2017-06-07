


$(document).ready(function () {
    
    /*
     * Login From cart Delivery Postcode Popup
     */
    $(document).on('click','.login-link a',function(e){
        e.stopPropagation();
        $('#login-register').modal({});
        $('#zip-code-popup').modal('hide');
    });

    /*
     * Hide Delivery Postcode Popup when clicked on Login.
     */
    $(document).on('click','.edit-zip-code',function() {
       $(this).closest('.logged-in-zip').siblings('.logged-in-no-zip').show();
       $(this).closest('.logged-in-zip').hide();
    });

    var clickGeneratedProduct = '';
    
    $(document).on('click touchstart', '.product-add-cart,a.product-price:not(.bundle-price)', function () {
        $(this).addClass('loading');
        var currElement = $(this);
        var cartContent = prepareCartPopUpData($(this));
        $.ajax({
            url: checkCustomerCartStatusUrl,
            method: 'GET',
            success: function(result) {
                if (result.validated) {
                        // Commented Modal Popup but functionality kept same.
                        // i.e it will add deafult quantity of product.
                        //$('#cart-modal').modal();
                        $('#cart_ul').html(cartContent);
                        clickGeneratedProduct = $(currElement).data('id');
                        $('.cart_popup_confirm').trigger('click');
                } else {
                    if (result.is_close) {
                        $('#openingModal').modal({});
                        $('#store_timing_ul').html(result.html_content);
                    } else {
                        if(!$('#zip-code-popup').length>0){
                            $('body').append(result.html_content);
                        }
                        $('body').find($('#zip-code-popup')).modal({});
                        $('#zip-code-popup').find('.logged-in-zip').show();
                        if($('#zip-code-popup').find('.first-time-zip').length > 0){
                            $('#zip-code-popup').find('.logged-in-no-zip').show();
                        }
                        else{
                            $('#zip-code-popup').find('.logged-in-no-zip').hide();
                        }
                    }
                }
                currElement.removeClass('loading');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
                currElement.removeClass('loading');
            }
        });
    });
    
    // Cart Minus
    $(document).on('click', 'button[class=prod-remove]', function () {
        var prodId      = $(this).data("id");
        var prodPrice   = $('#prod_price_'+prodId).text();
        var quantity    = $('#product_quantity_'+prodId).val();
        var newQuantity = 0;
        if (quantity <= 1) {
            return false;
        } else {
            newQuantity = parseInt(quantity) - 1;
        }
        var productTotal = prodPrice * newQuantity;
        $('#product_quantity_'+prodId).val(newQuantity);
        productTotal = parseFloat(productTotal).toFixed(2)
        $('#product_total_span_'+prodId).text(productTotal);
    });
    
    // Cart Add
    $(document).on('click', 'button[class=prod-add]', function () {
        var prodId      = $(this).data("id");
        var prodPrice   = $('#prod_price_'+prodId).text();
        var quantity    = $('#product_quantity_'+prodId).val();
        var newQuantity = 0;
        if (quantity < 1) {
            return false;
        } else {
            newQuantity = parseInt(quantity) + 1;
        }
        var productTotal = prodPrice * newQuantity;
        $('#product_quantity_'+prodId).val(newQuantity);
        productTotal = parseFloat(productTotal).toFixed(2)
        $('#product_total_span_'+prodId).text(productTotal);
    });
    
    $(document).on('click', '.cart_popup_confirm', function () {
        var prodIds      = $(this).data("ids");
        var aProdId      = [prodIds];
        $(this).addClass('loading');
        sendCartData(aProdId, 0);
    });
    
    function prepareCartPopUpData(attr) {
        var productId                  = attr.data("id");
        var productName                = attr.data("name");
        var productPrice               = attr.data("price");
        var productAlcoholContent      = attr.data("alcohol");
        var html = "<ul class='cart-products'>";
        html = html + createCartList(productId, productName, productAlcoholContent,
                            productPrice, "1", productPrice,
                            false);
        html = html + "</ul>";
        html = html + "<button data-ids='"+productId+"' class='cart_popup_confirm'>Confirm</button>";
        return html;
    }

    // Bundle Cart
    $(document).on('click touchstart', '.product-add-cart-bundle,a.bundle-price', function () {
        $(this).addClass('loading');
        var currElement = $(this);
        var bundleId        = $(this).data("id");
        var bundleName      = $(this).data("name");
        $.ajax({
            url: getBundleDetailUrl + "/" + bundleId,
            method: 'GET',
            success: function(result) {
                if (result.validated) {       
                    // Commented Modal Popup but functionality kept same.
                    // i.e it will add deafult quantity of product.
                    //$('#cart-modal').modal();
                    var cartContent = prepareCartPopUpBundleData(result, bundleName);
                    clickGeneratedProduct = $(currElement).data('id');
                    $('#cart_ul').html(cartContent);
                    $('.cart_popup_bundle_confirm').trigger('click');
                } else {
                    if (result.is_close) {
                        $('#openingModal').modal({});
                        $('#store_timing_ul').html(result.html_content);
                    } else {
                        if(!$('#zip-code-popup').length>0){
                            $('body').append(result.html_content);
                        }
                        $('body').find($('#zip-code-popup')).modal({});
                        $('#zip-code-popup').find('.logged-in-zip').show();
                        if($('#zip-code-popup').find('.first-time-zip').length > 0){
                            $('#zip-code-popup').find('.logged-in-no-zip').show();
                        }
                        else{
                            $('#zip-code-popup').find('.logged-in-no-zip').hide();
                        }
                    }
                }
                currElement.removeClass('loading');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
                currElement.removeClass('loading');
            }
        });
    });

    function prepareCartPopUpBundleData(result, bundleName) {
        var html = "<ul class='cart-products'>";
        html = html + "<li class='bundle-title'>";

	html = html + "<div class='product-name'>"+bundleName+"</div>";
	html = html + "<span class='product-modify'>";
		html = html + "<button class='prod-remove bundle-remove'></button>";
		html = html + "<input type='text' value='1' id='bundle_quantity' class='product-quan' disabled='disabled'>";
		html = html + "<button class='prod-add bundle-add'></button>";
	html = html + "</span>";
        html = html + "</li>";
        var bundleTotal = "0.00";
        var prodIds = "";
        var bundleId;
        var defaultLength = 25;
        $.each(result.bundleDetails, function (index, value) {
            var prodDescDefault = value.productsDescription;
            var descLen = prodDescDefault.length;
            var prodDesc = "";
            if (descLen > defaultLength) {
                prodDesc = prodDescDefault.slice(0,25);
                prodDesc = prodDesc + "...";
            } else {
                prodDesc = prodDescDefault;
            }
            bundleId = value.fk_bundle_id;
            var productId = value.fk_product_id;
            var productName = value.productsName;
            var productAlcoholContent = prodDesc;
            var productPrice = value.price;
            var productQuantity = value.product_quantity;
            var productPriceTotal = value.priceTot;
            bundleTotal = parseFloat(bundleTotal) + parseFloat(productPriceTotal);
            bundleTotal = bundleTotal.toFixed(2);
            prodIds += productId+"|";
            html = html + createCartList(productId, productName, productAlcoholContent,
                        productPrice, productQuantity, productPriceTotal,
                        true);
        });
        html = html + "</ul><div class='grand-total'>Total <span>£<span id='cart_bundle_grand_total'>"+bundleTotal+"</span></span></div>";
        prodIds = prodIds.slice(0, -1);
        html = html + "<button data-ids='"+prodIds+"' id='cart_popup_bundle_confirm' data-bundle-id='"+bundleId+"' class='cart_popup_bundle_confirm'>Confirm</button>";
        return html;
    }

    $(document).on('click', '.cart_popup_bundle_confirm', function () {
        var prodIds         = $(this).data("ids");
        var bundleId        = $(this).data("bundle-id");
        prodIds             = prodIds.toString();
        if (prodIds.indexOf("|") >= 0) {
            var aProdId         = prodIds.split("|");
        } else {
            var aProdId         = [prodIds];
        }
        $(this).addClass('loading');
        sendCartData(aProdId, bundleId);
        
    });

    function sendCartData(aProdId, isBundle) {
        var productArr = [];
        var isBundleFlag = isBundle;
        //console.log(isBundleFlag);
        for (i = 0; i < aProdId.length; ++i) {
            var productId                  = aProdId[i];
            var productName                = $('#prod_name_'+productId).text();
            var productPrice               = $('#prod_price_'+productId).text();
            var productAlcoholContent      = $('#prod_alcohol_content_'+productId).text();
            var productQuantity            = $('#product_quantity_'+productId).val();
            //var attributeArray = {"alcohol_content": productAlcoholContent, "isBundle": isBundle};
            var attributeArray = {"alcohol_content": productName, "bundleId": isBundle};
            var prod = {id: productId, name: productAlcoholContent, price: productPrice, qty: productQuantity, options: attributeArray};
            productArr.push(prod);
        }
        $.ajax({
            url: cartAddUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                data:JSON.stringify(productArr),
                _token: $('input[name=_token]').val()
            },
            success: function(result) {
                if(result.status) {
                    $('.prod-count').each(function(){
                       $(this).text(result.quantity); 
                    });
                    if(isBundleFlag != 0){
                        //console.log(clickGeneratedProduct);
                        $('[data-item-id-bundle]').each(function(){
                            if($(this).data('item-id-bundle')==clickGeneratedProduct){
                                var count = parseInt($(this).html());
                                //console.log(count);
                                if(count==''||count==undefined||isNaN(count)){
                                    count=0;
                                }
                                $(this).html(count+1);
                            }
                        });
                    }
                    else {
                        //console.log(clickGeneratedProduct);
                        $('[data-item-id]').each(function(){
                            if($(this).data('item-id')==clickGeneratedProduct){
                                var count = parseInt($(this).html());
                                //console.log(count);
                                if(count==''||count==undefined||isNaN(count)){
                                    count=0;
                                }
                                $(this).html(count+1);
                            }
                        });
                    }
                    //track intercom.io add cart event.
                    var metaData = {productId: productId, productName: productName, productPrice:productPrice };
                    trackIntercomEvent('cart-add', metaData);
                } else {
                    alert('Error while adding Product|' + result.message);
                }
                $('#cart-modal').modal('hide');
                $('.cart_popup_confirm').removeClass('loading');
                $('.cart_popup_bundle_confirm').removeClass('loading');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
                $('.cart_popup_confirm').removeClass('loading');
                $('.cart_popup_bundle_confirm').removeClass('loading');
            }
        });
    }

    function createCartList(productId, productName, productAlcoholContent,
                        productPrice, productQuantity, productPriceTotal,
                        isBundle) {
        var html = "";
        html = html + "<li>";
            html = html + "<div class='product-name' id='prod_name_"+productId+"'>"+productAlcoholContent+"</div>";
            html = html + "<div class='product-desc' id='prod_alcohol_content_"+productId+"'>"+productName+"</div>";
            html = html + "<div class='product-price'>£<span id='prod_price_"+productId+"'>"+productPrice+"</span></div>";
            html = html + "<div class='product-quantity'>";
                html = html + "<span class='product-modify'>";
                if (!isBundle) {
                    html = html + "<button class='prod-remove' data-id='"+productId+"'></button>";
                }
                html = html + "<input type='text' disabled='disabled' id='product_quantity_"+productId+"' class='product-quan' value='"+productQuantity+"' />";
                if (!isBundle) {
                    html = html + "<button class='prod-add' data-id='"+productId+"'></button>";
                } else {
                    html = html + "<input type='hidden' value='"+productPrice+"' id='bundle_prod_default_price_"+productId+"'>";
                    html = html + "<input type='hidden' value='"+productQuantity+"' id='bundle_prod_default_quantity_"+productId+"'>";
                }
                html = html + "</span>";
                html = html + "<span class='single-total'>Total <span class='price'>£<span id='product_total_span_"+productId+"'>"+productPriceTotal+"</span></span></span>";
            html = html + "</div>";
            html = html + "</li>";
        return html;
    }

    // Cart Bundle Add
    $(document).on('click', '.bundle-add', function () {
        var prodIds         = $('#cart_popup_bundle_confirm').data("ids");
        var aProdId = prodIds.split("|");
        var bundleQuantity    = $('#bundle_quantity').val();
        var newBundleQuantity = 0;
        if (bundleQuantity < 1) {
            return false;
        } else {
            newBundleQuantity = parseInt(bundleQuantity) + 1;
        }
        handleBundleAddRemove(aProdId, newBundleQuantity);
    });

    // Cart Bundle Rmove
    $(document).on('click', '.bundle-remove', function () {
        var prodIds         = $('#cart_popup_bundle_confirm').data("ids");
        var aProdId = prodIds.split("|");
        var bundleQuantity    = $('#bundle_quantity').val();
        var newBundleQuantity = 0;
        if (bundleQuantity <= 1) {
            return false;
        } else {
            newBundleQuantity = parseInt(bundleQuantity) - 1;
        }
        handleBundleAddRemove(aProdId, newBundleQuantity);
    });

    function handleBundleAddRemove(aProdId, newBundleQuantity) {
        var bundleTotalPrice = 0.00;
        for (i = 0; i < aProdId.length; ++i) {
            var productId                 = aProdId[i];
            var defaultProductQuantity    = $('#bundle_prod_default_quantity_'+productId).val();
            var defaultProductPrice       = $('#bundle_prod_default_price_'+productId).val();
            var newProductTotal = newBundleQuantity * defaultProductQuantity;
            var newProductTotalPrice = newProductTotal * defaultProductPrice;
            newProductTotalPrice = newProductTotalPrice.toFixed(2);
            $('#product_total_span_'+productId).text(newProductTotalPrice);
            $('#product_quantity_'+productId).val(newProductTotal);
            bundleTotalPrice = parseFloat(bundleTotalPrice) + parseFloat(newProductTotalPrice);
            bundleTotalPrice = bundleTotalPrice.toFixed(2);
        }
        $('#bundle_quantity').val(newBundleQuantity);
        $('#cart_bundle_grand_total').text(bundleTotalPrice);
    }
    
    /*==== Cart page ======*/

    // Product Qunatity Decrease
    $(document).on('click', '.product-cart-add', function () {
        var rowId      = $(this).data("id");
        var prodPrice   = $('#productDefaultPrice_'+rowId).text();
        var quantity    = $('#productQuantity_'+rowId).val();
        var newQuantity = 0;
        if (quantity < 1) {
            return false;
        } else {
            newQuantity = parseInt(quantity) + 1;
        }
        $(this).addClass('loading');
        $('#productQuantity_'+rowId).val(newQuantity);
        var productTotal = prodPrice * newQuantity;
        productTotal = parseFloat(productTotal).toFixed(2);
        $('#productTotalPrice_'+rowId).text(productTotal);
        sendCartUpdateData(rowId, newQuantity, 0, $(this));
    });


    // Product Qunatity Decrease
    $(document).on('click', '.product-cart-remove', function () {
        var rowId      = $(this).data("id");
        var prodPrice   = $('#productDefaultPrice_'+rowId).text();
        var quantity    = $('#productQuantity_'+rowId).val();
        var newQuantity = 0;
        if (quantity <= 1) {
            return false;
        } else {
            newQuantity = parseInt(quantity) - 1;
        }
        $(this).addClass('loading');
        $('#productQuantity_'+rowId).val(newQuantity);
        var productTotal = prodPrice * newQuantity;
        productTotal = parseFloat(productTotal).toFixed(2);
        $('#productTotalPrice_'+rowId).text(productTotal);
        sendCartUpdateData(rowId, newQuantity, 0, $(this));
        
    });

    function sendCartUpdateData(aProdId, qty, isBundle, clickedElement) {
        var prod = {id: aProdId, isBundle: isBundle, qty: qty};
        $.ajax({
            url: cartUpdateUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                data:JSON.stringify(prod),
                _token: $('input[name=_token]').val()
            },
            success: function(result) {
                if(result.status) {
                    $('.prod-count').each(function(){
                       $(this).text(result.quantity); 
                    });
                    $('#cart_grand_total').text(result.total);
                    if (isBundle == 1) {
                        updateBundleProductsCount(aProdId, qty);
                    }
//                    $('#driver_charges_li').html(result.html_content);
                    $('#cart-items').html(result.html_content);
                    //alert('Product Updated Successfully.');
                } else {
                    alert('Error while updating Product|' + result.message);
                }
                clickedElement.removeClass('loading');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
    }

    // Bundle Quantity Increase
    $(document).on('click', '.bundle-cart-add', function () {
        var rowId      = $(this).data("id");
        var prodPrice   = $('#bundleDefaultPrice_'+rowId).text();
        var quantity    = $('#bundleQuantity_'+rowId).val();
        var newQuantity = 0;
        if (quantity < 1) {
            return false;
        } else {
            newQuantity = parseInt(quantity) + 1;
        }
        $(this).addClass('loading');
        $('#bundleQuantity_'+rowId).val(newQuantity);
        var productTotal = prodPrice * newQuantity;
        productTotal = parseFloat(productTotal).toFixed(2);
        $('#bundleTotalPrice_'+rowId).text(productTotal);
        sendCartUpdateData(rowId, newQuantity, 1, $(this));
    });

    // Bundle Quantity Decrease
    $(document).on('click', '.bundle-cart-remove', function () {
        var rowId      = $(this).data("id");
        var prodPrice   = $('#bundleDefaultPrice_'+rowId).text();
        var quantity    = $('#bundleQuantity_'+rowId).val();
        var newQuantity = 0;
        if (quantity <= 1) {
            return false;
        } else {
            newQuantity = parseInt(quantity) - 1;
        }
        $(this).addClass('loading');
        $('#bundleQuantity_'+rowId).val(newQuantity);
        var productTotal = prodPrice * newQuantity;
        productTotal = parseFloat(productTotal).toFixed(2);
        $('#bundleTotalPrice_'+rowId).text(productTotal);
        sendCartUpdateData(rowId, newQuantity, 1, $(this));
    });

    $(document).on('click','.remove-product',function(e){
	e.preventDefault();
        $(this).addClass("loading");
        var rowId   = $(this).data("id");
        $.ajax({
            url: cartRemoveUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                data:rowId,
                _token: $('input[name=_token]').val()
            },
            success: function(result) {
                if(result.status) {
                    //track intercom.io remove cart event.
                    var metaData = {productId: rowId};
                    trackIntercomEvent('cart-remove', metaData);
                    window.location = window.location.href;
                } else {
                    alert('Error while removing Product from cart|' + result.message);
                }
                $(this).removeClass("loading");
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
            }
        });
    });

    function updateBundleProductsCount(aProdId, qty) {
        $('#bundle_product_count_div_'+aProdId).find('input').each(function(index) {
            var elementClass = $(this).attr("class");
            if ('product-quan-default' == elementClass) {
                var rowId           = $(this).data("id"),
                fieldValue          = $(this).val();
                var newQuantity     = qty * fieldValue;
                $('#bundle_product_count_'+rowId).val(newQuantity);
            }
        });
    }

    $(document).on('click','.cart_popup_postcode_set',function(e) {
        // get nearesr input text box which is visible
        var postCode = $(this).closest('.modal-content').find('[class*="logged"]:visible').find('input[type="text"]');
        var currentElement = $(this);
        if (!$('.error').length > 0) {
            $(postCode).after('<div class="error"></div>');
        }
        var postCodeValue = postCode.val();
        if (postCodeValue.replace(/\s/g,"") == "") {
            $('.error').html('Please enter Postcode.');
            return false;
        }
        $(this).addClass('loading');
        $.ajax({
            url: cartSetDeliveryPostcodeUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                pin:postCodeValue,
                _token: $('input[name=_token]').val()
            },
            success: function(result) {
                if(result.status) {
                    var eventName = "postcode_search_success_" + postCodeValue;
                    //console.log(eventName);
                    Intercom('trackEvent', eventName);
                    window.location = window.location.href;
                } else {
                    var eventName = "postcode_search_no_products_" + postCodeValue;
                    //console.log(eventName);
                    Intercom('trackEvent', eventName);
                    $('.unservicable-zipcode').siblings('div:not(.modal-header)').hide();
                    $('.unservicable-zipcode').show();
                    $('#postcode_msg_p').html(result.message);
                }
                currentElement.removeClass('loading');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Some error. Please try refreshing page.");
                currentElement.removeClass('loading');
            }
        });
    });
    
    $(function() {
        function log( message ) {
            $( "<div>" ).text( message ).prependTo( "#postcode_auto" );
            $( "#postcode_auto" ).scrollTop( 0 );
        }
        $(document).on('keydown.autocomplete','#postcode_auto',function(){
            $(this).autocomplete({
                source: validPostCodeUrl,
                minLength: 1,
                select: function( event, ui ) {
                  log( ui.item);
                }
            });
        })
    });
    
    $(function() {
        function log( message ) {
            $( "<div>" ).text( message ).prependTo( "#postcode_selected" );
            $( "#postcode_selected" ).scrollTop( 0 );
        }
        $(document).on('keydown.autocomplete','#postcode_selected',function(){
            $(this).autocomplete({
                source: validPostCodeUrl,
                minLength: 1,
                select: function( event, ui ) {
                  log( ui.item);
                }
            });
        })
    });
});



