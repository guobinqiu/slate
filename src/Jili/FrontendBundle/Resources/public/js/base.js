function selectYear_amazon() {
	var month = $("#month");
	if ($("#year").val() == '0') {
		month.get(0).options.length = 0;
		month.get(0).options.add(new Option("请选择月", "0"));
	} else {
		month.get(0).options.length = 0;
		month.get(0).options.add(new Option("请选择月", "0"));
		for (var i = 1; i < 13; i++) {
			month.get(0).options.add(new Option(i, i));
		}
	}
}

function selectCity_amazon() {
	var html = '';
	if ($("#province").val() != 0) {
		$.ajax({
			url: Routing.generate('_user_getCity', {
				"cid": $('#province').val()
			}),
			type: "POST",
			success: function(data) {
				var obj = eval(data);
				for (var i = 0; i < obj.length; i++) {
					html += "<option value='" + obj[i]['id'] + "'>" + obj[i]['cityName'] + "</option>";
				}
				$("#city").html(html);
			}
		});
	} else {
		html += "<option value='0'>请选择地区</option>";
		$("#city").html(html);
	}
}
function getYear_amazon() {
	var year = $("#year");
	var month = $("#month");

	var yearhtml = '';
	var monthhtml = '';
	var date = new Date();
	var y = date.getFullYear();
	yearhtml += "<option value='0'>请选择年</option>";
	for (var i = 1940; i <= y; i++) {

		yearhtml += "<option value='" + i + "' >" + i + "</option>";
	}
	year.html(yearhtml);
	monthhtml += "<option value='0'>请选择月</option>";
	for (var i = 1; i < 13; i++) {

		monthhtml += "<option value='" + i + "' >" + i + "</option>";
	}
	month.html(monthhtml);
}
function getProince_amazon() {
	$.ajax({
		url: Routing.generate('_user_province'),
		type: "POST",
		success: function(data) {
			var obj = eval(data);
			var html = "<option value='0'>请选择省</option>";
			for (var i = 0; i < obj.length; i++) {
				html += "<option value='" + obj[i]['id'] + "' >" + obj[i]['provinceName'] + "</option>";
			}
			$("#province").html(html);
		}
	});
}
function getHobby() {
	$.ajax({
		url: Routing.generate('_user_hobby'),
		type: "POST",
		success: function(data) {
			var obj = eval(data);
			var html = '';
			for (var i = 0; i < obj.length; i++) {
				html += "<input name='hobby' type='checkbox' value='" + obj[i]['id'] + "' >" + obj[i]['hobby'];
			}
			$(".hobby").html(html);
		}
	});
}
function getIncome() {
	$.ajax({
		url: Routing.generate('_user_income'),
		type: "POST",
		success: function(data) {
			var obj = eval(data);
			var html = '<select id="income" name="income">';
			html += '<option value="0">请选择收入</option>';
			for (var i = 0; i < obj.length; i++) {
				html += '<option value="' + obj[i]['id'] + '" >' + obj[i]["income"] + '</option>';
			}
			html += '</select>';
			$(".income").html(html);
		}
	});
}
function isExistInfo() {
	$.ajax({
		url: Routing.generate('_user_isExistInfo'),
		type: "POST",
		success: function(data) {
			if (data == 1) {
				$.ajax({
					url: Routing.generate('_default_infoVisit'),
					type: "POST",
					success: function(data) {
						if (data == 1) {
							amazonReward();
						}
					}
				});
			}
		}
	});
}
function showInfo() {
	$.ajax({
		url: Routing.generate('_user_userInfo'),
		type: "POST",
		success: function(data) {
			var obj = eval(data);
			$('.login').html(obj[0]['email']);
			$('.nick').html(obj[0]['nick']);
			if (obj[0]['sex'] == 1) {
				document.getElementById("man").checked = true;
			}
			if (obj[0]['sex'] == 2) {
				document.getElementById("feman").checked = true;
			}
			if (obj[0]['mobile']) {
				$("#tel").val(obj[0]['mobile']);
			}

		}
	});
}
function countMs() {
	$.ajax({
		url: Routing.generate('_user_countMs'),
		type: "POST",
		success: function(data) {
			$("#countMessage").html(data);
		}
	});
}

function showFlag() {
	$.ajax({
		url: Routing.generate('_user_createFlag'),
		type: "POST",
		success: function(data) {
			if (data == 1) {
				window.location.href = Routing.generate('_homepage');
			}

		}
	});
}

function isVisit() {
	$.ajax({
		url: Routing.generate('_default_isExistVist'),
		type: "POST",
		success: function(data) {
			if (data == 1) {
				$("#playGame").addClass("gameset");
			}
		}
	});
}

function amazonReward() {
	$('.blackBg').show();
	$('.amazon').show();
	$("#update").show();
	$('.amazon').css("left", ($('body').width() - $('.amazon').width()) / 2);
	if ($('body').height() < $('.amazon').height() + 240) {
		$('.blackBg').height($('.amazon').height() + 400)
	} else {
		$('.blackBg').height($('body').height())
	}
}

var isNewMs = function(uid) {
	if (typeof uid == "undefined") {
		return false;
	}
	$.ajax({
		url: Routing.generate('_user_isNewMs', {
			'id': uid
		}),
		type: "POST",
		success: function(data) {
            if ( data > 0) {
                $('.message').attr('href', Routing.generate("_user_message", {
                    "sid":  data
                }));
            }  
		}
	});
}

