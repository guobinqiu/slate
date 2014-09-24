$(function() {
	// 签到button
	// checkinBtnClick();
	// 自动签到设置button
	//autoCheckinConfigDomClick();
	//setAutoCheckinDomClicked();
	//setManualCheckinDomClicked();
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
			$('#signInFrame .mask').css('display', 'block');
			$('#signInFrame .signInAutoFrame').css('display', 'block');
		} else {
			$('#signInFrame .mask').css('display', 'block');
			$('#signInFrame .signInManualFrame').css('display', 'block');
		}
	});

	//signInAutoFrame
	checkinConfirm({
		container: 'div.signInConfirmFrame[.signInManualFrame]',
		callback: setManualCheckin
	})();
	checkinConfirm({
		container: 'div.signInConfirmFrame[.signInAutoFrame]',
		callback: setAutoCheckin 
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
		success: function(data) {
			if (rsp.code == 200) {
				jili_autocheckin.is_set = false;
			}
			autoCheckinDomUpdate(el);
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
	} else if (jili_autocheckin.is_set = false) {
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
			}
			autoCheckinDomUpdate(el);
			return false;
		}
	});
	return false;
};

// arguments = { container: the class name, callback: the ajax call}
var checkinConfirm = function(arguments) {
    console.log(arguments);
    var container = arguments.container;
    var callback = arguments.callback;
	return function() {
		var $btns = $(container).find(".btns a");
		$btns.hover(function() {
			$(this).addClass('active');
		},
		function() {
			$(this).removeClass('active');
		});

		$btns.on('click', function() {
			var el = $(this);
			if ($(this).hasClass('confirm')) {
				$('#signInFrame').css('display', 'none');
				$('.blackBg').css('display', 'none');
				callback();
			} else {
				$('#signInFrame .mask').css('display', 'none');
				$('#signInFrame .signInConfirmFrame').css('display', 'none');
			}
		});
	};
};
