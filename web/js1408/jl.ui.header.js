/**
 * Created by wangliting on 14-8-6.
 */
(function($){
    $.fn.expandList = function(){
        var expandIcon = $(this);
        var dailyList = $('.dailyList');
        expandIcon.css('cursor', 'pointer');
        expandIcon.on('click', function(){
            dailyList.toggleClass("fnHide");
        });
    }
})(jQuery);
(function($){
    $.fn.switchBg = function(options){
        var switchEle = $(this),
            switchMethod = options.switchMethod,
            switchClass = options.switchClass;

        function switchFocus(){
            switchEle.focusin(function(){ $(this).val("");});
			switchEle.focusout(function(){ 
				if($(this).val()==""){
					$(this).val("请输入您要搜索的商家名称");
				}
			});
        }
        function switchHover(){
            switchEle.hover(function(){
                var index = switchEle.index(this);
                switchEle.removeClass(switchClass).eq(index).addClass(switchClass);
            },function(){
                $(this).removeClass(switchClass);
            });
        }
        function switchClick(){
            switchEle.on('click', function(){
                var index = switchEle.index(this);
                switchEle.removeClass(switchClass).eq(index).addClass(switchClass);
            });
        }

        switch(switchMethod){
            case 'focus': switchFocus(); break;
            case 'hover': switchHover(); break;
            case 'click': switchClick(); break;
            default : break;
        }
    }
})(jQuery);
(function($){
    $(function(){
        $(".expandIcon").expandList();
        $('.search input').switchBg({switchMethod: 'focus', switchClass: ''});
        $('.dailyList li').switchBg({switchMethod: 'hover', switchClass: 'active'});
    });
})(jQuery);