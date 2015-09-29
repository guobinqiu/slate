require(['../config'],function(){
    require(['common']);
    require(['installer', 'swfobject'], function(installer, swfobject){
        window.__startAirInstall(
            'divInstaller',
            'http://www.91wenwen.net/swf/gadget/AIRInstallBadge.swf',
            '2.6',
            '91wenwen.com.DesktopWidget',
            '91问问桌面工具',
            '1.0.9',
            'http://www.91wenwen.net/dl/gadget/91wenwenDesktopWidget_1_0_9.air'
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
            btns.removeClass('active').eq(index).addClass('active');
            cons.hide().eq(index).show();
            arrow.removeClass().addClass(arrows[index]);
            slideD.slideDown();
        });
        close.on('click', function(){
            slideD.slideUp();
            btns.removeClass('active');
        });
    });
});