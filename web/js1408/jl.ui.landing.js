/**
 * Created by wangliting on 14-6-30.
 */
(function($){
    //图片淡入淡出切换
    $.fn.imgFade = function(options){
        var defaults = {
            config: {
                index: 0,
                timer: 1000,
                fadeTimer: 1000
            }
        };
        var options = $.extend(defaults, options);
        var sliderImg = $(options.sliderImg),
            menus = $(options.menus),
            sliderBg = $(options.sliderBg);

        function effect(){
            if(options.config.index > sliderImg.length - 1) options.config.index = 0;
            sliderImg.stop().fadeOut(options.config.fadeTimer).eq(options.config.index).stop().fadeIn(options.config.fadeTimer);
            menus.removeClass('active').eq(options.config.index).addClass('active');
            switch (options.config.index){
                case 0: sliderBg.removeClass().addClass('loginMask1'); break;
                case 1: sliderBg.removeClass().addClass('loginMask2'); break;
                case 2: sliderBg.removeClass().addClass('loginMask3'); break;
            }
            options.config.index++;
        }

        effect();
        var s = setInterval(effect, options.config.timer);
        menus.hover(function(){
            clearInterval(s);
            options.config.index = menus.index(this);
            effect();
        }, function(){
            s = setInterval(effect, options.config.timer);
        });
        menus.on('click', function(){
            options.config.index = menus.index(this);
            effect();
        });
        sliderImg.hover(function(){
            clearInterval(s);
        }, function(){
            s = setInterval(effect, options.config.timer);
        });
    }
})(jQuery);
(function($){
    //文字滚动
    $.fn.textScroll = function(options){
        var newsListUl = $(this);
        var defaults = {
            animateTimer: 2000,
            timer: 3000,
            direction: 'left'
        };
        var options = $.extend(defaults, options);

        function newsScroll(){
            var stepWid = options.direction == 'left'? newsListUl.find('li:first').outerWidth():newsListUl.find('li:first').outerHeight() ;
            if(options.direction == 'left'){
                newsListUl.css({width: '1000%'});
                newsListUl.stop().animate({left : '-'+ (stepWid + 30) +'px'}, options.animateTimer, function(){
                    newsListUl.css('left', '0px');
                    newsListUl.find('li:last').after(newsListUl.find('li:first'));
                });
            }else{
                newsListUl.css({width: '100%'});

                var newsListUl01Top, newsListUl01;
                if(newsListUl.length>=2){
                    newsListUl01Top = newsListUl.eq(1).css('top');
                }else{
                    newsListUl01Top = newsListUl.eq(0).css('top');
                }
                newsListUl01 = parseInt(newsListUl01Top.substring(newsListUl01Top.substring(0, newsListUl01Top.length - 2) >= 0 ? 0 : 1, newsListUl01Top.length - 2));

                if((newsListUl01+stepWid) < newsListUl.eq(0).outerHeight()){
                    if(newsListUl.length>=2){
                        newsListUl.eq(0).remove();
                        newsListUl = $(this);
                    }
                    newsListUl.eq(0).stop().animate({top:'-='+ stepWid +'px'}, options.animateTimer);
                }else{
                    newsListUl.eq(0).parent().append(newsListUl.eq(0).clone());
                    newsListUl = $(this);
                    newsListUl.eq(1).css({top: stepWid + 'px'});
                    newsListUl.stop().animate({top:'-='+ stepWid +'px'}, options.animateTimer);
                }
            }
        }
        var s2 = setInterval(newsScroll, options.timer);
        newsListUl.hover(function(){
            clearInterval(s2);
        }, function(){
            s2 = setInterval(newsScroll, options.timer);
        });
    }
})(jQuery);
(function($){
    //页面加载完毕时数字滚动到位
    $.fn.numScroll = function(options){
        var digit = $(this);
        var defaults = {
            digitH : 30,
            num: 99999,
            animateTimer: 3000
        };
        var options = $.extend(defaults, options);

        function numToArr(){
            var str = options.num.toString();
            var arr = str.split('');
            if(arr.length < digit.length){
                switch (digit.length - arr.length){
                    case 1 : str = '0' + str; break;
                    case 2 : str = '00' + str; break;
                    case 3 : str = '000' + str; break;
                    case 4 : str = '0000' + str; break;
                    default : break;
                }
                arr = str.split('');
            }
            return arr;
        }

        function numScroll(){
            var arr = numToArr();
            if(arr.length > digit.length){
                alert('数字超过显示长度！');
                return;
            }
            for(var i = 0; i< digit.length; i++){
                digit.eq(i).stop().animate({top: '-' + (arr[i]*options.digitH) + 'px'}, options.animateTimer);
            }
        }
        numScroll();
    }
})(jQuery);
(function($){
    //清除输入框内容
    $(function(){
        var inputBox = $('.loginForm li');
        inputBox.each(function(){
            var _self = $(this);
            var conInput = _self.find('.clearTxt');
            var inputTxt = _self.find('.defaultTxt');

            if(conInput.val() && conInput.val().length){
                inputTxt.css('display', 'none');
            }
            conInput.bind({
                focus: function(){
                    inputTxt.css('display', 'none');
                    $(this).addClass('active');
                },
                blur: function(){
                    if($(this).val() && $(this).val().length){
                        inputTxt.css('display', 'none');
                    }else{
                        inputTxt.css('display', 'block');
                    }
                    $(this).removeClass('active');
                }
            });
        });
    });
})(jQuery);
(function($){
    $(function(){
    	//图片渐隐渐现
        $('.slider').imgFade({
            sliderImg: '.sliderMask img',
            menus: '.menus b',
            sliderBg: '.loginMask',
            config: {
                index: 0,
                timer: 7000,
                fadeTimer: 500
            }
        });
        //最新动态文字滚动
        $('.newsList ul').textScroll({
            animateTimer: 3000,
            timer: 4000,
            direction: 'left'});
        //米粒数字
        $('.digits span').numScroll({
            digitH : 20,
            num: 598985,
            animateTimer: 2000
        });
    });
})(jQuery);
