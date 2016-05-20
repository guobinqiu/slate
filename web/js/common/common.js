define(['jquery', 'routing'],function($){

    var navLinks = $('.header-nav .nav a');
    var navSlider = $('.header-nav .border');
    var curIndex = navLinks.parent().find('.active').index();

    if(curIndex != -1){
       navSlider.show().animate({ left: 115 * curIndex +'px'}, 100);
    }
    navLinks.hover(function(){
        var i = $(this).index();
        navSlider.show().animate({ left: 115 * i +'px'}, 100);
    }, function(){});
    
    navLinks.parent().hover(function(){
    }, function(){
        var i = navLinks.parent().find('.active').index();
        if(i != -1){
            navSlider.show().animate({ left: 115 * i +'px'}, 100);
        }else{
            navSlider.hide();
        }
    });

    var expandBtn = $('.expand-btn'),
        expandCon = $('.expand-con');

    if(expandCon.length > 0){
        $.each(expandCon, function(i, e){
            expandBtn.eq(i).hover(function(){
                expandCon.eq(i).show(30);
            }, function(){
                expandCon.eq(i).hide(30);
            });
        });
    }


    //返回顶部
    $('.goTop').click(function() {
        $('body,html').animate({ scrollTop: 0 }, 800);
    })

    $(window).resize(function() {
        resizeFooter();
    });
    var resizeFooter =function(){
        if($(window).height() > $("body").height()){
            $(".footer").css({"position":"fixed", "bottom":"0"});
        }
        else{
            $(".footer").css("position","static");
        }   
    }
    resizeFooter();
});