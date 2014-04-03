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
	
	//news
	$("#news .all").width($("#news ul").length * $("#news ul").width())
	var newsPage = 1
	var newsSliderTime = 500
	var newsNext = function(){
		$("#news .all").stop(true,false).animate({left:-$("#news ul").width()},newsSliderTime,function(){
			$("#news ul:first").clone().appendTo($("#news .all"))
			$("#news .all").css("left",0);
			$("#news ul:first").remove();
			newsNumber();
		});
	}
	var newsPrev = function(){
		$("#news ul:last").clone().prependTo($("#news .all"));
		$("#news .all").css("left",-207);
		$("#news ul:last").remove();
		$("#news .all").stop(true,false).animate({left:0},newsSliderTime,function(){
			newsNumber();
		});
	}
	var newsNumber = function(){
		$("#news .pageNumber span").html(newsPage + "/" + $("#news ul").length)
	}
	$("#news .pageNumber a.next").click( function () {
		if(newsPage >= $("#news ul").length){
			newsPage = 1;
		}
		else{
			newsPage ++;
		}
		newsNext();
	})
	$("#news .pageNumber a.prev").click( function () {
		if(newsPage <= 1){
			newsPage = $("#news ul").length;
		}
		else{
			newsPage --;
		}
		newsPrev();
	})
	newsNumber();
	
	//ranking
	var rankingPage = 1
	var rankingSliderTime = 500
	var rankingSlider = function(){
		$("#ranking .all").stop(true,false).animate({left:-$("#ranking ul").width() * (rankingPage-1)},rankingSliderTime);
	}
	var rankingNumber = function(){
		$("#ranking .menu a").removeClass("hover").eq(rankingPage-1).addClass("hover");
	}
	$("#ranking .menu a").hover(function() {
		if($(this).attr("class") == "month"){
			rankingPage = 1
		}else if($(this).attr("class") == "year"){
			rankingPage = 2
		}
		rankingSlider();
		rankingNumber();
	},function() {
	})
	rankingNumber(); 
	
	//marketActivity
	$("#marketActivity .moveDiv").width($("#marketActivity li").length * $("#marketActivity li").width())
	var marketActivityPage = 1
	var marketActivitySliderTime = 500
	var marketActivityGo = function(){
		$("#marketActivity .moveDiv").stop(true,false).animate({left:-$("#marketActivity li").width() * (marketActivityPage-1)},marketActivitySliderTime,function(){
			
		});
	}
	$("#marketActivity a.nextBtn").click( function () {
		if(marketActivityPage >= $("#marketActivity li").length){
			marketActivityPage = 1;
		}
		else{
			marketActivityPage ++;
		}
		marketActivityGo();
	})
	$("#marketActivity a.prevBtn").click( function () {
		if(marketActivityPage <= 1){
			marketActivityPage = $("#marketActivity li").length;
		}
		else{
			marketActivityPage --;
		}
		marketActivityGo();
	})
	
	//task
	$("#task .all").width($("#task ul").width() * $("#task ul").length)
	var taskPage = 1
	var taskSliderTime = 500
	var taskSlider = function(){
		$("#task .all").stop(true,false).animate({left:-$("#task ul").width() * (taskPage-1)},taskSlider);
	}
	$("#task .menu a").click(function() {
		if($(this).attr("class") == "recommend"){
			taskPage = 1
		}else if($(this).attr("class") == "affirm"){
			taskPage = 2
		}
		else if($(this).attr("class") == "finish"){
			taskPage = 3
		}
		$("#task .menu a").removeClass("hover").eq(taskPage-1).addClass("hover")
		taskSlider();
	})
	
	//timeLine
	var timeLineSliderTime = 500
	var timeLineNextTime = 3000
	var timeLineNext = function(){
		$("#timeline ul").stop(true,false).animate({top:-40},timeLineSliderTime,function(){
			$("#timeline ul li:first").clone().appendTo($("#timeline ul"))
			$("#timeline ul").css("top",0);
			$("#timeline ul li:first").remove();
		});
	}
	var timeLineSetTime = setInterval( timeLineNext , timeLineNextTime); 
	
});