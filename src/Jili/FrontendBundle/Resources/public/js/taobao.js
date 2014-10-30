var jili = jili || {};

jili.taobao = {};

jili.taobao.searchBycatgory = function(id) {
	$.ajax({
		url: Routing.generate("jili_frontend_taobao_categoryapi", {
			"id": id
		}),
		type: "GET",
		dataType: "json",
		success: function(obj) {
			console.log(obj.current_id);
			var items = obj.keywords;
			var html = '';
			for (var i = 0; i < items.length; i++) {
				html += "<div class=\"cell\">" + items[i]['content'] + "</div>";
				//$("#waterfall").append("<div class=\"cell\">" + items[i]["content"] + "</div>");
				
			}
			//$("#waterfall").append(html);
			$("#waterfall").html(html);
			console.log(Routing.generate("jili_frontend_taobao_categoryapi", {"id": id,"page":2}));
			$("#more").html("<a href="+Routing.generate("jili_frontend_taobao_categoryapi", {"id": id,"page":2})+">aaa</a>");
			
		},
		error: function(e) {
			console.log(e.message);
		}
	});
}

