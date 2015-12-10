/**
 * Created by wangliting on 14-8-14.
 */
(function(window, $){
    $.fn.slider = function(options){
        var $document = $(document),
            sliderCon = $(options.sliderCon),
            sliderBtn = options.sliderBtn || null,
            sliderMenus = options.sliderMenus || null,
            curNumPos = options.curNumPos || null,
            defaults = {
                stepWid : 244,
                index : 0, 
                animateTimer : 300,
                stepNum: 3,
                autoSwitch: false,
                isLoop: false,
                direction: 'left',
				isDebug: false
            };
        var config = $.extend({}, defaults, options.config);
        var s;

        sliderCon.width(config.stepNum * config.stepWid);
        if(curNumPos){
            $(curNumPos).html((config.index + 1) + '/' + config.stepNum);
        }
		function debugMsg() {
			if(config.isDebug) {
                return window.console && console.log.call(console, arguments);
            }
        }
        function next(){
            if(config.index >= (config.stepNum -1)){
				debugMsg('执行到最后一组');
                if(sliderMenus){
					debugMsg('有索引按钮的话，需要当前索引的按钮样式改变');
                    $(sliderMenus).removeClass('active').eq(0).addClass('active');
                }
                if(config.isLoop){
					debugMsg('要循环切换，切换到第一个');
                    sliderCon.stop().animate({ left: '0px'}, config.animateTimer);
                    config.index = 0;
                }else{
					debugMsg('不要循环切换，要停下来歇息一下');
                    if(config.autoSwitch){
						debugMsg('是自动切换哎，有定时器咯，要进行清除哦');
                        clearInterval(s);
                    }
                }
            }else{
				debugMsg('当前索引值：', config.index);
                if(sliderMenus){
					debugMsg('给当前按钮变换class');
                    $(sliderMenus).removeClass('active').eq(config.index+1).addClass('active');
                }
                sliderCon.stop().animate({left: '-='+ config.stepWid +'px'},config.animateTimer);
                config.index++;
            }
            if(curNumPos){
				debugMsg('执行插入当前页码');
                $(curNumPos).html((config.index + 1) + '/' + config.stepNum);
            }
        }
        function prev(){
            if(config.index <= 0){
                if(sliderMenus){
                    $(sliderMenus).removeClass('active').eq(config.stepNum -1).addClass('active');
                }
                if(config.isLoop){
                    sliderCon.stop().animate({left: '-'+(config.stepNum -1)*config.stepWid + 'px'}, config.animateTimer);
                    config.index = (config.stepNum -1);
                }else{
                    if(config.autoSwitch){
                        clearInterval(s);
                    }
                }
            }else{
                if(sliderMenus){
                    $(sliderMenus).removeClass('active').eq(config.index+1).addClass('active');
                }
                sliderCon.stop().animate({ left: '+=' + config.stepWid + 'px'}, config.animateTimer);
                config.index--;
            }
            if($(curNumPos)){
                $(curNumPos).html((config.index + 1) + '/' + config.stepNum);
            }
        }
        if(sliderBtn){
			debugMsg('有上下翻页按钮');
            $(sliderBtn).parent().find('.prevBtn').on('click', function(){ prev();});
            $(sliderBtn).parent().find('.nextBtn').on('click', function(){ next();});
        }
        if(sliderMenus){
			debugMsg('有索引按钮，要跟随改变当前按钮样式哦');
            $(sliderMenus).hover(function(){
                config.index = $(sliderMenus).index(this);
                $(sliderMenus).removeClass('active').eq(config.index).addClass('active');
                sliderCon.stop().animate({left: '-' +config.index * config.stepWid +'px'}, config.animateTimer);
            }, function(){});
        }
        function autoS($ele){
            $ele.hover(function(){
                clearInterval(s);
            }, function(){
                s = setInterval(next, config.timer);
            });
        }
        if(config.autoSwitch){
			debugMsg('可以自动切换喽，要加定时器的');
            s = setInterval(next, config.timer);
            autoS(sliderCon);
            if(sliderBtn){autoS($(sliderBtn));}
            if(sliderMenus){autoS($(sliderMenus));}
        }
    }
})(window, jQuery);
(function($){
    $(function(){
        $('.hotShops li').switchBg({switchMethod: 'hover', switchClass: 'active'});
        $('.slider').slider({ sliderCon: '.sliderImg', sliderMenus: '.sliderBtns b', config: {
            stepWid : 518,
            timer : 5000,
            animateTimer : 1000,
            stepNum: $('.sliderBtns b').length,
            autoSwitch: true,
            isLoop: true
        }});
        $('.shopSlider').slider({ sliderCon: '.shopSliderMask ul', sliderBtn: '.shopSlider bdo', config: {
            stepWid : 976,
            animateTimer : 1000,
            isLoop: true
        }});
        $('.notice').slider({ sliderCon: '.notice .all', sliderBtn: '.pageNumber a', curNumPos: '.pageNumber span'});
        $('.rank').slider({ sliderCon: '.rank .all', sliderMenus: '.menu span'});
        var scrollCon = $('.timeline ul');
        function textScroll(){
            scrollCon.stop().animate({ marginTop: '-36px'}, 500, function(){
                scrollCon.find('li').eq(0).appendTo(scrollCon);
                scrollCon.css({ marginTop: '-1px'});
            });
        }
        var s = setInterval(textScroll, 2000);
		$('.adMask, .activityAdWrapper .close, .activityAdBg, .closeBtn').on('click', function(){
			$('.activityAd').animate({ left: '950px', top: '0px', width: '0px', height: '0px', opacity: '0'}, 500, function(){$('.adMask').hide();});
		});
		var ruleDetail = $(".ruleDetail");
		$(".ruleCon").hover(function(){
			ruleDetail.show();
		}, function(){
			ruleDetail.hide();
		});
		$.ajax({
			 url: Routing.generate('jili_frontend_decemberactivity_geteggsinfo'),
			 type: 'post',
			 dataType: 'json',
			 success: function(eggData){
				 if($.isEmptyObject(eggData)|| undefined === eggData.data || parseInt(eggData.data.numOfEggs + eggData.data.numOfConsolationEggs) <= 0) {//判断结果或金蛋个数是否为空
				 	 $('.eggTag').hide();
					 return false;
				 }else{
					 $('.eggTag').html('+' + (eggData.data.numOfEggs + eggData.data.numOfConsolationEggs)).show();
				 }
			 },
			 error: function(){
				console.log('第一次请求失败……');
			 }
		 });
    });
})(jQuery);

(function($){
	//截取字符串长度
		jQuery.fn.limit=function(){ 
			var self = $("span[limit]"); 
			self.each(function(){ 
				var objString = $(this).text(); 
				var objLength = $(this).text().length; 
				var num = $(this).attr("limit"); 
				if(objLength > num){ 
					$(this).attr("title",objString); 
					objString = $(this).text(objString.substring(0,num) + "..."); 
				} 
			}) 
		} 
		$(function(){ 
			$(document.body).limit(); 
		}) 
})(jQuery);

$(document).ready(function(){
    //fold and unfold while click button
        var unfold = $('#unfold');
        var text = $('#unfold span');
        var arrow = $('#unfold em');
        var target = $('#noticeCont');
        var fold = $('.noticeCont .warn .fold');

        unfold.click(function() {
            arrow.toggleClass('arrow-unfolded');
            if (target.css('display') !== 'none'){
                text.html("点击展开");
                target.slideUp(600);
                }else{
                text.html("点击收起");
                target.slideDown(800);
                }
        });
        fold.click(function() {
            target.slideUp(600);
        });
});