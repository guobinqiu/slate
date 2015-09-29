/**
 * Created by wangliting on 14-4-17.
 */
(function($){
    function bgSwitch(options, index){
        $(options.menus).removeClass("hover").eq(index).addClass("hover");
    }
    function setConHeight(options){
        var indexHover = $(options.menus).index($(options.menusHover));
        options.conHeight = $(options.tabcons).eq(indexHover).height();
    }
    function adaptiveHeight(container, options){
        if(options.conHeight>options.maxHeight){
            $(container).css({'height':options.maxHeight + 'px', 'overflow-y':'auto'});
        }else{
            $(container).css({'height':options.conHeight +"px", 'overflow':'hidden'});
            $(container).scrollTop(0,0);
        }
    }
    function init(container, options){
        bgSwitch(options, options.initIndex);
        setConHeight(options);
        adaptiveHeight(container, options);
    }

    $.fn.Tabs = function(container, options){
        options = options || {};
        return this.each(function(){
            options = $.extend({}, options,  $.fn.Tabs.defaults);
            var menus = $(options.menus);
            var isClick = options.isClick || 'click';
            init(this, options);
            menus.on(isClick, function(){
                var index = menus.index($(this));
                bgSwitch(options, index);
                setConHeight(options);
                adaptiveHeight(container, options);
            })
        });
    }
    $.fn.Tabs.defaults = {
    }
})(jQuery);