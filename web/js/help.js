$(document).ready(function() {
	$(window).resize(function() {
		resizeFooter()
	});
	var resizeFooter = function() {
		if ($(window).height() > $("body").height()) {
			$("footer").css("position", "fixed");
		} else {
			$("footer").css("position", "absolute");
		}
	}

	$(".tabs-nav li").each(function(i) {
		$(this).children("a").click(function() {
			$(".tabs-nav li").removeClass("hover");
			$(this).parent().addClass("hover");

			// for (l = 0; l <= $(".tabs-nav li").length; l++) {
				goToHelp(i)
				// }
			})
	})
	var goToHelp = function(i) {
		//$("#helpMain").load("help/help" + (i + 1) + ".html", {
			$("#helpMain").load("https://192.168.1.235/jili-amy/web/help/help" + (i + 1) + ".html", { 
			limit : 25
		}, function() {
			$("#helpMain ul").fadeIn("normal");
			resizeFooter();
		});
	}

	goToHelp(0)

	$(window).scroll(function() {
		// console.log($(window).scrollTop())
			if ($(window).scrollTop() > 100) {
				$(".topBtn").css("opacity", "1")
			} else {
				$(".topBtn").css("opacity", "0")
			}
		});

	$(".topBtn a").click(function() {
		if (jQuery.browser.safari || jQuery.browser.chrome) {
			$("body").animate( {
				scrollTop : 0
			}, "slow");
		} else {
			$("html").animate( {
				scrollTop : 0
			}, "slow");
		}
		return false;
	});

	$(".qaBtn").toggle(function() {
		$(this).addClass("hover")
		$(".qaFrame").show();
	}, function() {
		$(this).removeClass("hover")
		$(".qaFrame").hide();
	});

});