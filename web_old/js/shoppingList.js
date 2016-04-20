$(document).ready(function() {
	$(".s_box").hover(
		function () {
    		$(this).children(".hover").show();
  		},
  		function () {
   			$(this).children(".hover").hide();
  		}
	);
});