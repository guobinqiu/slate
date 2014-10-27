$(function() {
	CheckinModule.doAutoCheckin( );
});

var CheckinModule = CheckinModule || {};
// 开始自动签到。 
CheckinModule.doAutoCheckin = function( ) {
	var jili_autocheckin = CheckinModule.jili_autocheckin; 

	// 取当前的autocheckin 是否有设置。
	$.ajax({
		url: Routing.generate('autocheckinconfig_get'),
		post: "GET",
		success: function(rsp) {
			if (rsp.code == 200) {
				jili_autocheckin.is_set.init(rsp.data.flag_data);
			};
			if (typeof jili_autocheckin != "undefined" && typeof jili_autocheckin.is_set != "undefined" && jili_autocheckin.is_set.get() == true) {
				// 开始自动签到
				CheckinModule.auto_checkin.before_start();

				$('#signInFrame .close_checkin').hide();
				$("#signInFrame").show();
				$(".blackBg").show();
				$("#signInFrame .signInOptions span").removeClass('active');
				$("#signInFrame .signInOptions span.autoSignIn").addClass('active');

				CheckinModule.auto_checkin.start({
					advertiserments: jili_autocheckin.advertiserments,
					checkin_point: jili_autocheckin.checkin_point,
					urls: urls
				});
			} else {
				$("#signInFrame").hide();
			}
			return false;
		}
	});
};

CheckinModule.auto_checkin = function() {
	var index = 0;
	var ads = {}; // 签到商家 refer to  jili_autocheckin.advertiserments
	var count_of_ads = 0;
	var pts = 0; //  签到积分
	var urls = {};
	var buffer = null;
	var initFrame = function() {
		if ("object" == typeof window.frames.buffer) {
			buffer = window.frames.buffer;
			return false;
		}
		ifrm = document.createElement('iframe');
		ifrm.setAttribute('id', 'buffer');
		ifrm.setAttribute('name', 'buffer');
		ifrm.setAttribute('frameborder', '0');
		document.body.appendChild(ifrm);
		buffer = window.frames.buffer;
		return false;
	};

	var after_finished = function() {
//        $("#points").text(parseInt($("#points").text()) + parseInt(CheckinModule.jili_autocheckin.checkin_point));
		$("p.signInAuto").text("恭喜您签到成功");
        CheckinModule.afterFinish();
        return false;
	};
	var next = function() {
		if (index >= count_of_ads) {
			after_finished();
			return false;
		}
		goto(index);
		index++;
	};

	var goto = function(i) {
		var cid = ads[i].cid; 
		var aid = ads[i].id;
		var points = pts;

		$.ajax({
			// 记录商家的access: _advertiserment_click
			url: Routing.generate("_advertiserment_click", {
				"id": aid
			}),
			post: "GET",
			success: function(data) {
				if (data == 1) {
					$.ajax({
						//_checkin_issetClick
						url: Routing.generate("_checkin_issetClick", {
							"cid": cid
						}),
						post: "GET",
						success: function(data) {
							if (data == 1) {
								// 未签到过cid
								$.ajax({
									//_checkin_clickInsert
									url: Routing.generate("_checkin_clickInsert", {
										"cid": cid,
										"aid": aid
									}),
									post: "GET",
									success: function(data) {
										if (typeof(JSON) == 'undefined') {
											obj = eval("(" + data + ")");
										} else {
											obj = JSON.parse(data);
										}
										// 打开商家，_checkin_location
                                        var target = Routing.generate("_checkin_location", {
                                             "aid": aid,
                                             "type": 1
                                         });
										buffer.location.href = target;
										$("div.signInManual li:eq(" + i + ") a").find(".gray").show();
										$("div.signInManual li:eq(" + i + ")").addClass("finish");

										// update the user's pts div
										if (obj.code == 1) {
//                                            $("#points").text(parseInt(obj.point) + parseInt(points));
                                        }
                                        
									}
								});
							} else {
								// 己签到过cid
							}
						}
					});

				} else {
					//console.log("记录商家的access 0 " + ads[i].title);
				}
			}
		});
		return false;
	};
	return {
		start: function(params) {
			ads = params.advertiserments;
			count_of_ads = ads.length;
			pts = params.checkin_point;
			urls = params.urls;
			if (index == 0) {
				initFrame();
				document.getElementsByTagName("iframe")[0].onload = function() {
					next();
					return false;
				};
				goto(index);
				index++;
			} else {
				//				alert(' already start');
			}
		},
		before_start: function() {
			$("p.signInAuto").text("系统帮您自动签到中……，请稍后");
		},
	}
} ();

