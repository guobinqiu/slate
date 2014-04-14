$(document).ready(function(){
	$(".categorysFilter h4").click(function(){
	
		//$(".categorysFilter h4").parent("li").removeClass("open");
		if(!$(this).parent("li").hasClass("open") ){
			$(this).parent("li").addClass("open")
		}
		else{
			$(this).parent("li").removeClass("open")
		}
		
	})
});