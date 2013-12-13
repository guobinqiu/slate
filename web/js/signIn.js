$(document).ready(function() {
	
	$("#signInFrame ul li:lt(3)").addClass("tbn")
	for(i=1; i <= $("#signInFrame ul li").length ; i++){
		if(i%3 == 0){
			$("#signInFrame ul li:eq(" + (i-1) + ")").addClass("rbn")
		}
	}
	
	$("#signInFrame ul li a").hover(function(){
		$(this).children(".gray").show();
		$(this).children(".goTo").show();
	},function(){
		if($(this).parent().hasClass("finish")){
		}else{
			$(this).children(".gray").hide();
			$(this).children(".goTo").hide();	
		}
	});
	
	$("#signInFrame ul li a").click(function(){
		$(this).parent().addClass("finish")
	})
	
});