// JavaScript Document
$(function(){
		var img = $("ul.commodityUl li dt a img");
		var wide = $('img').attr("width");
		var high = $('img').attr("height");
		img.bind('error',function(e){
			this.src = "/images140307/noPic.jpg";
			this.attr("width",wide);
			this.attr("width",high);
		});
			
	});