require(['../../config'],function(){
    require(['common']);
    require(['slider'], function(slider){
        var sliderA = new slider({sliderEle: '.main-slider ul', groupBtn: '.btn-group b', textEle: '.main-slider-text', config: {
            index: 0,
            stepWid: 520,
            timer: 2000,
            animateTimer: 1000,
            eleLen: 5,
            isAuto: true,
            effect: 'slider'
        }});
    });
    require(['iframeResizer'], function(iframeResizer){
        iFrameResize({
            log                     : false,                  // Enable console logging
            enablePublicMethods     : true,                  // Enable methods within iframe hosted page
            resizedCallback         : function(messageData){ // Callback fn when message is received
            }
        });
    });
    require(['alimama'], function(alimama){
        var win = window;
        alimama.pid = 'mm_49376465_4372428_28778243';
        win.alimamatk_onload = win.alimamatk_onload || [];
        win.alimamatk_onload.push(alimama);
    });
});