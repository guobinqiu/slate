$(document).ready(function(){
  $(".categorys li").hover(function(){
	  $(this).children(".second").show();
	},function(){
	  $(this).children(".second").hide();
	})
});