define(['jquery'],function($){
    var disScroll;
    var lastScrollTop = 0;
    var delat = 5;
    var navHight = $('#top-header').outerHeight();

    $(window).scroll(function(event){
        disScroll = true;
    });

    setInterval(function (){
        if(disScroll){
            hasScrolled();
            disScroll = false;
        }
    }, 250);

    function hasScrolled(){
        var st = $(this).scrollTop();
        var clientHeight =document.documentElement.clientHeight;
        if(Math.abs(st - lastScrollTop) <= delat){
            return;
        }
        if(st > navHight+120 && st > lastScrollTop){
            $('#top-header').removeClass('header-show').addClass('header-hide');
        }else{
            if(st + $(window).height() < $(document).height()){
                $('#top-header').removeClass('header-hide').addClass('header-show');
            }
        }
        lastScrollTop = st;
    }

    var href = window.location.href;
    var channel, nav = $('.header-nav .nav');
    var arr = {'home': '首页', 'survey': '问卷列表', 'vote': '快速问答', 'advertiserment': '体验广告', 'shop': '体验广告','exchange': '兑换中心'};
    $.each(arr, function(i, e){
        if(href == 'http://wang-jili.com/app_dev.php/'){ 
            nav.find('a').removeClass('active').eq(0).addClass('active');
            return false;
        }
        if(href.indexOf(i) != -1){
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
                expandCon.eq(i).show();
            }, function(){
                expandCon.eq(i).hide();
            });
        });
    }
});