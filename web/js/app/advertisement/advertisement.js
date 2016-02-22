require(['../../config'],function(){
    require(['common']);
    require(['slider'], function(slider){
        var sliderA = new slider({sliderEle: '.main-slider ul', groupBtn: '.btn-group b', config: {
            index: 0,
            stepWid: 764,
            timer: 2000,
            animateTimer: 1000,
            eleLen: 5,
            isAuto: true,
            effect: 'slider'
        }});
    });
    require(['jquery','jqueryCookie'], function($){
        //如果没有cookie执行以下操作
        if($.cookie('prs') == null){
            $('.promptS').show();
            $('.promptSClose').click(function(){
                $('.promptS').hide();
                //添加cookie 
                $.cookie('prs', 1, { expires: 10000 });   
            });
        }
    });
});