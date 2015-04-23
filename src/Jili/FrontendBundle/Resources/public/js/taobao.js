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
				html += "<div class=\"cell loadimg\">" + items[i]['content'] + "</div>";
			}
			$box.find("#waterfall").html(html);
			$box.find("#more").html("<a href="+Routing.generate("jili_frontend_taobao_categoryapi", {"id": id,"page":2})+"></a>");
			$(".taoMainCon").html($box);
			$box.find(".cell").each(function(i, e){
				setTimeout(function(){$box.find(".cell").eq(i).removeClass("loadimg");}, 1000);
				setTimeout("alimamatk_show(0)", 800);
			});
			$('#tabs-1').infinitescroll({
				loading: {
					finishedMsg: "<br style='clear:both'/><em>没有更多了。</em>",
					img: $("#loading_bar").find('.loading').attr('src'),
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
					$boxes.find(".cell").addClass("loadimg");
					return $boxes.find('.cell');
				},
				errorCallback: function (e) {console.log("infinit scroll error"); console.log(e) }
			}, function( json, opts) {
				$(".cell").each(function(i, e){
					setTimeout(function(){$box.find(".cell").eq(i).removeClass("loadimg");}, 1000);
					setTimeout("alimamatk_show(0)", 800);
				});
			});
			if($(window).height() > $("body").height()){
				$("footer").css("position","fixed");
			}else{
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
		var ltab = $('.ltab li');
		ltab.eq(0).addClass('ui-tabs-active');
		ltab.on('click', function(){
			var index = ltab.index(this);
			ltab.removeClass('ui-tabs-active').eq(index).addClass('ui-tabs-active');
			var categoryId = $(this).attr('id');
			jili.taobao.searchBycatgory(categoryId);
		});
	});
})(jQuery);

//Goto top
$(function(){  
	showScroll();
	function showScroll(){
		$(window).scroll(function(){
			var scrollValue = $(window).scrollTop();
			scrollValue > 100 ? $('div[class=toTop]').fadeIn():$('div[class=toTop]').fadeOut();
		});
		$('#toTop').click(function(){
			$("html,body").animate({scrollTop:0},200);
		});
	}
}); 