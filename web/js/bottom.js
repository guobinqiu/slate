$(document).ready(function(){
	$(window).resize(function() {
        resizeFooter()
    });
	var resizeFooter =function(){
		if($(window).height() > $("body").height()){
			//alert("a");
			$("footer").css("position","fixed");
		}
		else{
			//alert("b");
			$("footer").css("position","none");
			
		}
	//$("footer").css("position","fixed");
	}
	resizeFooter();
});