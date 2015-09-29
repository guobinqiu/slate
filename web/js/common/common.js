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

    function RPAExpand(options){
        this.expandBtn = options.expandBtn;
        this.expandCon = options.expandCon;
        this.init();
    }
    RPAExpand.prototype = {
        init: function(){
            var _self = this;
            $.each(_self.expandCon, function(i, e){
                $(this).eq(i).hide();
                _self.oper(i);
            });
        },
        oper: function(index){
            var _self = this;
            var curExpandBtn = _self.expandBtn.eq(index);
            var curExpandCon = _self.expandCon.eq(index);
            curExpandBtn.hover(function(){
                curExpandCon.show();
                //curExpandCon.slideToggle("fast");
            }, function(){
                curExpandCon.hide();
                //curExpandCon.slideToggle("fast");
            });
        }
    };
    var expand = new RPAExpand({
        expandBtn: $('.expand-btn'),
        expandCon: $('.expand-con')
    });
});