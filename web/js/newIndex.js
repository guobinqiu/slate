$(document).ready(function() {

//indexBanner

	$("#indexBanner a").hover(function() {
		$("#indexBanner a").css("opacity",".8");
		$(this).css("opacity","1");
	},function() {
		$("#indexBanner a").css("opacity","1");
 	})

//marketActivity

	var marketActivityPage = 1
	var marketActivitySliderTime = 500
	var marketActivityNextTime = 10000
	
	$("#marketActivity ul").width($("#marketActivity ul li").length * $("#marketActivity ul li").width())
	
    var marketActivitySlider = function(){
		$("#marketActivity ul").stop(true,false).animate({left:-$("#marketActivity ul li").width()*2*(marketActivityPage-1)},marketActivitySliderTime);
	}
    var marketActivityNext = function(){
		if(marketActivityPage >= $("#marketActivity ul li").length / 2){
			marketActivityPage = 1;	
		}
		else{
			marketActivityPage ++;
		}
		marketActivitySlider();
	}
	var marketActivityPrev = function(){
		if(marketActivityPage <= 1){
			marketActivityPage = $("#marketActivity ul li").length % 4 == 0 ? parseInt($("#marketActivity ul li").length / 2) : parseInt($("#marketActivity ul li").length / 2) + 1;	
		}
		else{
			marketActivityPage --;
		}
		marketActivitySlider();
	}
	$("#marketActivity .prev").click( function () {  
		marketActivityPrev()
		//clearInterval(marketActivitySetTime);
		//marketActivitySetTime = setInterval( marketActivityNext , marketActivityNextTime);
	});
	$("#marketActivity .next").click( function () {
		marketActivityNext()
		//clearInterval(marketActivitySetTime);
		//marketActivitySetTime = setInterval( marketActivityNext , marketActivityNextTime);
	});
	//var marketActivitySetTime = setInterval( marketActivityNext , marketActivityNextTime);
	

	$("#marketActivity li").hover(function() {
		$(this).children(".more").animate({bottom:0},200);
	},function() {
		$(this).children(".more").animate({bottom:-30},200);
 	})


//banner

	var bannerPage = 1
	var sliderTime = 500
	var nextTime = 3000
	$(".bannerNumber a").eq(bannerPage-1).addClass("hover")
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
	
	
//timeLine

	for(i=1; i <= $("#timeLine ul li").length ; i++){
		if(i%3 == 0){
			$("#timeLine ul li:eq(" + (i-1) + ")").addClass("rbn")
		}
	}
	var timeLineSliderTime = 500
	var timeLineNextTime = 5000
	
	var timeLineNext = function(){
		$("#timeLine ul").stop(true,false).animate({top:-83},timeLineSliderTime,function(){
			$("#timeLine ul li:lt(3)").clone().appendTo($("#timeLine ul"))
			$("#timeLine ul").css("top",0);
			$("#timeLine ul li:lt(3)").remove();
		});
	}
	
	var timeLineSetTime = setInterval( timeLineNext , timeLineNextTime);
	
	
//recommended
	
	var recommendedPage = 1
	var recommendedSliderTime = 500
	
	$("#recommended ul").width($("#recommended ul li").length * $("#recommended ul li").width())
	$("#recommended .number a").eq(recommendedPage-1).addClass("hover")
    var recommendedSlider = function(){
		$("#recommended ul").stop(true,false).animate({left:-$("#recommended ul li").width() * (recommendedPage-1)*4},recommendedSliderTime);
	}
	var recommendedNumber = function(){
	 	$("#recommended .number a").removeClass("hover").eq(recommendedPage-1).addClass("hover");
	}
	$("#recommended .number a").hover(function() {
		recommendedPage = $(this).text();
		recommendedSlider();
		recommendedNumber();
	})

	$("#recommended ul li").hover(function() {
		$(this).children(".more").fadeIn("fast");
	},function() {
		$(this).children(".more").fadeOut("fast");
 	})
	
		
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
	
//task
		
	$("#task .all").width($("#task ul").length * $("#task ul").width())
	$("#task ul li:last").addClass("end");
	var taskSliderTime = 500
	
	var taskNext = function(){
		$("#task .all").stop(true,false).animate({left:-$("#task ul").width()},newsSliderTime,function(){
			$("#task ul:first").clone().appendTo($("#task .all"))
			$("#task .all").css("left",0);
			$("#task ul:first").remove();
		});
	}
	var taskPrev = function(){
			$("#task ul:last").clone().prependTo($("#task .all"));
			$("#task .all").css("left",-207);
			$("#task ul:last").remove();
			$("#task .all").stop(true,false).animate({left:0},newsSliderTime,function(){
		});
	}
	$("#task .pageNumber a.next").click( function () {
		taskNext();
	})
	$("#task .pageNumber a.prev").click( function () {
		taskPrev();
	})

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
	
//mainHotMarket
	
	$("#mainHotMarket ul li:lt(5)").addClass("tbn")

});