/*-------------------
引用common.js
-------------------*/
$(function(){
	$('.goto').on('click', function(e){
        window.location.href = Routing.generate('_homepage');   
    });
});
