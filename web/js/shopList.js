$(document).ready(function() {
	$(".shopListUl li").hover(
		function () {
			$(this).children(".hover").show();
		},
		function () {
			$(this).children(".hover").hide();
		}
	);
});
