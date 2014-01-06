$(document).ready(function() {
	
	$(".personal section .content").show();
	$(".personal h3 a").addClass("close");
	
	$(".personal h3 a").toggle(
	  function () {
	  	$(this).removeClass("close");
	    $(this).addClass("open");
	    $(this).parent().next(".content").hide();
		$(this).parent().children(".close").show();
	  },
	  function () {
		$(this).removeClass("open");
	    $(this).addClass("close");
	    $(this).parent().next(".content").show();
		$(this).parent().children(".open").show();
	  }
	);
	
	
});