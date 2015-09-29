require(['../config'],function(){
    require(['common']);
    require(['numScroll'], function(RPANumScroll){
        var numScroll = new RPANumScroll({ numScrollEle: '.digits b', config: {
            digitH : 30,
            num: 89754,
            animateTimer: 5000
        }});
    });
});