$(document).ready(function() {
	getCenter("#signInFrame");
	getCenter("#confirmAutoFrame");
	$("#confirmAutoFrame").hide();
	$.ajax({
		url: Routing.generate("_checkin_clickCount"),
		post: "GET",
		success: function(data) {
			if (data < 3) {
				var times = parseInt(3 - parseInt(data));
				$("#remain").text(times);
			} else {
				$("#remain").text(0);
				aremove();
			}
		}
	});

	//关闭签到
	$(".close_checkin").click(function() {
		$(".blackBg").hide();
		$("#signInFrame").hide();
	});
});

function getCenter(ele) {
	var windowWidth = document.documentElement.clientWidth;
	var windowHeight = document.documentElement.clientHeight;
	var popupHeight = $(ele).height();
	var popupWidth = $(ele).width();
	$(ele).css({
		"position": "absolute",
		"top": windowHeight / 2 - popupHeight / 2,
		"left": windowWidth / 2 - popupWidth / 2
	});
}
var CheckinModule =  CheckinModule || {};
CheckinModule.jili_autocheckin = CheckinModule.jili_autocheckin || {};
CheckinModule.afterFinish = function() {
        // update confirmPoint
        $("#points").text(parseInt($("#points").text()) + parseInt(CheckinModule.jili_autocheckin.checkin_point));

        $("li #task_checkin_mark").removeClass("mark").addClass("hasMark");

		$("#signTxt").text("已签到");
        $("#mysign").css("background", "#ccc").unbind("click", signs);
        $("#mysign").removeAttr("onclick");
        $("#mysign").attr('onMouseOver', null);


}

CheckinModule.autoCheckinResultChecker = function() {
	var status = 0;
	return function() {
		if (status == 0) {
            status = 1;
            if(! $("#mysign").hasClass("onprogress")) {
                $("#mysign").addClass("onprogress"); 
            } 
			$.ajax({
				url: Routing.generate("_checkin_userCheckIn"),
				type: 'GET'
			}).success(function(rsp) {
				if (rsp.statusCode == 200) {
                    if( rsp.userCheckin == 0 && "undefined" != typeof CheckinModule.autoCheckinCheckerId) {
                        clearInterval(CheckinModule.autoCheckinCheckerId);
                        $("#mysign").removeClass("onprogress"); 
                        CheckinModule.afterFinish();
                    }
				}
			}).done(function(){
                status =0 ;
            });
		}
		return false;
	}
} ();

// 点击签到
var signs = function() {
	var jili_autocheckin = CheckinModule.jili_autocheckin;
    // 是否已经签到?
    // 是否正在签到?
    if($("#mysign").hasClass("onprogress")) {
        if( "undefined" == typeof CheckinModule.autoCheckinCheckerId) {
            CheckinModule.autoCheckinCheckerId  = setInterval(CheckinModule.autoCheckinResultChecker, 4000);
        } else {
            clearInterval(CheckinModule.autoCheckinCheckerId);
            CheckinModule.autoCheckinCheckerId  = setInterval(CheckinModule.autoCheckinResultChecker, 4000);
        }
        return false;
    } 
    
	// 取当前的autocheckin 是否有设置。
	$.ajax({
		url: Routing.generate('autocheckinconfig_get'),
		post: "GET",
		success: function(rsp) {
			if (rsp.code == 200) {
                jili_autocheckin.is_set.init( rsp.data.flag_data);
            } else if (rsp.code == 404) {
                jili_autocheckin.is_set.init( undefined );
			};
			//如果没有设置手工，则显示手工签到
			if (typeof jili_autocheckin == "undefined" || typeof jili_autocheckin.is_set == "undefined" || jili_autocheckin.is_set.get() == false || typeof jili_autocheckin.is_set.get() == "undefined") {
				$("#signInFrame").show();
				$(".blackBg").show();
			} else {
				// redirect to the new page.
				$("#confirmAutoFrame").show();
				$(".blackBg").show();
			}
		}
	});
	return false;
};

function aremove() {
	$("#sign").removeClass('signIn');
	$("#sign").addClass('signIn close');
	$("#sign").removeAttr("onclick");
	$("#sign").attr('onMouseOver', null);
}

function goto(cid, aid, points) {
	var w = window.open("", "_blank");
	$.ajax({
		// 记录商家的access: _advertiserment_click
		url: Routing.generate("_advertiserment_click", {
			"id": aid
		}),
		post: "GET",
		success: function(data) {
			if (data == 1) {
				// 是否已经签到过cid
				$.ajax({
					//_checkin_issetClick
					url: Routing.generate("_checkin_issetClick", {
						"cid": cid
					}),
					post: "GET",
					success: function(data) {
						if (data == 1) {
							var nowTimes = parseInt(parseInt($("#remain").text()) - 1);
							if (nowTimes <= 0) {
								$("#signInFrame h5").html("恭喜您签到成功，已获得<font color='#E94C1B'>" + points + "</font>米粒！");
                                CheckinModule.afterFinish();
							} else {
								$("#remain").text(nowTimes);
							}

						}
						$(".image" + cid).removeAttr("onclick");
						$(".goTo " + cid).removeAttr("onclick");
						// 记录签到商家数，发米粒 .
						$.ajax({
							//_checkin_clickInsert
							url: Routing.generate("_checkin_clickInsert", {
								"cid": cid,
								"aid": aid
							}),
							post: "GET",
							success: function(data) {
								var points = $("#points").text();
								var obj;
								if (typeof(JSON) == 'undefined') {
									obj = eval("(" + data + ")");
								} else {
									obj = JSON.parse(data);
								}

								// 打开商家，_checkin_location
								w.location.href = Routing.generate("_checkin_location", {
									"aid": aid,
									"type": 1
								});
								if (obj.code == 1) {
									aremove();
									$("#points").text(parseInt(obj.point) + parseInt(points));
								}
							}
						});
					}
				});
			} else if (data == 0) {
				window.location.href = Routing.generate("_user_login"); 
				w.close();
			} else {
				w.close();
			}
		},
		error: function() {
			w.close();
		}
	});
}

