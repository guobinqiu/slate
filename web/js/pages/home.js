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

    /*---set profile survey css---*/
    $('.survey li:has(.proItem)').css('border-bottom', 'none');
    $(".survey li").not(":has(.proItem)").css('border-bottom', 'solid 1px #E0E5E5');
});