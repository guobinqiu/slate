$(function() {
	// 选择自动、手工签到
	$('span.autoSignIn a').toggle(function() {
		$('.signInOptions p').slideDown('fast');
	},
	function() {
		$('.signInOptions p').slideUp('fast');
	});
	$('.signInOptions span').on('click', function() {
		var $el = $(this);
		if ($el.hasClass("active")) {
			return false;
		}
		if ($el.hasClass('autoSignIn')) {
			$('#signInFrame .mask').show();
			$('#signInFrame .signInAutoFrame').show();
		} else {
			$('#signInFrame .mask').show();
			$('#signInFrame .signInManualFrame').show();
		}
	});

	// 配置为的手工
	checkinConfirm({
		"container": 'div.signInConfirmFrame.signInManualFrame',
		"callback": setManualCheckin
	})();

	// 配置为自动
	checkinConfirm({
		"container": 'div.signInConfirmFrame.signInAutoFrame',
		"callback": setAutoCheckin
	})();

	checkinConfirm({
		"container": '#confirmAutoFrame',
		"callback": function() {
			var target_url = Routing.generate("_homepage", {
				"auto_checkin": 1
			},
			true);
			window.open(target_url, "_blank");
		}
	})();
});

// 更换手工自动签到按键的样式 
var autoCheckinDomUpdate = function() {
	var $e1 = $("div.signInOptions span.active");
	var $e2 = $("div.signInOptions span:not(.active)");
	$e2.addClass("active");
	$e1.removeClass("active");
	return false;
};

//  设置手工签到的Ajax
var setManualCheckin = function() {
	var el = $("span#set_manualcheckin");
	if (el.hasClass('active')) {
		return false;
	}
	var method = "DELETE";
	var url = Routing.generate('autocheckinconfig_delete');
	// return false;
	$.ajax({
		url: url,
		type: method,
		success: function(rsp) {
			if (rsp.code == 200) {
				jili_autocheckin.is_set = false;
				autoCheckinDomUpdate(el);
				console.log("todo: 显示手工签到成功，10秒后退出页面。");
			}
			return false;
		}
	});
	return false;
};

//  设置自动签到的Ajax
var setAutoCheckin = function() {
	var el = $("span#set_autocheckin");
	// var url = el.attr('href');
	if ("undefined" == typeof jili_autocheckin.is_set) {
		var method = "PUT";
		var url = Routing.generate('autocheckinconfig_create');
	} else if (jili_autocheckin.is_set == false) {
		var method = "POST";
		var url = Routing.generate('autocheckinconfig_update');
	} else {
		return false;
	};

	// return false;
	$.ajax({
		url: url,
		type: method,
		success: function(rsp) {
			if (rsp.code == 200) {
				jili_autocheckin.is_set = true;
				autoCheckinDomUpdate(el);
				console.log("todo: 显示自动签到设置成功，10秒后退出页面/开始自动签到。");
			}
			autoCheckinDomUpdate(el);
			return false;
		}
	});
	return false;
};

// arguments = { container: the class name, callback: the ajax call}
var checkinConfirm = function(arguments) {
    var args = arguments;
	return function() {
		console.log(args.container);
		var $btns = $(args.container).find(".btns a");
		$btns.on('click', function() {
			var el = $(this);
            console.log(args);

			if (el.parent().parent().hasClass('confirmAutoFrame')) {
				// 签到confirm
				$('#confirmAutoFrame').hide();
				$('.blackBg').hide();
				if ($(this).hasClass('confirm')) {
					args.callback();
				}
			} else {
				if (el.hasClass('confirm')) {
					$('#signInFrame').hide();
					$('.blackBg').hide();
					if (el.parent().parent().hasClass('signInAutoFrame')) {
						$('#sign').addClass('autoCheckinBtn');
						$('#sign').html('自动签到');
						args.callback();
					} else {
						$('#sign').removeClass('autoCheckinBtn');
						$('#sign').html('手动签到');
						args.callback();
					}
				} else {
					$('#signInFrame .mask').hide();
					$('#signInFrame .signInConfirmFrame').hide();
				}
			}
			return false;
		});

		$btns.hover(function() {
			$(this).addClass('active');
		},
		function() {
			$(this).removeClass('active');
		});
	};
};

