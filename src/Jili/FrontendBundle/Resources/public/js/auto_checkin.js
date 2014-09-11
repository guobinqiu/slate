$(function() {
	autoCheckinConfigDomClick();
    if(jili_autocheckin.is_set ) {
        initFrame();
    }
});

var initFrame = function() {
	ifrm = document.createElement('iframe');
	ifrm.setAttribute('id', 'buffer');
	ifrm.setAttribute('name', 'buffer');
	ifrm.setAttribute('frameborder', '0');
	//      ifrm.setAttribute('style', 'display:none;overflow:hidden;');
	document.body.appendChild(ifrm);
    return ifrm;
}
// 
var autoCheckinConfigDomClick = function() {

	// Demo 设置自动签到的Ajax
	$(".set-autocheckin").each(function() {
		var el = $(this);
		el.click(function() {
            if( false == confirm("确认 " + el.text() + " ? ")) {
                return false;
            }
			// var url = el.attr('href');
			if (el.hasClass("create")) {
				var method = "PUT";
				var url = jili_urls.autocheckinconfig.create;
			} else if (el.hasClass("update")) {
				var method = "POST";
				var url = jili_urls.autocheckinconfig.update;
			} else if (el.hasClass("delete")) {
				var method = "DELETE";
				var url = jili_urls.autocheckinconfig.delete;
			} else {
				return false;
			};

			// return false;
			$.ajax({
				url: url,
				type: method,
				success: function(data) {
					//  console.log(data);
					autoCheckinDomUpdate(el);
					// update the class.
					return false;
				}
			});
			return false;
		});
	});
};

// Demo更换af自动签到按键的内容 
var autoCheckinDomUpdate = function(el) {
	if (el.hasClass("create") || el.hasClass("update")) {

		el.text("Quit Auto-Checkin");
		el.removeClass("update");
		el.removeClass("create");
		el.addClass("delete");

	} else if (el.hasClass("delete")) {

		el.text("Update Auto-Checkin");
		el.removeClass("delete");
		el.addClass("update");
	}
	return false;
}

