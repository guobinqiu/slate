$(document).ready(function(){
  $("#nav .task").hover(function(){
	  $(this).children("ul").show();
	},function(){
	  $(this).children("ul").hide();
	})
});