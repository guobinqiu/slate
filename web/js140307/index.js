$(document).ready(function(){
	
	//indexBanner
	$("#indexBanner a").hover(function() {
		$("#indexBanner a").css("opacity",".8");
		$(this).css("opacity","1");
	},function() {
		$("#indexBanner a").css("opacity","1");
	})

	//banner
	var bannerPage = 1
	var sliderTime = 500
	var nextTime = 3000
	var bannerLength = $(".bannerImg li").length;
	for(i=1;i<=bannerLength;i++){
		$(".bannerNumber").append("<a>"+ i +"</a>");
	}
	$(".bannerNumber a").eq(bannerPage-1).addClass("hover");
	var slider = function(){
		$(".bannerImg").stop(true,false).animate({top:-$(".bannerImg li").height()*(bannerPage-1)},sliderTime);
	}
	var number = function(){
		$(".bannerNumber a").removeClass("hover").eq(bannerPage-1).addClass("hover");
	}
	var next = function(){
		if(bannerPage >= $(".bannerImg li").length){
		bannerPage = 1;
	}
	else{
		bannerPage ++;
	}
	slider();
	number();
	}
	$(".bannerNumber a").hover(function() {
		bannerPage = $(this).text();
		slider();
		number();
		clearInterval(SetTime);
	},function() {
		SetTime = setInterval( next , nextTime);
	})
	var SetTime = setInterval( next , nextTime); 
	
});