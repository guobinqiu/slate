require(['/js/config.js'],function(){
    require(['common']);
    // require(['jquery','slider'], function($, slider){
    //    var sliderA = new slider({sliderEle: '.main-slider ul', prevBtn: '.arrowL', nextBtn: '.arrowR', groupBtn: '.btn-group b', config: {
    //        index: 0,
    //        stepWid: 1000,
    //        timer: 2000,
    //        animateTimer: 1000,
    //        eleLen: 4,
    //        isAuto: true,
    //        effect: 'fade'
    //    }});
    // });
    require(['textScroll']);
    require(['countdown']);
    require(['jquery', 'jqueryCookie'], function($){
        //新手引导部分
        function shouldShow(){
            var vp  = $.cookie('guide');
            if (vp == undefined || vp == 1) {
                return true;
            } else {
                return false;
            }
        }
        $('#mask, #newguideWrap, #newguideWrap div:eq(0)').show();
        $('#newguideWrap a.ngbtn').click(function(){
            var current = $(this).parent().parent();
            current.hide();
            current.next().show();
        });
        $(document.body).click(function(event){
            var target = $(event.target);
            if(target.is('.ngbtn1, .ngbtn2')){ return false; }
            $.cookie('guide', 0, { expires: 10000, path: '/' });
            $('#mask, #newguideWrap').hide();
        });
        if(shouldShow()){
            $('#mask, #newguideWrap').show();
        }else{
            $('#mask, #newguideWrap').hide();
        }

        //set profile survey css
        // $('.survey li:has(.proItem)').css('border-bottom', 'none');
        // $(".survey li").not(":has(.proItem)").css('border-bottom', 'solid 1px #E0E5E5');      

        //set answered survey css on Mobile&Tablet
        if($(window).width() < 1024){
            $('.survey li:has(.hiddenPhone)').css('padding','0');
        };
    });
    require(['jquery', 'jqueryCookie'], function($){
        //profile survey prompt popup
        var surveyList = $('#surveyList');
        var prop = $.cookie('prop');
        var proCon = $('.proSurPop');
        function shouldShow(){
           var pp  = $.cookie('prop');
           if (pp == undefined || pp == 1) {
               return true;
           } else {
               return false;
           }
        }
        surveyList.delegate('.proClose','click', function(){
           $.cookie('prop', 0, { expires: 10000, path: '/' }); 
           surveyList.find(proCon).hide();    
        });
        if(shouldShow()){
           surveyList.find(proCon).show();
        }else{
           surveyList.find(proCon).hide();
        }
    });
});