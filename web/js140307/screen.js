$(document).ready(function(){
  $(".unfoldBtn").click(function(){
	if($(this).hasClass("already")){
		$(this).removeClass("already");
		if($(".screen dd").hasClass("h28")){
			$(".screen dd").css("height","28px")
		}else if($(".screen dd").hasClass("h56")){
			$(".screen dd").css("height","56px")
		}else{
			$(".screen dd").css("height","28px")
		}
	}else{
		$(this).addClass("already");
		$(".screen dd").css("height","auto")
	}
  })
});