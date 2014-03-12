$(document).ready(function() {
	$("a.unfoldBtn").click(function() {
		var $ele = $(this);
		if ($ele.hasClass("already")) {
			$(".classify dd").css("height", "28px")
		} else {
			$ele.addClass("already");
			$(".classify dd").css("height", "auto")
		}
	})
	$(".shopListUl li").hover(
	function() {
		$(this).children(".hover").show();
	},
	function() {
		$(this).children(".hover").hide();
	});
});

