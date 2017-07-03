/*-------------------
引用jquery.js, common.js
-------------------*/
$(function(){
    var link = window.location.href;
    var channel;
    if(link.indexOf('#') != -1){
        channel = link.substr(link.indexOf('#') + 1, link.length);
        $('#' + channel).show();
    }else{
        channel = '';
        $('#survey').show();
    }     
    if($(window).height() > $("body").height()){
        $("#footer").css({"position":"fixed", "bottom":"0", "left": "0", "right": "0"});
        $(".navLayerBg").add($(".navLayer")).css("position","fixed");
    }
    else{
        $("#footer").css("position","static");
        $(".navLayerBg").add($(".navLayer")).css("position","absolute");
    } 
});