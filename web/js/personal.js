$(document).ready(function() {
	
	$(".close").click(function(){
		$(this).parent().next(".content").hide();
		$(this).parent().children(".open").show();
		$(this).hide();
	})
	$(".open").click(function(){
		$(this).parent().next(".content").show();
		$(this).parent().children(".close").show();
		$(this).hide();
	})
	
});