$(document).ready(function () {

    var activeSub = $(document).find('.active-sub');
    if (activeSub.length > 0) {
        activeSub.parent().show();
        activeSub.parent().parent().find('.arrow').addClass('open');
        activeSub.parent().parent().addClass('open');
    }

    $('.datatable').dataTable({
        retrieve: true,
        "iDisplayLength": 100,
        "aaSorting": [],
        "aoColumnDefs": [
            {'bSortable': false, 'aTargets': [0]}
        ]
    });
    $('#product').dataTable({
        retrieve: true,
        "iDisplayLength": 100,
        "aaSorting": [],
        "aoColumnDefs": [
            {'bSortable': false, 'aTargets': [0]}
        ]
    });

    $('.ckeditor').each(function () {
        CKEDITOR.replace($(this));
    })

    $('.mass').click(function () {
        if ($(this).is(":checked")) {
            $('.single').each(function () {
                if ($(this).is(":checked") == false) {
                    $(this).click();
                }
            });
        } else {
            $('.single').each(function () {
                if ($(this).is(":checked") == true) {
                    $(this).click();
                }
            });
        }
    });

    $('.page-sidebar').on('click', 'li > a', function (e) {

        if ($('body').hasClass('page-sidebar-closed') && $(this).parent('li').parent('.page-sidebar-menu').size() === 1) {
            return;
        }

        var hasSubMenu = $(this).next().hasClass('sub-menu');

        if ($(this).next().hasClass('sub-menu always-open')) {
            return;
        }

        var parent = $(this).parent().parent();
        var the = $(this);
        var menu = $('.page-sidebar-menu');
        var sub = $(this).next();

        var autoScroll = menu.data("auto-scroll");
        var slideSpeed = parseInt(menu.data("slide-speed"));
        var keepExpand = menu.data("keep-expanded");

        if (keepExpand !== true) {
            parent.children('li.open').children('a').children('.arrow').removeClass('open');
            parent.children('li.open').children('.sub-menu:not(.always-open)').slideUp(slideSpeed);
            parent.children('li.open').removeClass('open');
        }

        var slideOffeset = -200;

        if (sub.is(":visible")) {
            $('.arrow', $(this)).removeClass("open");
            $(this).parent().removeClass("open");
            sub.slideUp(slideSpeed, function () {
                if (autoScroll === true && $('body').hasClass('page-sidebar-closed') === false) {
                    if ($('body').hasClass('page-sidebar-fixed')) {
                        menu.slimScroll({
                            'scrollTo': (the.position()).top
                        });
                    }
                }
            });
        } else if (hasSubMenu) {
            $('.arrow', $(this)).addClass("open");
            $(this).parent().addClass("open");
            sub.slideDown(slideSpeed, function () {
                if (autoScroll === true && $('body').hasClass('page-sidebar-closed') === false) {
                    if ($('body').hasClass('page-sidebar-fixed')) {
                        menu.slimScroll({
                            'scrollTo': (the.position()).top
                        });
                    }
                }
            });
        }
        if (hasSubMenu == true || $(this).attr('href') == '#') {
            e.preventDefault();
        }
    });

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

        $('.table-availability').each(function(){
            $(this).addClass('table');
        })

});