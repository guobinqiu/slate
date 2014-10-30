var jili = jili || {};

jili.taobao = {};

jili.taobao.searchBycatgory = function(id) {
	var html = '';
	$.ajax({
		url: Routing.generate("jili_frontend_taobao_categoryapi", {
			"id": id
		}),
		type: "GET",
		//contentType: "application/json",
		success: function(data) {
			var obj = eval(data);
			console.log(obj.current_id);
			console.log(obj.keywords.length);
			var items = obj.keywords;
			for (var i = 0; i < items.length; i++) {
				html += "<div class=\"cell\">" + items[i]['content'] + "</div>";
			}
			//$("#waterfall").append(html);
			$("#waterfall").html(html);
		},
		error: function(e) {
			console.log(e.message);
		}
	});
}

