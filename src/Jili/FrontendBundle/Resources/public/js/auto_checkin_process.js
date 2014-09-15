$(function() {
	console.log(jili_autocheckin);
	if (typeof jili_autocheckin != 'undefined') {
		auto_checkin.start({
			advertiserments: jili_autocheckin.advertiserments,
			checkin_point: jili_autocheckin.checkin_point,
			urls: urls
		});
	} else {
		console.log('Already checked in today');
	}
});

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

	var next = function() {

		if (index >= count_of_ads) {
			return false;
		}
		console.log('next..' + index);
		goto(index);
		index++;
	};

	var goto = function(i) {
		console.log(ads[i]);
		console.log(ads[i].cid);
		console.log(ads[i].id);
		console.log(ads[i].inter_space);

		var cid = ads[i].cid;
		var aid = ads[i].id;
		var points = pts;

		//function goto(cid,aid,points){
		$.ajax({
			// 记录商家的access: _advertiserment_click
			url: urls.advertiserment_click + "?id=" + aid,
			post: "GET",
			success: function(data) {
				if (data == 1) {
					console.log("记录商家的access 1 " + ads[i].title);

					$.ajax({
						//_checkin_issetClick
						url: urls.checkin_issetClick + "?cid=" + cid,
						post: "GET",
						success: function(data) {
							if (data == 1) {
								// 未签到过cid
								console.log("未签到过cid 1 " + ads[i].title);

								$.ajax({
									//_checkin_clickInsert
									url: urls.checkin_clickInsert + "?cid=" + cid + "&aid=" + aid,
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

										console.log(target + ' ' + ads[i].title + ' checked');
                                        console.log("obj:");
                                        console.log(obj);
										if (obj.code == 1) {
											// update the user's pts div
										}
									}
								});

							} else {
								console.log("己签到过cid 0 " + ads[i].title);
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
		}
	}
} ();

//
//	console.log("process the auto checkin now ... ");
//
//	if (jili_autocheckin.is_set) {
//		initFrame();
//	}
//
//	console.log("loop shops for auto check");
//
//	// iframe.onload  = function () {
//	check.start();
//}
//// todo  checkNext()
//// todo iframe onload event
//// render the current checkin..  EchoCheckinProcess();
//console.log("loop shops for auto check");

