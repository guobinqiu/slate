require(['/js/config.js'],function(){
    require(['common']);
    require(['autoJump'], function(autoJump){
        new autoJump({
            ele: '.tips b',
            secs: 10
        });
    });
});
