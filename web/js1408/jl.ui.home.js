/**
 * Created by wangliting on 14-7-26.
 */
(function($){
    $.fn.focusPic = function(options){
        var $this = $(this),
            sliderImg = $(options.sliderImg),
            sliderBtns = $(options.sliderBtns),
            config = {
                stepWid : 518,
                index : 0,
                timer : 5000,
                animateTimer : 1000
            };

        function nextPic(){
            if(config.index >= (sliderBtns.length -1)){
                sliderBtns.removeClass('active').eq(0).addClass('active');
                sliderImg.stop().animate({left: '0px'}, config.animateTimer);
                config.index = 0;
            }else{
                sliderBtns.removeClass('active').eq(config.index+1).addClass('active');
                sliderImg.stop().animate({left: '-='+ config.stepWid +'px'},config.animateTimer);
                config.index++;
            }
        }

        var s = setInterval(nextPic, config.timer);
        sliderBtns.hover(function(){
            clearInterval(s);
            config.index = sliderBtns.index(this);
            sliderBtns.removeClass('active').eq(config.index).addClass('active');
            sliderImg.stop().animate({left: '-' +config.index * config.stepWid +'px'}, config.animateTimer);
        }, function(){
            s = setInterval(nextPic, config.timer);
        });
        sliderImg.hover(function(){
            clearInterval(s);
        }, function(){
            s = setInterval(nextPic, config.timer);
        });
    }
})(jQuery);
(function($){
    $.fn.sliderC = function(options){
        var $this = $(this),
            preMenu = $(options.preMenu),
            nextMenu = $(options.nextMenu),
            sliderEle = $(options.sliderEle),
            config = {
                stepNum: 3,
                stepWid : 975,
                index : 0,
                timer : 2000,
                animateTimer : 1000
            };

        sliderEle.width(config.stepNum * config.stepWid);
        function next(){
            if(config.index >= (config.stepNum -1)){
                sliderEle.stop().animate({left: '0px'}, config.animateTimer);
                config.index = 0;
            }else{
                sliderEle.stop().animate({left: '-='+ config.stepWid +'px'},config.animateTimer);
                config.index++;
            }
        }
        function pre(){
            if(config.index <= 0){
                sliderEle.stop().animate({left: '-' + (config.stepNum -1) * config.stepWid +'px'}, config.animateTimer);
                config.index = (config.stepNum -1);
            }else{
                sliderEle.stop().animate({left: '+='+ config.stepWid +'px'},config.animateTimer);
                config.index--;
            }
        }
        preMenu.on('click', function(){ pre();});
        nextMenu.on('click', function(){ next();});
    }
})(jQuery);
(function($){
    $.fn.pageTurn = function(options){
        var $this = $(this),
            nextBtn = $(options.nextBtn),
            prevBtn = $(options.prevBtn),
            noticeCon = $(options.noticeCon),
            pageNumber = $(options.pageNumber),
            config = {
                index: 0,
                pageNum: noticeCon.find('ul').length || 2,
                stepWid: 244,
                animateTimer: 300
            };

        pageNumber.html((config.index + 1) + '/' + config.pageNum);
        function nextPage(){
            if(config.index >= (config.pageNum - 1)){
                config.index = (config.pageNum - 1);
            }else{
                noticeCon.stop().animate({ left: '-=' + config.stepWid + 'px'}, config.animateTimer);
                config.index++;
            }
            pageNumber.html((config.index + 1) + '/' + config.pageNum);
        }
        function prevPage(){
            if(config.index <= 0){
                config.index = 0;
            }else{
                noticeCon.stop().animate({ left: '+=' + config.stepWid + 'px'}, config.animateTimer);
                config.index--;
            }
            pageNumber.html((config.index + 1) + '/' + config.pageNum);
        }
        prevBtn.on('click', function(){ prevPage()});
        nextBtn.on('click', function(){ nextPage()});
    }
})(jQuery);
(function($){
    $.fn.tabEle = function(options){
        var $this = $(this),
            tabMenus = $(options.tabMenus),
            tabCon = $(options.tabCon);

        tabMenus.removeClass('active').eq(0).addClass('active');
        tabMenus.removeClass('active').eq(0).addClass('active');
        tabMenus.on('mouseover', function(){
            var index = tabMenus.index(this);
            tabCon.addClass('fnHide').eq(index).removeClass('fnHide');
            tabMenus.removeClass('active').eq(index).addClass('active');
        });
    }
})(jQuery);
(function($){
    $(function(){
        //$('.hotShops li').switchBg({switchMethod: 'hover', switchClass: 'active'});
        $('.slider').focusPic({ sliderImg: '.sliderImg', sliderBtns: '.sliderBtns b'});
        $('.shopSlider').sliderC({preMenu: '.preGroup', nextMenu: '.nextGroup', sliderEle: '.shopSliderMask ul'},config = {
                stepNum: 3,
                stepWid : 994,
                index : 0,
                timer : 2000,
                animateTimer : 1000
            });
        //$('.rank').tabEle({ tabMenus: '.rank .menu span', tabCon: '.rank .all ul'});
        $('.notice').pageTurn({ nextBtn: '.pageNumber .next', prevBtn: '.pageNumber .prev', noticeCon: '.notice .all', pageNumber: '.pageNumber span'});
        var monthBtn = $('.rank .menu .month'),
            yearBtn = $('.rank .menu .year'),
            rankCon = $('.rank .all'),
            rankMenus = $('.rank .menu span');
        monthBtn.on('mouseover', function(){
            rankMenus.removeClass('active');
            $(this).addClass('active');
            rankCon.stop().animate({ left: '0px'}, 500);
        });
        yearBtn.on('mouseover', function(){
            rankMenus.removeClass('active');
            $(this).addClass('active');
            rankCon.stop().animate({ left: '-244px'}, 500);
        });
        var scrollCon = $('.timeline ul');
        function textScroll(){
            scrollCon.stop().animate({ marginTop: '-36px'}, 500, function(){
                scrollCon.find('li').eq(0).appendTo(scrollCon);
                scrollCon.css({ marginTop: '-1px'});
            });
        }
        var s = setInterval(textScroll, 2000);
    });
})(jQuery);

