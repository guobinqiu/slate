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
    require(['iframeResizer'], function(iframeResizer){
        iFrameResize({
            log                     : true,                  // Enable console logging
            enablePublicMethods     : true,                  // Enable methods within iframe hosted page
            resizedCallback         : function(messageData){ // Callback fn when message is received
            }
        });
    });
});