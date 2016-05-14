define(['jquery', 'routing'],function($){
    var href = window.location.href;
    var nav = $('.header-nav .nav');
    var arr = {'callboard': '首页', 'survey': '问卷列表', 'vote': '快速问答', 'advertiserment': '任务体验', 'shop': '任务体验','exchange': '兑换中心'};
    $.each(arr, function(i, e){
        if(href == Routing.generate('_homepage')){ 
            nav.find('a').removeClass('active').eq(0).addClass('active');
            return false;
        }
        if(href.indexOf('user') == -1 && href.indexOf(i) != -1){
            var navs = nav.find('a');
            var len = navs.length;
            for(var j = 0; j < len; j++){
                if(navs.eq(j).html() == e){
                    nav.find('a').removeClass('active').eq(j).addClass('active');
                    return false;        
                }
            }
        }else{
            nav.find('a').removeClass('active');
        }    
    });

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