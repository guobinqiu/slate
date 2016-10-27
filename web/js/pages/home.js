/*-------------------
引用jquery.js, jqueryCookie.js, common.js, textScroll.js, countdown.js
-------------------*/
$(function(){
    /*---新手引导部分---*/
    function guideShow(){
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
    if(guideShow()){
        $('#mask, #newguideWrap').show();
    }else{
        $('#mask, #newguideWrap').hide();
    }

    //home banner slider
    //点击按钮更换图片
    var e = $("#planbanner").children(".planpicnum").find("li");
    $(".planpicnum li").hover(function (val) {
        num = $(this).index();
        //获取图片数量
        var imgCount = e.length;
        //获得下一个元素在其父元素中的下标
        if (num < (imgCount - 1)) {
            num += 1;
        }
        else {
            num = 0;
        }
        //
        $(".planpicshow a").hide();
        $(".planpicshow a").eq($(this).index()).show();
    })

    //自动更换图片
    function showImg(val) {
       //获取图片数量
       var imgCount = e.length;
       //显示索引为val的图片
       $(".planpicshow a").hide();
       $(".planpicshow a").eq(val).show();
       $(".planpicnum li").eq(val).addClass('hover').siblings().removeClass('hover');
       if (num < (imgCount - 1)) {
           num += 1;
       }
       else {
           num = 0;
       }
       //3秒后重新绑定图片
       setTimeout(function () { showImg(num); }, 6000);
    }
    //加载图片
    var num = 0; showImg(num);
});