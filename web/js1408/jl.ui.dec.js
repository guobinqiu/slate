/**
 * Created by wangliting on 14-12-1.
 */
function formClear(){
    var inputBox = $('.shoppingForm li');
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
        inputTxt.on('click', function(){
			conInput.focus();
            $(this).css('display', 'none');
            conInput.addClass('active');
        });
    });
}
// 对Date的扩展，将 Date 转化为指定格式的String 
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符， 
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字) 
// 例子： 
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423 
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18 
Date.prototype.Format = function(fmt) 
{ //author: meizz 
  var o = { 
    "M+" : this.getMonth()+1,                 //月份 
    "d+" : this.getDate(),                    //日 
    "h+" : this.getHours(),                   //小时 
    "m+" : this.getMinutes(),                 //分 
    "s+" : this.getSeconds(),                 //秒 
    "q+" : Math.floor((this.getMonth()+3)/3), //季度 
    "S"  : this.getMilliseconds()             //毫秒 
  }; 
  if(/(y+)/.test(fmt)) 
    fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
  for(var k in o) 
    if(new RegExp("("+ k +")").test(fmt)) 
  fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length))); 
  return fmt; 
}
function formCheck(){
	function compareTime(startDate, endDate){
		var arr = startDate.split("-");
		var starttime = new Date(arr[0], arr[1], arr[2]);
		var starttimes = starttime.getTime();
		var arrs = endDate.split("-");
		var lktime = new Date(arrs[0], arrs[1], arrs[2]);
		var lktimes = lktime.getTime();
		if (starttimes > lktimes) {
			return false;
		}else{
			return true;
		}
	}   
    var startDate = '2014-12-12', 
		endDate = '2015-1-12', 
		curDate = new Date().Format("yyyy-MM-dd"),
		inputDate, orderNum;
	inputDate = $('#datepicker').val().toString();
	orderNum = $('#orderNum').val().toString().replace(/\s+/g,"");
	if(compareTime(startDate, inputDate)&&compareTime(inputDate, endDate)){
		if(compareTime(inputDate, curDate)){
			if(orderNum.match(/[^a-zA-Z\d]/g) || orderNum.length > 20) {
				$('.errorMsg').html('*您输入的订单格式或长度不对，请重新输入！');
				return false;
			}
			return true;
		}else{
			$('.errorMsg').html('*您输入的时间大于当前时间，请重新输入！');
			return false;
		}
	}else{
		$('.errorMsg').html('*您输入的时间不在活动范围内，请重新输入！');
		return false;
	}
}
function textScroll(){
    var scrollCon = $('.winnerList ul');
    scrollCon.stop().animate({ marginTop: '-36px'}, 500, function(){
        scrollCon.find('li').eq(0).appendTo(scrollCon);
        scrollCon.css({ marginTop: '-1px'});
    });
}
function runFlow(){
    var decSort = $('.decSort'), decList = $('.decList');
    function getLeftPos(){
        var runElePosX = $('.decMainCon').offset().left - 150;
        decSort.addClass('decSortActive').css({ left: Math.floor(runElePosX) + 'px'});
    }
    $(window).resize(function(){
        getLeftPos();
    });
    $(window).scroll(function(){
        var scrollH = $(this).scrollTop();
        var decListDistance = distance + decList.position().top;
        if(scrollH > decListDistance){
            getLeftPos();
            decSort.addClass('decSortActive').css({ top: '0px'});
        }else{
            decSort.removeClass('decSortActive').css({ left: '-150px', top: decList.position().top + 'px'});
        }
    });
    var distance, titleDistance;
    function initDecSort(){
        decSort.css({ top: decList.position().top + 'px'}).fadeIn(1E3);
        distance = decList.parent().position().top;
    }
    initDecSort();
    function getDistance(ele){
        titleDistance = distance + Math.floor(ele.position().top);
        return titleDistance;
    }
    function goTop(pos){
        $("html,body").animate({ scrollTop: pos + "px"},500);
    }
    $('.backTop').on('click', function(){
        goTop(0);
    });
    decSort.find('li').on('click', function(){
        var index = decSort.find('li').index(this);
        decSort.find('li').removeClass('active').eq(index).addClass('active');
        if(index > decList.find('h2').length){ return;}
        goTop(getDistance(decList.find('h2').eq(index)));
    });
}
function checkFlow(){
    var decRule = $('.decRule'), decRuleBtn = $('.decRuleBtn, .decTopCon .goBtn, .noStart, .noLogin, .timestamp');
	var datepicker = $( "#datepicker, .defaultTxt" );
	var tipsMsg = $('.errorMsg .tipsMsg');
	if(tipsMsg.html()){ 
		decRule.animate({ right: '0px'}, 500, function(){
            $('.decRuleBtn').addClass('decRuleBtnActive');
        });
	}
    decRuleBtn.on('click', function(){
        if($(this).hasClass('decRuleBtnActive')){
            $('.decRuleBtn').removeClass('decRuleBtnActive');
            decRule.animate({ right: '-405px'}, 500);
        }else{
            decRule.animate({ right: '0px'}, 500, function(){
                $('.decRuleBtn').addClass('decRuleBtnActive');
            });
        }
    });
	datepicker.datetimepicker({ lang : 'ch', parentID: '.decRuleFlow', timepicker : false, format : 'Y-m-d', formatDate : 'Y-m-d' });
    formClear();
}
function countDown(){
    function wrapperNum(num){
        var arr = num.toString().split("");
        for(var i = 0; i < arr.length; i++){
            arr[i] = "<b>" + arr[i] + "</b>";
        }
        return arr.join("");
    }
    //对显示数字进行补位
    function fillDigits(num, digit){
        var resultNum;
        switch (digit){
            case 2: resultNum = (num < 10 ? ('0' + num) : num); return wrapperNum(resultNum);
            case 3: resultNum = (num < 10 ? ('00' + num):(num < 100 ? ('0' + num) : num)); return wrapperNum(resultNum);
            default : resultNum = num; return wrapperNum(resultNum);
        }
    }

    //计算差值 判断并显示倒计时
    function countdown(endDate){
        var str, endDiff, curDate;
        var oneDay = 24*60*60*1000,
            oneHour = 60*60*1000,
            oneMinute = 60*1000,
            oneSecond = 1000;
        var dayDiff, hourDiff, minuteDiff, secondDiff, millisecondDiff;
        curDate = new Date();
        endDiff = (endDate).getTime() - curDate.getTime();
        dayDiff = Math.floor(endDiff/oneDay);
        hourDiff = Math.floor((endDiff%oneDay)/oneHour);
        minuteDiff = Math.floor(((endDiff%oneDay)%oneHour)/oneMinute);
        secondDiff = Math.floor((((endDiff%oneDay)%oneHour)%oneMinute)/oneSecond);
        millisecondDiff = Math.floor((((endDiff%oneDay)%oneHour)%oneMinute)%oneSecond);
        if(dayDiff <= 0 && hourDiff <= 0 && minuteDiff <= 0 && secondDiff <= 0 && millisecondDiff <= 0){
            str = '<b>0</b><b>0</b><strong>天</strong><b>0</b><b>0</b><strong>时</strong><b>0</b><b>0</b><strong>分</strong>';
            clearInterval(countdownStart);
        }else{
            str = fillDigits(dayDiff, 2) + '<strong>天</strong>' + fillDigits(hourDiff, 2) + '<strong>时</strong>' + fillDigits(minuteDiff, 2) + '<strong>分</strong>';
        }
        $('.countDownTime').html(str);
    }
    var countdownStart, endDate = new Date('2015/1/20 00:00:00');
    countdown(endDate);
    countdownStart = setInterval(function(){ countdown(endDate)}, 1E3);
}
function topFold(){
    $('.timestamp').hide();
    setTimeout(function(){
        $('.goBtn').fadeOut('fast');
        $('.decTopConBg').animate({ height: '119px', overflow: 'hidden'}, 1E3);
        $('.decTop').addClass('decTopFold').animate({ height: '119px'}, 1E3, function(){
            $('.timestamp').fadeIn();
            $('.decTopConBg').fadeOut();
            runFlow();
        });
    }, 5E3);
}
$(function(){
    var s = setInterval(textScroll, 2E3);
    topFold();
    checkFlow();
    countDown();
    $.fn.eggFrenzy({
        container: '.goldenEggs',
        hasEgg: '.luckyDrawL .mask',
        noEgg: '.luckyDrawL .noStart',
        eggNum: '.luckyDrawL .eggNum',
        eggMoney: '.luckyDrawL .eggMoney',
        debug: true
    });
});
(function($){
    var Egg = function(options, element){
        this.options = options;
        this.element = $(element);
        this.init();
    };
    Egg.prototype = {
		debug: function() {
            var opts = this.options;
            if (opts.debug) {
                return window.console && console.log.call(console, arguments);
            }
        },
        beginAjax: function(){
            var $this = this;
            var opts = $this.options;
            $.ajax({
				 url: Routing.generate('jili_frontend_decemberactivity_geteggsinfo'),
				 type: 'post',
				 dataType: 'json',
				 success: function(eggData){
					 $this.debug('初始金蛋信息……', $.isEmptyObject(eggData));
					 if(!$.isEmptyObject(eggData)&&eggData.data === undefined) return false;
					 if($this.showEgg(eggData)){
						 $this.setEggInfo(eggData);
						 $this.addEgg(eggData);
						 $this.openStart(eggData);
					 }else{
						 $(opts.eggNum).html("0");
						 $(opts.eggMoney).html('10元');
					 }
				 },
				 error: function(){
					$this.debug('第一次请求失败……');
				 }
             });
        },
        init: function(){
            this.beginAjax();
        },
        showEgg: function(initData){
            var opts = this.options;
            if($.isEmptyObject(initData) || (initData.data.numOfEggs + initData.data.numOfConsolationEggs) <= 0){
				this.debug('金蛋数为空……');
                $(opts.hasEgg).hide();
                $(opts.noEgg).show();
				return false;
            }else{
                $(opts.hasEgg).show();
                $(opts.noEgg).hide();
				return true;
            }
        },
        setEggInfo: function(initData){
            $(this.options.eggNum).html((initData.data.numOfEggs + initData.data.numOfConsolationEggs));
            $(this.options.eggMoney).html(initData.data.lessForNextEgg + '元');
        },
        addEgg: function(initData){
            var $this = this;
            var opts = $this.options;
            var randNum;
            var imgArr = ["/images/december/static_egg.gif", "/images/december/shaking_egg7.gif", "/images/december/shaking_egg12.gif"];
            var eggWrapper = '<li><div><img src="#" width="110" height="138"/></div><span>我要砸蛋</span></li>';
            for(var i = 0; i< initData.data.numOfEggs; i++){
                randNum = Math.floor(Math.random()*(0-3) + 3);
                $(eggWrapper).find('img').attr("src", imgArr[randNum]).end().appendTo($(opts.container));
            }
            for(var j = 0; j< initData.data.numOfConsolationEggs; j++){
                randNum = Math.floor(Math.random()*(0-3) + 3);
                $(eggWrapper).find('img').attr("src", imgArr[randNum]).end().addClass('comfort').appendTo($(opts.container));
            }
        },
        getResult: function(initData, eggType){
            var $this = this;
            $.ajax({
				 url: Routing.generate('jili_frontend_decemberactivity_breakegg'),
				 type: 'post',
				 dataType: 'json',
				 data: "token=" + initData.token + "&eggType=" + eggType,
				 success: function(resultData){
					 //var resultData = { code: 1, msg: '', data: { token: '', points: 50}};
					 $this.debug('砸蛋结果……', resultData);
					 $this.showResult(resultData);
				 },
				 error: function(){
				 $this.debug('第二次请求结果失败……');
				 }
             });
            /*var resultData = { code: 1, msg: '', data: { token: '', points: 50}};
            $this.debug('砸蛋结果……', resultData);
            $this.showResult(resultData);*/
        },
        showResult: function(resultData){
            this.debug('展示砸蛋结果……');
        },
        openGif: function(index){
            this.debug('砸蛋效果……');
            $(this.options.container).find('li img').eq(index).attr("src", "/images/december/egg.png");
        },
        openEgg: function(initData){
            var $this = this;
            var opts = $this.options;
            var eggType = 0;
            $this.debug('进入砸蛋程序');
            $(opts.container).find('li').each(function(index, e){
                $(this).on('click', function(){
                    $this.debug('开始执行砸蛋');
                    $this.openGif(index);
                    if($(this).hasClass('comfort')){
                        eggType = 1;
                        initData.data.numOfConsolationEggs = initData.data.numOfConsolationEggs -1;
                    }else{
                        initData.data.numOfEggs = initData.data.numOfEggs -1;
                    }
                    $this.getResult(initData, eggType);
                    $this.setEggInfo(initData);
                });
            });
        },
        openStart: function(initData){
            if(true){
                this.openEgg(initData);
            }else{
                $(this.options.container).on('click', function(){
                    var $div = $('<div></div>');
                    $div.addClass('eggTips').html('还不可以砸蛋哦！').appendTo($('.luckyDrawL')).fadeIn(1000, function() {
                        $(this).fadeOut(3000);
                    });
                });
            }
        }
    }
    $.eggFrenzy = function(options, element){
        options = $.extend(true, {}, $.eggFrenzy.defaults, options);
        $.data(element, 'eggFrenzy', new Egg(options, element));
        return element;
    };
    $.eggFrenzy.defaults = {
        container: '.goldenEggs',
        hasEgg: '.luckyDrawL .mask',
        noEgg: '.luckyDrawL .noStart',
        eggNum: '.luckyDrawL .eggNum',
        eggMoney: '.luckyDrawL .eggMoney',
        debug: true
    };
    $.fn.eggFrenzy = function(options){
        return $.eggFrenzy(options, this);
    };
})(jQuery);