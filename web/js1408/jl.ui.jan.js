/**
 * Created by wangliting on 14-12-1.
 */

$(function(){
	$.ajax({
		url: Routing.generate('jili_api_monthactivity_gatheringtaobaoordercount'),
		type: 'get',
		dataType: 'json',
		success: function(orderData){
			console.log('初始订单信息……', $.isEmptyObject(orderData));
			if(!$.isEmptyObject(orderData)&&orderData.data === undefined){ 
				console.log('返回结果为空');
				return false;
			}
			$('.splitLvTxt').numScroll({num:orderData.data.total});
		},
		error: function(){
			console.log('请求失败……');
		}
	});
	$('.shoppingForm').formOp();
});
(function($){
    //页面加载完毕时数字滚动到位
	var defaults = {
		digitH : 33,
        num: 235,
        animateTimer: 2000
    };
	var numScro = function(obj, opts){
		var arrCur = opts.num.toString().split('');
		var needNum, arrNeed;
		if(opts.num<1000){
			needNum = parseInt(1000-opts.num);
		}else if(opts.num>1000&&opts.num<2000){
			needNum = parseInt(2000-opts.num);
			$('.splitLvImg').html('<img src="images/January/lv2.png" width="554" height="387" />');
			$(obj).find('strong').html('<img src="images/January/lv2Txt.png" width="104" height="28" />');
			$(obj).addClass('splitLv2Txt');
		}else if(opts.num>2000&&opts.num<3000){
			needNum = parseInt(3000-opts.num);
			$('.splitLvImg').html('<img src="images/January/lv3.png" width="614" height="387" />');
			$(obj).find('strong').html('<img src="images/January/lv3Txt.png" width="99" height="27" />');
			$(obj).addClass('splitLv3Txt');
		}else{
			needNum = 0;
			$('.splitLvImg').html('<img src="images/January/clearance.png" width="614" height="387" />');
			$(obj).hide();
		}
		arrNeed = needNum.toString().split('');
		for(var i = 0; i < arrNeed.length; i++){
			$('<b><span></span></b>').appendTo($(obj).find('.needNum')).find('span').animate({ top: '-' + (parseInt(arrNeed[i])*opts.digitH) + 'px'}, opts.animateTimer);
		}
		console.log('还需要的订单数是：' + needNum + '-------' + $('.splitLvTxt .needNum').html());
	}
	$.fn.numScroll = function(options){
		var options = $.extend(defaults, options);
		return this.each(function(){
			numScro(this, options);
		});
	}
})(jQuery);
(function($){
	var defaults = {};
	var clearForm = function(obj){
        var conInput = $(obj).find('.clearTxt'),
			inputTxt = $(obj).find('.defaultTxt');

        if(conInput.val() && conInput.val().length){ inputTxt.hide();}
        conInput.bind({
            focus: function(){
                inputTxt.hide();
                $(this).addClass('active');
            },
            blur: function(){
                if($(this).val() && $(this).val().length){
                    inputTxt.hide();
                }else{
                    inputTxt.show();
                }
                $(this).removeClass('active');
            }
        });
        inputTxt.on('click', function(){
			conInput.focus();
            $(this).hide();
            conInput.addClass('active');
        });
	};
	var valForm = function(obj){
		//console.log($(obj).find('#orderId').val());
		var inputVal = $(obj).find('#orderId').val();
		if(inputVal ==""){
			$(obj).find('.tips').html('*请输入您的订单号！');
			return false;
		}else{
			inputValTrim = inputVal.toString().replace(/\s+/g,"");
			//console.log('匹配结果：'+inputValTrim.match(/^\d{15}$/));
			if(!inputValTrim.match(/^\d{15}$/)) {
				$(obj).find('.tips').html('*请输入合法订单号！');
				return false;
			}else{
				$(obj).find('.tips').html('');
				return true;
			}
		}
	};
	$.fn.formOp = function(options){
		var options = $.extend(defaults, options);
		return this.each(function(){
			var _this = this;
			clearForm(_this);
			$(_this).find('.submitBtn').on('click', function(event){ 
				event.preventDefault();
				//console.log('验证结果'+valForm(_this));
				if(valForm(_this)){
					$(_this).submit();
				}
			});
		});
	}
})(jQuery);

























