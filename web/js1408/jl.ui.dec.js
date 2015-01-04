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
			$('.timestamp img').attr('src', '/images/december/foldTxt02.png');
			$('.timestamp').unbind('click');
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
        debug: false
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
					 $this.debug('初始金蛋信息……', eggData);
					 if($.isEmptyObject(eggData)||undefined === eggData.data) return false;
					 if(eggData.data.isStart){
						$('.timestamp img').attr('src', '/images/december/foldTxt02.png');
						$('.timestamp').unbind('click');
					 }
					 if($this.showEgg(eggData)){
						 $this.setEggInfo(eggData);
						 $this.addEgg(eggData);
						 $this.openStart(eggData);
					 }else{
						 $(opts.eggNum).html('0');
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
			var opts = this.options;
			var allEggs = parseInt(initData.data.numOfEggs + initData.data.numOfConsolationEggs);
			if(allEggs>0){
				$(opts.eggNum).html((initData.data.numOfEggs + initData.data.numOfConsolationEggs));
				$(opts.eggMoney).html(initData.data.lessForNextEgg + '元');
			}else{
				$(opts.eggNum).html('0');
				$(opts.eggMoney).html('10元');
				$(opts.hasEgg).hide();
                $(opts.noEgg).show();
			}
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
        getResult: function(initData, eggType, index){
            var $this = this;
            $.ajax({
				 url: Routing.generate('jili_frontend_decemberactivity_breakegg'),
				 type: 'post',
				 dataType: 'json',
				 data: "token=" + initData.data.token + "&eggType=" + eggType,
				 success: function(resultData){
					$this.debug('砸蛋结果……', resultData);
					if($.isEmptyObject(resultData)||undefined === resultData.data){
						$this.debug('砸蛋结果为空');	
						return false;
					} 
					setTimeout(function(){
						$this.delEgg(index);
						$this.setEggInfo(initData);
					}, 5E3);
					$this.showResult(resultData);
				 },
				 error: function(){
				 	$this.debug('第二次请求结果失败……');
				 }
             });
        },
        showResult: function(resultData){
			this.debug('展示砸蛋结果……');
			if($('.eggResult').length>=1){
				$('.fixMask').remove();
				$('.eggResult').remove();
			}
            $('<div></div>').addClass('fixMask').appendTo($('body'));
			if(parseInt(resultData.data.points)>0){
				$('<div class="eggResult"><div><div class="resultTxt"></div><span class="close"></span><div><img src="/images/december/success.gif?t='+Math.random()+'" width="930" height="515"/></div></div></div>').appendTo($('body'));
				setTimeout(function(){
					switch(resultData.data.points.toString()){
						case "8888": $('.resultTxt').html('恭喜您中了一等奖，获得了<strong>'+resultData.data.points+'</strong>米粒!发财啦~').hide().fadeIn(1E3); break;
						case "888": $('.resultTxt').html('恭喜您中了二等奖，获得了<strong>'+resultData.data.points+'</strong>米粒!发财啦~').hide().fadeIn(1E3); break;
						case "88": $('.resultTxt').html('恭喜您中了三等奖，获得了<strong>'+resultData.data.points+'</strong>米粒!还不错哦~').hide().fadeIn(1E3); break;
						case "8": $('.resultTxt').html('恭喜您中了四等奖，<strong>'+resultData.data.points+'</strong>米粒入手咯~').hide().fadeIn(1E3); break;
						case "1": $('.resultTxt').html('恭喜您获得安慰奖，<strong>'+resultData.data.points+'</strong>米粒入手咯~').hide().fadeIn(1E3); break;
						default: break;
					}
				}, 2E3);
			}else{
				$('<div class="eggResult"><div><div class="resultTxt"></div><span class="close"></span><div><img src="/images/december/fail.gif?t='+Math.random()+'" width="930" height="515"/></div></div></div>').appendTo($('body'));
				setTimeout(function(){
					$('.resultTxt').text('太残忍了，竟然没有米粒！').hide().fadeIn(1E3);
				}, 2E3);
			}
			$('.eggResult .close').on('click', function(){
				$('.fixMask').remove();
				$('.eggResult').remove();
			});			 
        },
        delEgg: function(index){
			$(this.options.container).find('li').eq(index).remove();
        },
        openEgg: function(initData){
            var $this = this;
            var opts = $this.options;
            var eggType = 0;
            $this.debug('进入砸蛋程序');
			$(opts.container).find('li').on('mousedown', function(){
				$('.luckyDrawL .hammer').css({'background-image': 'url(/images/december/hammer0.png)', 'background-repeat': 'no-repeat', 'background-position': 'center'});
			});
			$(opts.container).find('li').on('mouseup', function(){
				$('.luckyDrawL .hammer').css({'background-image': 'url(/images/december/hammer.png)'});
				$this.debug('当前图片路径',$(this).find('img').attr('src'));
				if($(this).find('img').hasClass('active')){
					$(this).find('img').removeClass('active');
					var index = $(opts.container).find('li').index(this);
					$this.debug('开始执行砸蛋');
					if($(this).hasClass('comfort')){
						eggType = 1;
						initData.data.numOfConsolationEggs = initData.data.numOfConsolationEggs -1;
					}else{
						initData.data.numOfEggs = initData.data.numOfEggs -1;
					}
					$this.getResult(initData, eggType, index);
				}else{
					$(this).find('img').addClass('active').attr('src', '/images/december/crack_egg.gif');
					var $div = $('<div></div>');
                    $div.addClass('eggTips').html('咦？没砸开，再砸一下！').appendTo($('.luckyDrawL')).fadeIn(1000, function() {
                        $(this).fadeOut(3000);
                    });
				}
			});
			var leftW = $('.luckyDrawL .hammer').css('left').substr(0, $('.luckyDrawL .hammer').css('left').indexOf('px'));
			$(opts.container).find('li').on('mouseover', function(){
				var index = $(opts.container).find('li').index(this);
				$('.luckyDrawL .hammer').css({'top': Math.floor(index/4)*186 + 'px', 'left': ((Math.floor(index%4))*(110+28)+parseInt(leftW)) + 'px'});
			});
        },
        openStart: function(initData){
            if(initData.data.isStart){
				$(this.options.container).find('li span').addClass('active');
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