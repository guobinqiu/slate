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
});