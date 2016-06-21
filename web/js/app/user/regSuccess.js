require(['/js/config.js'],function(){
    require(['common']);
    require(['autoJump'], function(autoJump){
        $('.goto').on('click', function(e){
            // e.preventDefault();
            $('.tips').show();
            new autoJump({
                ele: '.tips b',
                secs: 10
            });    
        });
    });
});
