/*-------------------
引用jquery.js, jqueryCookie.js, common.js, textScroll.js, countdown.js
-------------------*/
$(function(){
    /*---新手引导部分---*/
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