$(document).ready(function() {
	var page = 1
	var sliderTime = 500
	var nextTime = 3000
    var slider = function(){
		$(".bannerImg").stop(true,false).animate({top:-$(".bannerImg li").height()*(page-1)},sliderTime);
	}
	var number = function(){
	 	$(".bannerNumber a").removeClass("hover").eq(page-1).addClass("hover");
	}
    var next = function(){
		if(page >= $(".bannerImg li").length){
			page = 1;	
		}
		else{
			page ++;
		}
		slider();
		number();
	}
	$(".bannerNumber a").hover(function() {
		page = $(this).text();
		slider();
		number();
		clearInterval(SetTime);
	},function() {
		SetTime = setInterval( next , nextTime);
 	})
	var SetTime = setInterval( next , nextTime);
	
});