require(['../../config'],function(){
    require(['common']);
    require(['slider'], function(slider){
        var sliderA = new slider({sliderEle: '.main-slider ul', groupBtn: '.btn-group b', textEle: '.main-slider-text', config: {
            index: 0,
            stepWid: 520,
            timer: 5000,
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
        if(window.location.hostname == 'www.91wenwen.net'){
            if($('.offerWow').length >= 1){
                var websiteid = $('#websiteid').val(), memberid = $('#websiteid').val();
                $('iframe').attr('src', 'http://www.offer-wow.com/affiliate/wall/open.do?websiteid='+websiteid +'&styleIndex=1&memberid='+memberid).add('.taskFlow').show();    
            }
            if($('.offer99').length >= 1){
                var pid = $('#pid').val(), userid = $('#userid').val();
                $('iframe').attr('src', 'http://app.offer99.com/index.php?pid='+pid +'&userid='+userid).add('.taskFlow').show();    
            }
            $('.maintenance').hide();
        }else{
            $('iframe').attr('src', '#').add('.taskFlow').hide();
            $('.maintenance').show();
        }
    });
    // require(['alimama'], function(alimama){
    //     var win = window;
    //     alimama.pid = 'mm_49376465_4372428_28778243';
    //     win.alimamatk_onload = win.alimamatk_onload || [];
    //     win.alimamatk_onload.push(alimama);
    // });
});