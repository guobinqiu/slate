require(['../../config'],function(){
    require(['common']);
    require(['iframeResizer'], function(iframeResize){
        iFrameResize({
            log                     : false,                  // Enable console logging
            enablePublicMethods     : true,                  // Enable methods within iframe hosted page
            resizedCallback         : function(messageData){ // Callback fn when message is received
            }
        });
    });
});