$(document).ready(function() {
	if($(".tags").height()>32){
		$(".tags").height(32);
		$(".unfoldBtn").show();
	}else{
		$(".unfoldBtn").hide();
	}
	if($(".btags").height()>32){
		$(".btags").height(32);
		$(".bunfoldBtn").show();
	}else{
		$(".bunfoldBtn").hide();
	}

	$("ul.activityAll li").hover(function() {
		$(this).children(".more").animate({bottom:0},200);
	},function() {
		$(this).children(".more").animate({bottom:-60},200);
 	})
	
	$("div.classify-nav a.unfoldBtn").toggle(function() {
		$(this).text("收缩")
		$(this).parent().css("height","auto")
	},function() {
		$(this).text("展开")
		$(this).parent().css("height","28px")
 	})

 	$("div.classify-nav a.bunfoldBtn").toggle(function() {
		$(this).text("收缩")
		$(this).parent().css("height","auto")
	},function() {
		$(this).text("展开")
		$(this).parent().css("height","28px")
 	})
	
	$(window).scroll( function() {
	//console.log($(window).scrollTop())
		if($(window).scrollTop() > 100){
			$(".topBtn").css("opacity","1")
		}else{
			$(".topBtn").css("opacity","0")
		}
	});
	
	$(".topBtn a").click(function(){
		if(jQuery.browser.safari || jQuery.browser.chrome ) {
			$("body").animate({scrollTop: 0},"slow");
		}else{
			$("html").animate({scrollTop: 0},"slow");
		}return false;
	}); 

})
