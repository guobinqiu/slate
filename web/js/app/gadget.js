require(['../config'],function(){
    require(['common']);
    require(['swfobject', 'installer'], function(swfobject, installer){
        window.__startAirInstall(
            'divInstaller',
            'http://wang1-jili.com/swf/gadget/AIRInstallBadge.swf',
            '2.6',
            '91wenwen.com.DesktopWidget',
            '91问问桌面工具',
            '1.0.9',
            'http://wang1-jili.com/dl/gadget/91wenwenDesktopWidget_1_0_9.air'
        );
    });
    require(['jquery'], function($){
        var btns = $('.btn'),
            cons = $('.con'),
            slideD = $('.slideDown'),
            close = $('.closeTag'),
            arrow = $('.arrowUp');
        btns.on('click', function(){
            var index = btns.index(this);
            var arrows = ['arrowUp', 'arrowUp second', 'arrowUp third'];
            if($(this).hasClass('active')){
                btns.eq(index).removeClass('active');
                cons.eq(index).hide();
                arrow.removeClass();
                slideD.slideUp();
            }else{
                btns.removeClass('active').eq(index).addClass('active');
                cons.hide().eq(index).show();
                arrow.removeClass().addClass(arrows[index]);
                slideD.slideDown();
            }
        });
        close.on('click', function(){
            slideD.slideUp();
            btns.removeClass('active');
        });
    });
});