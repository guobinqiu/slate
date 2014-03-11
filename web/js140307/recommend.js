$(document).ready(function(){
  $(".recommend ul").width(($(".recommend ul li").length + 1) * ($(".recommend ul li").width()+40));
  var recommendPage = 1
  var recommendPageMax = Math.ceil($(".recommend ul li").length / 3);
  var recommendBtnReset = function(){
	if(recommendPage <= 1 ){
		$(".recommend .prevBtn").hide();
		$(".recommend .nextBtn").show();
	}else if( recommendPage>= recommendPageMax){
		$(".recommend .prevBtn").show();
		$(".recommend .nextBtn").hide();
	}else{
		$(".recommend .prevBtn").show();
		$(".recommend .nextBtn").show();
	}
  }
  var recommendGoPage = function(){
	  $(".recommend ul").animate({ left: -(recommendPage-1)*($(".recommend ul li").width()+40) * 3}, 500);
  }
  $(".recommend .prevBtn").click(function(){
	if(recommendPage <= 1 ){}else{
		recommendPage = recommendPage - 1
  	}
	recommendGoPage();
	recommendBtnReset();
  })
  $(".recommend .nextBtn").click(function(){
	if(recommendPage >= recommendPageMax ){}else{
		recommendPage = recommendPage + 1
	}
	recommendGoPage();
	recommendBtnReset();
  })
  recommendBtnReset();
});