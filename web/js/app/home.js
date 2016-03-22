require(['../config'],function(){
    require(['common']);
    //require(['jquery','slider'], function($, slider){
    //    var sliderA = new slider({sliderEle: '.main-slider ul', prevBtn: '.arrowL', nextBtn: '.arrowR', groupBtn: '.btn-group b', config: {
    //        index: 0,
    //        stepWid: 1000,
    //        timer: 2000,
    //        animateTimer: 1000,
    //        eleLen: 4,
    //        isAuto: true,
    //        effect: 'fade'
    //    }});
    //});
    require(['countdown']);
    require(['jquery'], function($){
        //读取cookie
        var res = document.cookie.substring(5,10);
        //如果没有cookie执行以下操作
        //新手引导部分
        if(res!="guide"){
            var omar = $('.main-con').height() + 385;
            $('#newguideWrap').css('margin-top','-'+ omar +'px')
            $('#mask, #newguideWrap, #newguideWrap div:eq(0)').show();
            $('#newguideWrap a.ngbtn').click(function(){
                var current = $(this).parent().parent();
                current.hide();
                current.next().show();
            });

            $(document.body).click(function(event){
                var target = $(event.target);
                if(target.is('.ngbtn1, .ngbtn2')){ return false; }
                $('#mask, #newguideWrap').hide();
            });
            //添加cookie
            var oDate = new Date();
            oDate.setDate(oDate.getDate() + 10000);
            document.cookie="name=guide;expires=" + oDate;
        }
    });    
});