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
    },250);

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

        $.each(expandCon, function(i, e){
            expandBtn.eq(i).hover(function(){
                expandCon.eq(i).show();
            }, function(){
                expandCon.eq(i).hide();
            });
        });
});