(function($) {
    $.fn.verticalZoom = function(options) {
    	var settings = $.extend({
            thumbCount : 2,
            thumbMargin : 10,
            zoom : true
        }, options);

        return this.each( function() {
        	_this = $(this);
        	$(this).addClass('vz-outer-wrap');
        	$(this).children().wrapAll('<div class="vz-inner-wrap"></div>');
        	$(this).append('<div class="vz-thumb-wrap"><div class="thumb-container"></div></div><div class="thumb-nav"><span class="prev">prev</span><span class="next">next</span></div>');
        	var current_slider = $(this).find('.vz-thumb-wrap .thumb-container');
        	$(this).find('img').each(function(){
        		$(current_slider).append('<a><img src="' + $(this).attr('src') + '"/></a>');
        	});
        	$(this).find('.vz-thumb-wrap a').first().addClass('active');
        	$(this).find('.vz-inner-wrap .items').first().addClass('active-slide').show();
        	$(this).find('.vz-thumb-wrap a').height($(this).find('.vz-thumb-wrap').height()/settings.thumbCount);
        	
        	var slideToMove = $(this).find('.thumb-container');
        	var currentTranslate = 0;

        	if($(current_slider).find('img').length < 2){
        		$('.prev').attr('disabled','disabled');
        		$('.next').attr('disabled','disabled');
        	}

        	$('.prev').attr('disabled','disabled');

            $(_this).css({
                'opacity':1
            });

        	$(this).find('.next').on('click',function(){
        		if(-(currentTranslate)<=($(current_slider).height()-$(current_slider).parent().height())){
        			currentTranslate = currentTranslate - $(current_slider).parent().height()/settings.thumbCount;
	        		$(slideToMove).css({
	        			'transform': 'translateY('+ currentTranslate+'px)'
	        		});
	        		if(-(currentTranslate)>=($(current_slider).height()-$(current_slider).parent().height())){
	        			$(this).attr('disabled','disabled');
	        		}
	        		else {
	        			$(this).removeAttr('disabled','disabled');
	        		}
        		}
        		$('.prev').removeAttr('disabled','disabled');
        	});

        	$(this).find('.prev').on('click',function(){
        		if(currentTranslate < 0){
        			currentTranslate = currentTranslate + $(current_slider).parent().height()/settings.thumbCount;
	        		$(slideToMove).css({
	        			'transform': 'translateY('+ currentTranslate+'px)'
	        		});
	        		if(currentTranslate >= 0){
	        			$(this).attr('disabled','disabled');
	        		}
	        		else {
	        			$(this).removeAttr('disabled','disabled');
	        		}
        		}
        		$('.next').removeAttr('disabled','disabled');
        	});

        	$(this).find('.vz-thumb-wrap a').on('click',function(){
        		var itemShow = $(this).index()+1;
        		$(_this).find('.vz-inner-wrap .items').removeClass('active-slide').hide();
        		$(_this).find('.vz-inner-wrap .items:nth-child('+itemShow+')').addClass('active-slide').show();
        	});

        });
    }
}(jQuery));