(function($){
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
			$(window).unbind('.infscr');
			$(window).scrollTop(0);
			var items = obj.keywords;
			var $box = $('<div class="tablist block" id="tabs-1"><div id="waterfall"></div><div id="more" class="more"></div></div>');
			var html = '';
			for (var i = 0; i < items.length; i++) {
				html += "<div class=\"cell\">" + items[i]['content'] + "</div>";
			}
			$box.find("#waterfall").html(html);
			$box.find("#more").html("<a href="+Routing.generate("jili_frontend_taobao_categoryapi", {"id": id,"page":2})+"></a>");
			$('.taobaoCon').html($box);
			/*$('#tabs-1').infinitescroll({
				loading: {
					finishedMsg: "<br style='clear:both'/><em>没有更多了。</em>",
					img: $("#loading_bar").html(),
					msgText: ""
				},
				navSelector: '#more',
				nextSelector: '#more a',
				itemSelector: ".cell",
				debug: false,
				dataType: 'json',
				contentSelector: '#waterfall',
				animate: false,
				extraScrollPx: 50,
				template: function(data) {
					$boxes = $("<div class = 'container'></div>");
					$.each(data.keywords, function(key, value){
						myString = "<div class = 'cell'>" + value.content + "</div>";
						$box = $(myString);
						$boxes.append($box);
					});
					return $boxes.find('.cell');
				}
			})*/
			if($(window).height() > $("body").height()){
				$("footer").css("position","fixed");
			}
			else{
				$("footer").css("position","");
			} 
		},
		error: function(e) {
			console.log(e.message);
		}
	});
}
	$(function(){
		jili.taobao.searchBycatgory(1); 
		$('.ltab li:first').addClass('ui-tabs-active');
		$('.ltab li').on('click', function(){
			var index = $('.ltab li').index(this);
			var categoryId = $(this).attr('id');
			$('.ltab li').removeClass('ui-tabs-active').eq(index).addClass('ui-tabs-active');
			jili.taobao.searchBycatgory(categoryId); 
		});
	});
})(jQuery);