$(function() {
	//  开始自动签到
	//    console.log(jili_autocheckin);
	//    if (typeof jili_autocheckin != 'undefined') {
	//        auto_checkin.start({
	//            advertiserments: jili_autocheckin.advertiserments,
	//            checkin_point: jili_autocheckin.checkin_point,
	//            urls: urls
	//        });
	//    } else {
	//        console.log('Already checked in today');
	//    }
	doAutoCheckin();

	//    window.onbeforeunload = function(event){
	//        event = event || window.event;
	//        if(someCondition == someValue){
	//            return event.returnValue = "Are you sure you want to leave?  someCondition does not equal someValue..."
	//        };
	//    };
});

// 开始自动签到。 
var doAutoCheckin = function() {
	var jili_autocheckin = this.jili_autocheckin || {};
	// 取当前的autocheckin 是否有设置。
	$.ajax({
		url: Routing.generate('autocheckinconfig_get'),
		post: "GET",
		success: function(rsp) {
			if (rsp.code == 200) {
				jili_autocheckin.is_set = rsp.data.flag_data;
				console.log("after ajax get:");
				console.log(jili_autocheckin);
			};
			if (typeof jili_autocheckin != "undefined" && typeof jili_autocheckin.is_set != "undefined" && jili_autocheckin.is_set == true) {
				console.log('开始自动签到...');
				//<p class="signInAuto"></p>
				auto_checkin.before_start();
				//<!-- 系统帮您自动签到中……，请稍后-->
				$('#signInFrame .close_checkin').hide();
				$("#signInFrame").show();
				$(".blackBg").show();
				$("#signInFrame .signInOptions span").removeClass('active');
				$("#signInFrame .signInOptions span.autoSignIn").addClass('active');

				auto_checkin.start({
					advertiserments: jili_autocheckin.advertiserments,
					checkin_point: jili_autocheckin.checkin_point,
					urls: urls
				});
			} else {
				console.log(jili_autocheckin);
				console.log('当前配置为手工签到...');
				$("#signInFrame").hide();
			}

			return false;
		}
	});
};

var auto_checkin = function() {
	var index = 0;
	var ads = {}; // 签到商家 参数 jili_autocheckin.advertiserments
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
		//      ifrm.setAttribute('style', 'display:none;overflow:hidden;');
		document.body.appendChild(ifrm);
		console.log("init iframe");
		buffer = window.frames.buffer;
		return false;
	};

	var after_finished = function() {
		$("p.signInAuto").text("恭喜您签到成功");
        $("li #task_checkin_mark").removeClass("mark").addClass("hasMark");
        $("#mysign").text("已签到").css("background", "#ccc").unbind("click", signs);
		console.log('finished..!!' + index);
	};
	var next = function() {

		if (index >= count_of_ads) {
			after_finished();
			return false;
		}
		console.log('next..' + index);
		goto(index);
		index++;
	};

	var goto = function(i) {
		console.log("ads[" + i + "] " + ads[i]);
		console.log("ads[" + i + "].cid " + ads[i].cid);
		console.log("ads[" + i + "].id " + ads[i].id);
		console.log("ads[" + i + "].inter_space " + ads[i].inter_space);

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
					console.log("记录商家的access 1 " + ads[i].title);

					$.ajax({
						//_checkin_issetClick
						url: Routing.generate("_checkin_issetClick", {
							"cid": cid
						}),
						post: "GET",
						success: function(data) {
							if (data == 1) {
								// 未签到过cid
								console.log("未签到过ads[" + i + "].title=" + ads[i].title);
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
										var target = urls.checkin_location + "?aid=" + aid + "&type=1";
										buffer.location.href = target;

										console.log(target + "\n  " + ads[i].title + ' checked');
										console.log("obj:");
										console.log(obj);
										if (obj.code == 1) {}
										// update the user's pts div
										console.log("i:" + i);
										$("div.signInManual li:eq(" + i + ") a").find(".gray").show();
										$("div.signInManual li:eq(" + i + ")").addClass("finish");
									}
								});

							} else {
								console.log("己签到过cid 0 ads[" + i + "].title " + ads[i].title);
								// 己签到过cid
							}
						}
					});

				} else {
					console.log("记录商家的access 0 " + ads[i].title);
				}
			}
		});
		//redirect..
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
				console.log('started..' + index);
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

