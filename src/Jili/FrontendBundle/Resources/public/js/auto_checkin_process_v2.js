$(function() {
	CheckinModule.doAutoCheckin();
});

var CheckinModule = CheckinModule || {};
// 开始自动签到。 
CheckinModule.doAutoCheckin = function() {
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

	var completed = []; // successful checked shop
	var failed = []; // successful checked shop
    var checkin_timeout_id = null;

	var initFrame = function(i) {
		//console.log(">>> initFrmae");
		if ("object" == typeof window.frames[i]) {
			buffer = window.frames[i];
//            console.log('frame exists');
			return false;
		}

		var div_id = "buffer" + i;
		ifrm = document.createElement("iframe");
		ifrm.setAttribute("id", div_id);
		ifrm.setAttribute("name", div_id );
		ifrm.setAttribute("frameborder", 0);
		document.body.appendChild(ifrm);

                window.frames[i].onload = function() {
//                    console.log("buffer.onload:");
                    completed.push(i);
		if ( completed.length >= count_of_ads) {
			after_finished();
			return false;
		}
//                    console.log(completed);
        //removeFrame();
                }
//
//                window.frames[j].onerror= function() {
//                    console.log("buffer.error:");
//                    failed.push(j);
//                    console.log(failed);
//        //removeFrame();
//                }
		return false;
	};

    var roundTimeUp =function() {
        removeFrame();
        failed.push(index);
    };

	var removeFrame = function() {
//		console.log(" removeFrame>>>");
		if ("object" == typeof window.frames[index]) {
            buffer.close();
			var ifrm = document.getElementById("buffer" + index);
			ifrm.parentNode.removeChild(ifrm);
		}
		return false;
	};

	var after_finished = function() {
		$("p.signInAuto").text("恭喜您签到成功");
		CheckinModule.afterFinish();
		return false;
	};

	var next = function() {
		if (index >= count_of_ads) {
			after_finished();
			return false;
		}
		setTimeout(goto(index), 2000);
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
									post: "GET"
								}).success(function(data) {
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

									if (target.trim().length > 0) {
										try {
                                            buffer = window.frames[i].location.href = target;
										} catch(e) {
//											console.log(" name:  " + e.name + " \nmessage:  " + e.message + " \nlineNumber:  " + e.lineNumber + " \nfileName:  " + e.fileName + " \nstack:  " + e.stack);
										}
									}
									$("div.signInManual li:eq(" + i + ") a").find(".gray").show();
									$("div.signInManual li:eq(" + i + ")").addClass("finish");

									// update the user's pts div
									if (obj.code == 1) {
										//                                            $("#points").text(parseInt(obj.point) + parseInt(points));
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

            var j = 0;
            for (j= 0; j< count_of_ads; j++) {
                initFrame(j);
//                window.frames[j].onload = function() {
//                    console.log("buffer.onload:");
//                    completed.push(j);
//                    console.log(completed);
//        //removeFrame();
//                }
//
//                window.frames[j].onerror= function() {
//                    console.log("buffer.error:");
//                    failed.push(j);
//                    console.log(failed);
//        //removeFrame();
//                }

                goto(j);

//                checkin_timeout_id = setTimeout(roundTimeUp, 10000);

                // wait round over
//                while( -1 != completed.indexOf(index) 
//                || -1 != failed.indexOf(index) ) {
//                    sleep(2000);
//                }
//
//                console.log("index:");
//                console.log(index);
//                console.log("completed:");
//                console.log(completed);
//                console.log("failed:");
//                console.log(failed);
            }

            // v3
            // 3000 9000 27000
//            var loop_idx = 0;
//            var loop_count  = 3;
//            while ( completed.length > count_of_ads) {
//                loop_idx++;
//                // how to control the timeout
//                
//                // what if done successfully??
//                // onload ? next() ?
//                // setTimeout( removeFrame(), 4000);
//
//            }
		},
		before_start: function() {
			$("p.signInAuto").text("系统帮您自动签到中……，请稍后");
		},
	}
} ();

//  iframe.addEventListener( "load", function(){
//         //代码能执行到这里说明已经载入成功完毕了
//      this.removeEventListener( "load", arguments.call, false);
//         //这里是回调函数
//
//   }, false);

