$(function() {
    // 签到button
    
    // checkinBtnClick();

    // 自动签到设置button
	//autoCheckinConfigDomClick();
    setAutoCheckinDomClicked();
    setManualCheckinDomClicked();
});
var checkinBtnClick = function(){
    // ajax request
    //  
};

	//  设置手工签到的Ajax
var setManualCheckinDomClicked = function() {
	$("span#set_manualcheckin").each(function() {
		var el = $(this);
        if( el.hasClass('active') ) {
            return false;
        }
		el.click(function() {
            if( false == confirm("确认 " + el.text() + " ? ")) {

                return false;
            }


			if (el.hasClass("delete")) {
				var method = "DELETE";
				var url = Routing.generate('autocheckinconfig_delete') ;
			} else {
				return false;
			};

			// return false;
			$.ajax({
				url: url,
				type: method,
				success: function(data) {
                    if(rsp.code == 200 ) {
                        jili_autocheckin.is_set = false;
                    }
					autoCheckinDomUpdate(el);
					return false;
				}
			});

			return false;
		});
	});
};
//  设置自动签到的Ajax
var setAutoCheckinDomClicked = function() {
	$("span#set_autocheckin").each(function() {
		var el = $(this);
		el.click(function() {
            if( false == confirm("确认 " + el.text() + " ? ")) {
                return false;
            }
			// var url = el.attr('href');
			if ( "undefined" == typeof jili_autocheckin.is_set ) {
				var method = "PUT";
				var url = Routing.generate('autocheckinconfig_create') ;
			} else if (jili_autocheckin.is_set = false ) {
				var method = "POST";
				var url = Routing.generate('autocheckinconfig_update') ;
			} else {
				return false;
			};

			// return false;
			$.ajax({
				url: url,
				type: method,
				success: function(rsp) {

                    if(rsp.code == 200 ) {
                        jili_autocheckin.is_set = true;
                    }

					autoCheckinDomUpdate(el);
					return false;
				}
			});
			return false;
		});
	});
};

// 更换手工自动签到按键的样式 
var autoCheckinDomUpdate = function() {
    var $e1 = $("div.signInOptions span.active");
    var $e2  =  $("div.signInOptions span:not(.active)");
    $e2.addClass("active");
    $e1.removeClass("active");
    return false;
};
