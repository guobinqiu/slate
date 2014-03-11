$(document).ready(function(){
  $(".unfoldBtn").click(function(){
	if($(this).hasClass("already")){
		$(this).removeClass("already");
		$(".classifyShop dd").css("height","28px")
	}else{
		$(this).addClass("already");
		$(".classifyShop dd").css("height","auto")
	}
  })
});