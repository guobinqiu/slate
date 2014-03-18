$(document).ready(function() {
	 $(".shopList .unfoldBtn").click(function(){
	if($(this).hasClass("already")){
		$(this).removeClass("already");
		$(".classify dd").css("height","56px")
	}else{
		$(this).addClass("already");
		$(".classify dd").css("height","auto")
	}
  })

//	$(".shopListUl li").hover(
//		function () {
//			$(this).children(".hover").show();
//		},
//		function () {
//			$(this).children(".hover").hide();
//		}
//	);
});
