require(['/js/config.js'],function(){
    require(['common'],function(){
    	$('.goto').on('click', function(e){
            window.location.href = Routing.generate('_homepage');   
        });
    });
});
