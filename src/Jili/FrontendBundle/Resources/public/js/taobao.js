//; 
var jili = jili || {};

jili.taobao = {};

jili.taobao.searchBycatgory = function(id) {
	var html = '';
    console.log( "x")
	$.ajax({
		url: Routing.generate("jili_frontend_taobao_categoryapi", {
			"id": id
		}),
		type: "GET",
		dataType: "json",
		success: function(obj) {
    console.log( obj)
//			var obj = eval(data);
			console.log(obj.current_id);
			console.log(obj.keywords.length);
			var items = obj.keywords;
            var html = "";
			for (var i = 0; i < items.length; i++) {

				//html += "<div class=\"cell\">" + items[i]['content'] + "</div>";
			$("#waterfall").append("<div class=\"cell\">" + items[i]["content"] + "</div>");
			}
//			$("#waterfall").html(html);
		},
		error: function(e) {
			console.log(e.message);
		}
	});
}

