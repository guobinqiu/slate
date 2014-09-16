$(document).ready(function() {
	
	$("#signInFrame ul li:lt(3)").addClass("tbn")
	for(i=1; i <= $("#signInFrame ul li").length ; i++){
		if(i%3 == 0){
			$("#signInFrame ul li:eq(" + (i-1) + ")").addClass("rbn")
		}
	}
	
	$("#signInFrame ul li a").hover(function(){
		$(this).children(".gray").show();
		$(this).children(".goTo").show();
	},function(){
		if($(this).parent().hasClass("finish")){
		}else{
			$(this).children(".gray").hide();
			$(this).children(".goTo").hide();	
		}
	});
	
	$("#signInFrame ul li a").click(function(){
		$(this).parent().addClass("finish")
	})
	
});
$(document).ready(function(){
		$('span.autoSignIn a').toggle(function(){
			$('.signInOptions p').slideDown('fast');
		}, function(){
			$('.signInOptions p').slideUp('fast');
		});
		$('.signInOptions span').on('click', function(){
//			var index = $('.signInOptions span').index(this);
//			$('.signInOptions span').removeClass('active').eq(index).addClass('active');
			if($(this).hasClass('autoSignIn')){
				$('#signInFrame .mask').css('display', 'block');
				$('#signInFrame .signInAutoFrame').css('display', 'block');
			}else{
				$('#signInFrame .mask').css('display', 'block');
				$('#signInFrame .signInManualFrame').css('display', 'block');
			}
		});
		$('.btns a').hover(function(){
			$(this).addClass('active');
		}, function(){
			$(this).removeClass('active');
		});
		$('.btns a').on('click', function(){
			if($(this).hasClass('confirm')){
				$('#signInFrame').css('display', 'none');
				$('.blackBg').css('display', 'none');
			}else{
				$('#signInFrame .mask').css('display', 'none');
				$('#signInFrame .signInConfirmFrame').css('display', 'none');
			}
		});
	});