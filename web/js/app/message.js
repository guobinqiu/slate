require(['../config'],function(){
    require(['common']);
    require(['tab']);
    require(['jquery'],function($){
       	var navLinks = $('.main-personal-message .btn span');
	    var navSlider = $('.main-personal-message .btn .border');
		var curIndex = navLinks.parent().find('.active').index();

	    if(curIndex != -1){
	        navSlider.show().animate({ left: 110 * curIndex +'px'}, 100);
	    }
	    navLinks.hover(function(){
	        var i = $(this).index();
	        navSlider.show().animate({ left: 110 * i +'px'}, 100);
	    }, function(){});
	    navLinks.parent().hover(function(){
	    }, function(){
	        var i = navLinks.parent().find('.active').index();
	        if(i != -1){
		        navSlider.show().animate({ left: 110 * i +'px'}, 100);
		    }else{
		    	navSlider.hide();
		    }
	    });
   	});
   	require(['jquery'],function($){
       	var titles = $('.main-personal-message .con li');
       	 titles.on('click', function(){
            var index = $(this).index(),
            	cons = titles.eq(index).find('p');

            if(cons.length > 0){
            	cons.slideToggle();
	            if(titles.eq(index).hasClass('active')){
	                titles.eq(index).removeClass('active');
	            }else{
	                titles.removeClass('active').eq(index).addClass('active');
	            }
            }
        });
   	});
});