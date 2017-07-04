/*-------------------
依赖jquery.js, routing.js
-------------------*/
$(function(){

    //返回顶部
    $('.goTop').click(function() {
        $('body,html').animate({ scrollTop: 0 }, 800);
    });

    $(window).resize(function() {
        resizeFooter();
    });
    var resizeFooter =function(){
        if($(window).height() > $("body").height()){
            $("#footer").css({"position":"fixed", "bottom":"0", "left": "0", "right": "0"});
            $(".navLayerBg").add($(".navLayer")).css("position","fixed");
        }
        else{
            $("#footer").css("position","static");
            $(".navLayerBg").add($(".navLayer")).css("position","absolute");
        }   
    };
    resizeFooter();

    // mobile dropdown
    var expandMobileBtn = $('.navCollapsed'),
    expandMobileCon = $('.navLayer'),
    navLayerBg = $('.navLayerBg'),
    navbarUn = $('.navbarUn'),
    navbar = $('.navbar'),
    wrap = $('.wrap');

    expandMobileBtn.add(navLayerBg).click(function () {
        if(expandMobileCon.is(":hidden")){
            expandMobileCon.slideDown(600);
            navLayerBg.show();
            expandMobileBtn.parent().find('b').show();
            navbarUn.addClass('fixed');
            navbar.addClass('fixed');
            wrap.addClass('unfixed');
        } else if(expandMobileCon.is(":visible")){
            expandMobileCon.slideUp(600);
            navLayerBg.hide();
            expandMobileBtn.parent().find('b').hide();
            navbarUn.removeClass('fixed');
            navbar.removeClass('fixed');
            wrap.removeClass('unfixed');
        }
    });

});