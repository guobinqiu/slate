// JavaScript Document
(function($){
	$(function(){
		var resizeFooter = function(){
			if($(window).height() > $("body").height()){
				$("footer").css("position","fixed");
			}else{
				$("footer").css("position","");
			} 	
		}
		var formClear = function(){
			var inputBox = $('.askQuestionDetail p');
			inputBox.each(function(){
				var _self = $(this);
				var conInput = _self.find('.clearTxt');
				var inputTxt = _self.find('.defaultTxt');
		
				if(conInput.val() && conInput.val().length){
					inputTxt.hide();
				}
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
			});
		}
		var sendContent = function(){
			var con = $("#fbc").val().toString().replace(/\s+/g,"");
			var email = $("#email").val().toString().replace(/\s+/g,"");
			var tips = $('.tips');
			if(con == "" || email == ""){ tips.text('*请输入您的问题和联系方式'); return; }
			$.ajax({  
				url: Routing.generate("_default_contact", {
					"content": con,
					"email": email
				}),
				type: "POST",
				success:function(data){
					switch(data){
						case "1": tips.text('*请输入您的问题'); break;
						case "2": tips.text('*请输入您的联系方式'); break;
						case "3": tips.text('*您的联系方式不正确'); break;
						case "4": tips.text('*系统出错，邮件发送失败'); break;
						default: $('.askQuestionDetail').html("非常感谢，我们已经收到了您的问题！之后我们将以邮件形式与您取得联系，并帮助您解决问题。");  break;
					}
				}
			  });
		}
		var tabs = function(args){
			return $.each(args, function(i, e){
				$(e.title).on('click', function(){
					var index = $(e.title).index(this);
					resizeFooter();
					if(e.toggle){
						if($(e.title).parent().eq(index).hasClass('active')){
							$(e.title).parent().eq(index).removeClass('active');
							$(e.con).eq(index).slideToggle();
						}else{
							$(e.con).slideUp().eq(index).slideDown();
							$(e.title).parent().removeClass('active').eq(index).addClass('active');
						}
					}else{
						$(e.title).removeClass('active').eq(index).addClass('active');
						$(e.con).hide().eq(index).show();
						if($(e.title).eq(index).hasClass('more')){
							$('.newGuide').hide();
							$(e.con).parent().show();
							$(e.con).parent().find('h3').html($('.newGuide dt').eq(index).text());
						}else{
							$('.newGuideDetail').hide();
							$(e.con).parent().find('h2').text($(e.title).eq(index).text());
						}
					}
				});
			});
		};
		tabs([{title: '.helpNavs li', con: '.helpR>ul', toggle: false},{title: '.newGuide span.more', con: '.newGuideDetail>ul', toggle: false}, {title: '.questions li .question', con: '.questions li p', toggle: true}, {title: '.askQuestion h2', con: '.askQuestionDetail', toggle: true}]);
		var questions = $('.questions li');
		questions.hover(function(){
			var index = questions.index(this);
			questions.removeClass('on').eq(index).addClass('on');
		}, function(){
			$(this).removeClass('on');
		});
		formClear();
		$('.submitQuestion').on('click', function(){
			sendContent();
		});
	});
})(jQuery);