$(function(){
	/*==== Global Script ====*/

	var lastScrollTop = 0;
	$(window).scroll(function(event){
		var st = $(this).scrollTop();
		if (st < lastScrollTop){
			$('body').addClass('slide-up-mobile-header');
			$('body').removeClass('slide-down-mobile-header');
		}
		else {
			$('body').addClass('slide-down-mobile-header');
			$('body').removeClass('slide-up-mobile-header');
		}
	   lastScrollTop = st;
	});

	$(window).scroll(function(){
		if($(this).scrollTop() > 0){
			$('body').addClass('header-fixed');
		}
		else {
			$('body').removeClass('header-fixed');
			$('body').removeClass('slide-up-mobile-header');
			$('body').removeClass('slide-down-mobile-header');
		}
		//$('.siteHeader').removeClass('search-open');
		//$('.siteHeader').removeClass('search-open-fixed');
		//$('.search-result').removeClass('show-search');
		//$('.search-result').removeClass('loading');
	});

	$(document).on('click touchend','#search',function(e){
		e.stopPropagation();
		$('.siteHeader').addClass('search-open');
		$('body').addClass('search-active');
	});

	$(document).on('click','body',function(){
		$('body').removeClass('search-active');
		$('.siteHeader').removeClass('search-open');
		$('.siteHeader').find('#search').val('');
		$('.search-result').removeClass('show-search');
		$('.search-result').removeClass('loading');
	});

	$(document).on('click touchend','.btn-search-close',function(e){
		e.stopPropagation();
		$('#search').val('');
		$('.search-result').removeClass('show-search');
		$('.search-result').removeClass('loading');
		$('.siteHeader').removeClass('search-open');
		$('.siteHeader').removeClass('search-open-fixed');
		$('body').removeClass('search-active');
	});

	$(document).on('click','.top-navigation-links .btn-search',function(e){
		e.stopPropagation();
		$('.siteHeader').addClass('search-open-fixed');
	});

	$(document).on('click','.search-result .search-result-inner,.search-result .search-view-all',function(e){
		e.stopPropagation();
	});

	$(document).on('keyup','body',function(e){
		console.log(e.which);
		if(e.which == 27){
			if($('.search-open').length > 0){
				$('.siteHeader').find('#search').val('');
				$('.search-result').removeClass('show-search');
				$('.search-result').removeClass('loading');
				$('.siteHeader').removeClass('search-open');
				$('.siteHeader').removeClass('search-open-fixed');
				$('body').removeClass('search-active');
			}
		}
	});

	/*===== Home Page Script ======*/

	$('.feature-banner .item').each(function(){
		$(this).css('background-image','url("'+$(this).find('img').attr('src')+'")');
	});

	$('.home-slider .slider-wrap').owlCarousel({
		navigation : true, // Show next and prev buttons
		slideSpeed : 300,
		paginationSpeed : 400,
		singleItem:true,
		items : 1
	});

	$('.occation-slider .occation-item,.occation-wrap .occation-item,.featured-occation .occation-item,.create-event-wrap .occation-item, .occation-wrap .occation-item').each(function(){
		$(this).css('background-image','url("'+$(this).find('img.bgImg').attr('src')+'")');
	});

	$('#shop-by-product-list .product-group.active').each(function(){
		if($(this).find('.items-unavailable').length<=0){
			$(this).owlCarousel({
			    nav:true,
			    responsive:{ 
			    	0:{ items:3 }, 
			    	600:{ items:4 },
			    	768: {items: 5},
			    	1025:{ items:6 }
			    },
			    dots:false
			});
		}
	})

	$(document).on('click','.shop-by ul.nav-tabs li a',function(){
		var _currentItem = $(this).attr('data-target');
		var owl = $(_currentItem);
		/*$("#shop-by-product-list .product-group").each(function(){
			if($(this).data('owlCarousel')){
				$(this).data('owlCarousel').destroy();
			}
		});*/
		if($(_currentItem).find('.items-unavailable').length<=0){
			$(_currentItem).owlCarousel({
			    nav:true,
			    responsive:{ 
			    	0:{ items:3 }, 
			    	600:{ items:4 },
			    	768: {items: 5},
			    	1025:{ items:6 }
			    },
			    dots:false
			});
		}
	});

	$('.explore-occasion .explore-list .explore-item').each(function(){
		$(this).css('background-image','url("'+$(this).find('img.bgImg').attr('src')+'")');
	});

	$('.explore-occasion .explore-list').owlCarousel({
	    nav:true,
	    responsive:{ 
	    	0:{ items:2 }, 
	    	600:{ items:3 },
	    	768: {items: 3},
	    	1025:{ items:4 }
	    },
	    dots:false,
	    margin:10
	});

	$(document).on('click','.explore-sub-occasion .title-header .btn-red',function(){
		$('.explore-sub-occasion').fadeOut();
	});

	$(document).on('click','.explore-occasion .explore-item',function(e){
		//
                e.preventDefault();
                $('.explore-sub-occasion').addClass('loading');
                $('.explore-sub-occasion').fadeIn();
                var occasionId     = $(this).data("occasion-id");
                var occasionName   = $(this).data("occasion-name");
                $.ajax({
                    url: getSubOccasionUrl + "/" + occasionId,
                    method: 'GET',
                    success: function(result) {
                        $('.explore-sub-occasion').removeClass('loading');
                        $("#explore-sub-occasion").html(result.html_content);
                }});
	});
        
	$(document).on('click','.occasion_page_sub_tab',function(e){
            e.preventDefault();
            $('#category-content').addClass('loading');
            var occasionId     = $(this).data("occasion-id");
            $.ajax({
                url: getSubOccasionUrl + "/" + occasionId,
                method: 'GET',
                success: function(result) {
                    $('#category-content').removeClass('loading');
                    $("#explore-sub-occasion").html(result.html_content);
            }});
	});

        $(document).on('click','.theme_page_sub_tab',function(e){
            e.preventDefault();
            $('#category-content').addClass('loading');
            var eventId     = $(this).data("event-id");
            $.ajax({
                url: getSubEventUrl + "/" + eventId,
                method: 'GET',
                success: function(result) {
                    $('#category-content').removeClass('loading');
                    $("#explore-sub-occasion").html(result.html_content);
            }});
	});

	$(window).load(function(){
		$('.occation-slider').owlCarousel({
		    nav:false,
			dots:false,
			margin:10
			//autoWidth:true
			//loop:true
		})
	});

	/*==== Modal Vertically Function ====*/

	var modalVerticalCenterClass = ".modal";
	function centerModals($element) {
	    var $modals;
	    if ($element.length) {
	        $modals = $element;
	    } else {
	        $modals = $(modalVerticalCenterClass + ':visible');
	    }
	    $modals.each( function(i) {
	        var $clone = $(this).clone().css('display', 'block').appendTo('body');
	        var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
	        top = top > 0 ? top : 0;
	        $clone.remove();
	        $(this).find('.modal-content').css("margin-top", top);
	    });
	}
        
        /* For Single Modal */
        function centerModal($element) {
            var $modal;
            if ($element.length) {
                $modal = $element;
            } else {
                $modal = $(modalVerticalCenterClass + ':visible');
            }
            var $clone = $($modal).clone().css('display', 'block').appendTo('body');
            var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
            top = top > 0 ? top : 0;
            $clone.remove();
            $($modal).find('.modal-content').css("margin-top", top);
        }

	$(document).on('show.bs.modal',modalVerticalCenterClass, function(e) {
	    centerModals($(this));
	});
	$(window).on('load resize', centerModals);
        
        
	$(document).on('ajaxComplete',function(){
		centerModal('#openingModal');
	})
        

	$(document).on('ajaxComplete',function(){
		centerModal('#openingModal');
		centerModal('#promoCode');
	})

	/*==== Modal Vertically Function Ends ====*/

	$('.action-register').click(function () {
        if (!$(this).hasClass('link-register')) {
        	$('#login-register').addClass('register-active');
            $('.login-section').hide();
            $('.register-section').show();
            centerModal('#login-register');
        }
    });

	$('.action-login').click(function(){
		$('#login-register').removeClass('register-active');
		$('.login-section').show();
		$('.register-section').hide();
		centerModal('#login-register');
	});


	/*===== Mobile Functionality =====*/

	if($('#main-menu').length > 0){
		$('body').append('<div class="mobile-overlay"></div>');
	}
	$('.siteHeader .top-links').append('<div class="mobile-trigger"></div>');
	$('#main-menu > ul').prepend('<li class="mobile-header">Menu<span class="close-trigger"></span></li>');
	$('#main-menu > ul li').each(function(){
		if($(this).find('ul').length > 0){
			$(this).addClass('item-has-child');
			$(this).children('a').after('<div class="child-trigger"></div>');
		}
	});

	$('.mobile-trigger').click(function(){
		$('body').removeClass('mobile-out');
		$('body').addClass('mobile-in');
	});

	$('.close-trigger').click(function(){
		$('body').removeClass('mobile-in');
		$('body').addClass('mobile-out');
	});

	$(document).on('click touchstart','.mobile-overlay',function(){
		$('body').removeClass('mobile-in');
		$('body').addClass('mobile-out');
	});

	$('.child-trigger').click(function(){
		$(this).closest('li').siblings('li').find('ul').slideUp(250);
		$(this).closest('li').siblings('li').find('.child-trigger').removeClass('child-open');
		$(this).siblings('ul').slideToggle(250);
		$(this).toggleClass('child-open');
	});

	$(document).on('touchend','.menu-postcode',function(){
		$('#selected-location-popup').modal();
	});

	$(document).on('touchend','.menu-login',function(){
		$('#login-register').modal();
	});

	$(document).on('touchend','.menu-register',function(){
		$('#login-register').modal();
	});

	/*===== Store Section =====*/

	$('.store-description img').each(function(){
		$(this).after('<div class="bg-'+$(this).attr('class')+'" style="background-image:url('+$(this).attr('src')+');"></div>');
	});

	/*==== My Products =====*/

	$(document).on('click','.product-group > li > a',function(){
		$(this).parent('li').toggleClass('category-open');
		$(this).siblings('ul').slideToggle(250);
		$(this).parent('li').siblings('li').find('ul').slideUp(250);
		$(this).parent('li').siblings('li').removeClass('category-open')
	});

	/*==== Store Dashboard ====*/

	/*if($('.store-template .section-store-dashboard').length>0){
		$('body').addClass('store-dashboard-template');
	}*/

	$('.section-store-dashboard .store-order .order-listing img').each(function(){
		$(this).after('<div class="bg-'+$(this).attr('class')+'" style="background-image:url('+$(this).attr('src')+');"></div>');
	});

	$('.store-sales .sales-listing img').each(function(){
		$(this).after('<div class="bg-'+$(this).attr('class')+'" style="background-image:url('+$(this).attr('src')+');"></div>');
	});

	/*===== Store Sales Page ========*/

	$('.best-seller-slider').owlCarousel({
	    margin:0,
	    nav:false,
		dots:false,
		//items:4,
		responsive:{ 320:{ items:4 },768:{ items:7 } }
	});
        
        $('.my-best-seller-slider').owlCarousel({
	    margin:0,
	    nav:false,
		dots:false,
		//items:4,
		responsive:{ 320:{ items:4 },768:{ items:7 } }
	});
       

	/*==== Store Search and History Page ======*/

	if($('.stickyfooter').length>0){
		$('body').find('footer.siteFooter').hide();
	}

	/*===== Event Menu =======*/

	$('.occation-item[data-event-name]').on('click',function(e) {
		e.preventDefault();

                var eventId     = $(this).data("event-id");
                var eventName   = $(this).data("event-name");
                $('#event_popup_heading').html(eventName);
                var loadingContent = getLoadingImage();
                $("#event_popup_ul").html(loadingContent);
                $.ajax({
                    url: "getSubEvents/"+eventId,
                    method: 'GET',
                    success: function(result) {
                    var content = prepareEventList(result);
                    $("#event_popup_ul").html(content);
                }});
			$(this).closest('[class$="-inner-wrap"]').find('.event-menu').fadeIn();
			$(this).closest('[class$="-inner-wrap"]').find('.occasion-menu').fadeIn();
			$(document).ajaxComplete(function(){
				var h1 = $('[data-event-id="'+eventId+'"]').closest('[class$="-inner-wrap"]').find('.menu-wrap').outerHeight();
				var h2 = $('[data-event-id="'+eventId+'"]').closest('[class$="-inner-wrap"]').outerHeight();
				if(h1>h2){
					$('[data-event-id="'+eventId+'"]').closest('[class$="-inner-wrap"]').attr('disable-middle-align','true');
				}
				else{
					$('[data-event-id="'+eventId+'"]').closest('[class$="-inner-wrap"]').removeAttr('disable-middle-align');
				}
			})
	});
        
        $('.occation-item[data-occasion-name]').on('click',function(e){
            e.preventDefault();
                $('.explore-sub-occasion').addClass('loading');
                $('.explore-sub-occasion').fadeIn();
                var occasionId     = $(this).data("occasion-id");
                var occasionName   = $(this).data("occasion-name");
                $.ajax({
                    url: getSubOccasionUrl + "/" + occasionId,
                    method: 'GET',
                    success: function(result) {
                    $('.explore-sub-occasion').removeClass('loading');
                    $("#explore-sub-occasion").html(result.html_content);
                }});
        });

		$(document).on('click','.event-menu .close-event-menu',function(){
			$('[class$="-inner-wrap"]').removeAttr('disable-middle-align');
			$(this).parent('.event-menu').fadeOut();
		});

		$(document).on('click','.occasion-menu .close-occasion-menu',function(){
			$('[class$="-inner-wrap"]').removeAttr('disable-middle-align');
			$(this).parent('.occasion-menu').fadeOut();
		});

        function prepareEventList(result) {
            var html = '';
            $.each(result, function(key, value) {
                html = html + '<li><a href="'+ moodUrl +'/'+value.id +'/'+value.name.replace(/[\. ,:-]+/g, "-").toLowerCase()+'">'+value.name+'</a></li>';
            });
            return html;
        }
        
        function prepareOccasionList(result) {
            var html = '';
            $.each(result, function(key, value) {
                html = html + '<li><a href="'+ occasionUrl +'/'+value.id +'/'+value.name.replace(/[\. ,:-]+/g, "-").toLowerCase()+'">'+value.name+'</a></li>';
            });
            return html;
        }

        function getLoadingImage() {
            return "<img src='"+loadingImgUrl+"' />";
        }


	/*==== Create Event Description - Bought FOr =====*/

	$('.bought-frequency img').each(function(){
		$(this).after('<div class="bg-'+$(this).attr('class')+'" style="background-image:url('+$(this).attr('src')+');"></div>');
	});

	/*===== Fix For Sticky FOoter =====*/

	/*if($('.customer-content-section .stickyfooter').length<=0){
		$('.customer-content-section').addClass('no-sticky-footer');
	}*/

	$('.feature-banner').append('<div class="item" style="background-image:url('+$('.feature-banner .banner-img').attr('src')+')"></div>');

	/*===== My Order History Bookmark ======*/

	$(window).load(function(){
		var checkUrl = window.location.href;
		if (checkUrl.indexOf('#order-history-wrap') > -1) {
			$('ul.nav-tabs').find('li').removeClass('active');
			$('ul.nav-tabs a[href="#order-history-wrap"]').parent('li').addClass('active');

			$('.tab-content > div').removeClass('active').removeClass('in');
			$('.tab-content > div[id="order-history-wrap"]').addClass('active').addClass('in');
		}
	});

	/*-===== FAQ Accordian =======*/

	$('.section-faq .accr-title').on('click',function(){
		$(this).parent('li').siblings('li').removeClass('active');
		$(this).parent('li').siblings('li').find('.accr-content').slideUp(250);
		$(this).parent('li').toggleClass('active');
		$(this).siblings('.accr-content').slideToggle(250);
	});


	/*===== Order Basket ======*/

	$('.customer-basket-order-section .cart-products li ul.bucket-product').each(function(){
		$(this).closest('li').addClass('is-basket-order');
	});

	$('.customer-order-status .order-info ul li ul.bucket-product').each(function(){
		$(this).closest('li').addClass('is-basket-order');
	});

	$(document).on('click', '.customer-basket-order-section .is-basket-order > .col-left > .product-name', function(){
		$(this).closest('li').find('.bucket-product').slideToggle(250);
		$(this).closest('li').toggleClass('active');
	});

	$(document).on('click', '.customer-order-status .is-basket-order > .product-info > .product-name', function(){
		$(this).closest('li').find('.bucket-product').slideToggle(250);
		$(this).closest('li').toggleClass('active');
	});

	$(document).on('click','.checkout-cart-product .cart-products .is-basket-order .product-name',function(){
		$(this).closest('li').find('.bucket-product').slideToggle(250);
		$(this).closest('li').toggleClass('active');
	});

	/*$(document).on('click','.remove-product',function(e){
		e.preventDefault();
		$(this).closest('li').addClass('product-remove');
		$(this).closest('li').animate({
			opacity : 0
		},300,function(){
			$('.product-remove').remove();
		});
	});*/

	// Label With Checkbox 

	$(document).on('change','input[type="checkbox"]',function(){
		if($(this).is(':checked')){
			$(this).parent().addClass('checked');
		}
		else{
			$(this).parent().removeClass('checked');
		}
	});

	$(document).on('click','.radio-option',function(){
		$('input[type="radio"][name="'+$(this).find("input").attr("name")+'"]').each(function(){
			$(this).closest('.radio-option').removeClass('checked');
		})
		if($(this).find('input').is(':checked')){
			$(this).addClass('checked');
		}
		else{
			$(this).removeClass('checked');
		}
	});

	$('.check-option').each(function(){
		if($(this).find('input[type="checkbox"]').is(':checked')){
			$(this).addClass('checked');
		}
	});

	$('.radio-option').each(function(){
		if($(this).find('input[type="radio"]').is(':checked')){
			$(this).addClass('checked');
		}
	});

	/*===== Related Occation =======*/

	$('.related-occations ul li').each(function(){
		$(this).find('.bgImg').after('<div class="bgBanner" style="background-image:url('+$(this).find('.bgImg').attr('src')+');"></div>');
	});

	if ($('.search-cart').find('li').length >=3) {
		$('.search-cart').closest('.siteHeader').addClass('loginActive');
	}

	/*===== Profile Menu ======*/

	$('.tree-view > a').on('click',function(e){
		e.preventDefault();
		$(this).parent().siblings('.tree-view').find('.tree-child').slideUp(250);
		$(this).parent().siblings('.tree-view').find('a').removeClass('active');
		$(this).toggleClass('active');
		$(this).siblings('.tree-child').slideToggle(250);
	});
        
    $('.store-btn').on('click', function () {
    	$('#login-register').removeClass('register-active');
        $('.action-register').addClass('link-register');
        $('.action-register').attr('href', '/home/registervendor');
        $('.login-section').show();
        $('.register-section').hide();
        centerModal('#login-register');
    });

    $('.shopper-btn').on('click', function () {
        $('.action-register').removeClass('link-register');
        $('.action-register').removeAttr('href');
        $('.login-section').show();
        $('.register-section').hide();
        centerModal('#login-register');
    });

    $(document).on('hidden.bs.modal', '#login-register', function () {
        $('#login-register').find('form')[0].reset();
        $('#login-register').removeClass('register-active');
        $('.shopper-btn').trigger('click');
        $('.login-section').show();
        $('.register-section').hide();
        $(this).removeData('bs.modal');
    });

    $('.shop-info-link.type-accordian').on('click',function(){
    	$(this).closest('.shop-info').toggleClass('info-expended');
    	$(this).closest('.shop-info-title').siblings('ul').slideToggle(250);
    });

    $(document).on('click','.opening-time .day',function(){
    	$(this).toggleClass('active');
    	$(this).siblings('ul').slideToggle(250,function(){
    		centerModal('#openingModal');
    	});
    });

    $(document).on('change','.time-table li input[type="checkbox"]',function(){
    	if($(this).is(':checked')){
    		$(this).closest('li').addClass('active-strip');
    	}
    	else {
    		$(this).closest('li').removeClass('active-strip');
    	}
    });

    $(document).on('ready ajaxComplete',function(){
    	$('.time-table li input[type="checkbox"]').each(function(){
    		if($(this).is(':checked')){
    			$(this).parent().addClass('checked');
        		$(this).closest('li').addClass('active-strip');
        	}
        	else {
        		$(this).parent().removeClass('checked');
        		$(this).closest('li').removeClass('active-strip');
        	}
    	});
    });

    $(document).on('change','.time-table .opening-hrs input[type="radio"]',function(){
    	var selection_val = $('input[name="schedule"]:checked').val();

    	if(selection_val=='is_closed' || selection_val=='is_24hrs'){
    		$('.active-strip').addClass('save-state');
    	}
    	else {
    		$('.active-strip').removeClass('save-state');
    	}
    });

    $(document).on('ready ajaxComplete',function(){
    	$('input[name="schedule"]:checked').closest('label').addClass('checked');
    });

    $(document).on('click','.try_zipcode',function(){
    	$(this).closest('.unservicable-zipcode').hide();
    	$(this).closest('.unservicable-zipcode').siblings('div.logged-in-no-zip').show();
            $(this).closest('.unservicable-zipcode').siblings('div.not-logged-in').show();
            $('#postcode_auto').val('');
    });

    /*===== Delivery Address ======*/

    $(document).on('show.bs.modal','#zip-code-popup', function(e) {
    	$('#zip-code-popup .unservicable-zipcode').hide();
    	$('#zip-code-popup .logged-in-zip').show();
    	$('#zip-code-popup .first-time-zip').show();
    })

    /*=== Select Location Zipcode ======*/

    $(document).on('show.bs.modal','#selected-location-popup', function(e) {

    	$('#postcode_selected').val('');
    	$('#selected-location-popup .error').remove();
    	$('#selected-location-popup .unservicable-zipcode').hide();

	    if($('.available-zipcode #topPostCode').text().trim()==""){
	    	$('#selected-location-popup .first-time-zip').show();
	    	$('#selected-location-popup .logged-in-zip').hide();
	    }

	    if($('.available-zipcode #topPostCode').text().trim()!=""){
	    	$('#selected-location-popup .first-time-zip').hide();
	    	$('#selected-location-popup .logged-in-zip').show();
                console.log($('.available-zipcode #topPostCode').text());
	    	$('#selected-location-popup input[type="text"]').val($('.available-zipcode #topPostCode').text());
	    }

	    centerModal('#selected-location-popup');
	});

	/*======= Table Price Compare =======*/

	$(document).on('click','.table-price-compare .btn-edit',function(){
		$('.table-price-compare .available-price-wrap').hide();
		$('.table-price-compare .edit-price-wrap').show();
	});

	$(document).on('click','.table-price-compare .btn-confirm',function(){
		$('.table-price-compare .available-price-wrap').show();
		$('.table-price-compare .edit-price-wrap').hide();
	});

	$(window).on('load',function(){
		if($('#have-in-stock input[type="checkbox"]').is(':checked')){
			$('.table-price-compare').show();
		}
	});

	// Table Price COmparision (RRSP) in store
	$(document).on('change','#have-in-stock input[type="checkbox"]',function(){
		if($(this).is(":checked")){
			$('.table-price-compare').show();
		}
		else {
			$('.table-price-compare').hide();
		}
	})

	// Hide COokie Policy Notification in website
	$(document).on('click','.close-cookies-policy',function(){
		$('.cookies-policy-instruction').fadeOut();
	});

	// Adjust horizontal spacer in subcategory in sitemap page
	$('.sitemap-section hr').each(function(){
		$(this).css({
			top: $(this).siblings('.category-name').outerHeight()/2
		});
	});

	// Open product tab with respect to URL

	var ifCompleted = false;

	$(document).on('ajaxComplete',function(){
		if(ifCompleted===false){
			var checkPath = window.location.pathname;
			checkPath = checkPath.trim().toLowerCase();
			checkPath = checkPath.substring(checkPath.indexOf('products/'));
			checkPath = checkPath.split('/');
	//		console.log(checkPath);

			if(checkPath.length === 7) {
				$('.product-group li').each(function(){
					var productCat = $(this).find('.product-group-name').text().trim().toLowerCase();
					productCat = productCat.replace(/\s+/g,'-');
					productCat = productCat.replace(/[^A-Za-z0-9\-]/g,'');

	//				console.log(checkPath[5]);
	//				console.log(productCat);

					if(productCat === checkPath[5]){
						$(this).addClass('category-open');
						$(this).find('ul').slideDown(250);
					}
				});
			}

			ifCompleted = true;
		}
	});
        
	$('.store-selected span').text($('.store-selected ul li.store-active a').text().trim().toLowerCase());

	$(document).on('mouseenter touchstart','.info-icon',function(){
		$(this).siblings('.store-info').css({
			top: -($(this).siblings('.store-info').outerHeight())
		});
	});

	/* Generate Clickon Enter Event */

	$(document).on('keypress','#selected-location-popup #postcode_selected,#zip-code-popup #postcode_auto',function(e){
		if(e.which === 13){
			console.log('event fires');
			$(this).closest('.modal-body').siblings('.modal-footer').find('.cart_popup_postcode_set').trigger('click');
			return false;
		}
	});

	/*===== Dashboard =======*/
	$('.user-orders table.dataTable tbody tr td').on('click',function(){
		location.href = $(this).closest('tr').find('.order-summary-link').attr('href');
	})
	
	/*==== Phone Number Validation ======*/
	$(document).on('keypress','.phone-group input',function(evt){
		evt = (evt) ? evt : window.event;
	    var charCode = (evt.which) ? evt.which : evt.keyCode;
	    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
	        return false;
	    }
	    return true;
	});

	/*==== Checkout Page =======*/
	if($('.checkout-cart-product').length > 0){
		$('#main').addClass('checkout-cart-page');
	}

	/*==== Bookmark Navigation =======*/
	if($(window.location.hash).length > 0){
		$('body').addClass('loading-content');
		$(window.location.hash).find('a').addClass('active');
		$(window.location.hash).find('.tree-child').slideDown(250);
	}
	$(window).load(function(){
		$('body').removeClass('loading-content');
		if($(window.location.hash).length > 0){
			$('body').animate({
				scrollTop: $(window.location.hash).offset().top
			},500)
		}
	});

	/*==== Three Column Group ======*/

	$('.work-group.three-column-group .group-item .group-banner').each(function(){
		$(this).after('<div class="group-banner-bg" style="background-image:url('+$(this).attr('src')+');"></div>');
	});

	/*==== Mobile User Icon ====*/
	if($('.siteHeader .top-navigation-links ul li a.btn-account').siblings('ul').length > 0){
		$('.siteHeader .top-navigation-links ul li a.btn-account').parent().addClass('hidden-xs');
	}

	/*==== Play Video =====*/
	$('.video-button').click(function() {
		$(this).toggleClass('pause');
		$(this).siblings('.container').find('video').each(function(){
			this.paused ? this.play() : this.pause();
			if(this.play){
				$('.video-button').hide();
			}
		})
    });

    var timeout = null;

    $(document).on('mousemove','.video-container',function(){
            $('.video-button').show();

            clearTimeout(timeout);
            timeout = setTimeout(function() {
                    if($('.video-button').hasClass('pause')){
                            $('.video-button').hide();
                    }
            }, 1000);

    });

    $(document).on('click', '[data-intercom-event]', function () {
        var eventName = $(this).data("intercom-event");
        Intercom('trackEvent', eventName);
        //console.log(eventName);
    });

    /*==== Page Info Content in Shoreditch =====*/
    $(document).on('click','.page-info .more-link a',function(e){
    	e.preventDefault();
    	$(this).closest('.more-link').siblings('.content').addClass('show-more');
    	$(this).hide();
    });

    /*==== Multibanner ======*/
    if($('.multibanner-section').length > 0){
    	var imageCount = $('.multibanner-section').find('img').length;
    	if(imageCount == 1){
    		$('.multibanner-section').addClass('banner-count-1');
    	}
    	if(imageCount == 2){
    		$('.multibanner-section').addClass('banner-count-2');
    	}
    	if(imageCount == 3){
    		$('.multibanner-section').addClass('banner-count-3');
    	}
    	if(imageCount == 4){
    		$('.multibanner-section').addClass('banner-count-4');
    	}
    }
    $('.multibanner-section .multibanner-image').each(function(){
    	var banner = $(this).attr('src');
    	$('.multibanner-section .multibanner-banner-wrap').append("<div class='multibanner-bg' style=background-image:url('"+banner+"');></div>");
    });

    /*===== Landing Slider =====*/
    if($(window).width() >767){
    	// How we work 
	    $('.benefits-slides').owlCarousel({
		    nav:false,
		    responsive:{
		    	320: {items: 1},
		    	768: {items: 4}
		    },
		    dots:true,
		    mouseDrag: false,
		    touchDrag: true
		});

	    // As Seen In
		$('.seen-in ul').owlCarousel({
		    nav:false,
		    responsive:{
		    	320: {items: 2},
		    	768: {items: 4}
		    },
		    dots:true,
		    mouseDrag: false,
		    touchDrag: true
		});

		// Partners
		$('.our-partners .partners-list').owlCarousel({
		    nav:false,
		    responsive:{
		    	320: {items: 2},
		    	768: {items: 5}
		    },
		    dots:true,
		    margin: 12,
		    mouseDrag: false,
		    touchDrag: true
		});

		$('.our-partners-mobile .partners-list').owlCarousel({
		    nav:false,
		    responsive:{
		    	320: {items: 2},
		    	768: {items: 5}
		    },
		    dots:true,
		    margin: 5,
		    mouseDrag: false,
		    touchDrag: true
		});

		// Work With us
		$('.work-with-us .work-group').owlCarousel({
		    nav:false,
		    responsive:{
		    	320: {items: 1},
		    	768: {items: 3}
		    },
		    dots:true,
		    margin: 12,
		    mouseDrag: false,
		    touchDrag: true
		});
    }
    else {
    	// How we work 
	    $('.benefits-slides').owlCarousel({
		    nav:false,
		    responsive:{
		    	320: {items: 1},
		    	768: {items: 4}
		    },
		    dots:true,
		    mouseDrag: false,
		    touchDrag: true,
		    loop: true,
		    autoplay: true,
		    autoplayTimeout: 3000,
		    autoplaySpeed: 1000
		});

	    // As Seen In
		$('.seen-in ul').owlCarousel({
		    nav:false,
		    responsive:{
		    	320: {items: 2},
		    	768: {items: 4}
		    },
		    dots:true,
		    mouseDrag: false,
		    touchDrag: true,
		    loop: true,
		    autoplay: true,
		    autoplayTimeout: 3000,
		    autoplaySpeed: 1000
		});

		// Partners
		$('.our-partners .partners-list').owlCarousel({
		    nav:false,
		    responsive:{
		    	320: {items: 2},
		    	768: {items: 5}
		    },
		    dots:true,
		    margin: 12,
		    mouseDrag: false,
		    touchDrag: true,
		    loop: true,
		    autoplay: true,
		    autoplayTimeout: 3000,
		    autoplaySpeed: 1000
		});

		$('.our-partners-mobile .partners-list').owlCarousel({
		    nav:false,
		    responsive:{
		    	320: {items: 2},
		    	768: {items: 5}
		    },
		    dots:true,
		    margin: 5,
		    mouseDrag: false,
		    touchDrag: true,
		    loop: true,
		    autoplay: true,
		    autoplayTimeout: 3000,
		    autoplaySpeed: 1000
		});

		// Work With us
		$('.work-with-us .work-group').owlCarousel({
		    nav:false,
		    responsive:{
		    	320: {items: 1},
		    	768: {items: 3}
		    },
		    dots:true,
		    margin: 12,
		    mouseDrag: false,
		    touchDrag: true,
		    loop: true,
		    autoplay: true,
		    autoplayTimeout: 3000,
		    autoplaySpeed: 1000
		});
    }
})

// track function for intercom.io
function trackIntercomEvent(eventName, metadata){
    //console.log(eventName);
    //console.log(metadata);
    Intercom('trackEvent', eventName, metadata);
}

