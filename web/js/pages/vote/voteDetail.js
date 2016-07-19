/*-------------------
引用jquery.js, common.js, textScroll.js
-------------------*/
$(function(){
	var progress = $('.progress');
	for(var i = 0; i< progress.length; i++){
		progress.eq(i).find('p').css('width', progress.eq(i).find('input').val());
	}
});