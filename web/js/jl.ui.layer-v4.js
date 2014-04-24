/**
 * Created by wangliting on 14-4-21.
 */
(function($){
    /**
     * 公共方法体
     * @type {{init: Function, destroy: Function, option: Function}}
     */
    var methods = {
        init: function(initOptions){
            return this.each(function(){
                options = $.extend({}, options, $.fn.xrLayer.defaults, initOptions);
                var guide = $(options.guide),
                    guideBtn = $(options.guideBtn),
                    bgMask = $(options.bgMask),
                    newGuide = $(options.newGuide),
                    closeBtn = $(options.closeBtn),
                    isClick = options.isClick,
                    showGuide = function(){
                        bgMask.removeClass('none');
                        newGuide.removeClass('none');
                    },
                    closeGuide = function(){
                        bgMask.addClass('none');
                        newGuide.addClass('none');
                    },
                    showStep = function(index){
                        guide.addClass('none').eq(index+1).removeClass('none');
                    };

                showGuide();
                guideBtn.on(isClick, function(){
                    var curEle = $(this).parent().parent().parent();
                    var index = guide.index(curEle);
                    if(index == (guide.length-1)){
                        closeGuide();
                    }else{
                        showStep(index);
                    }
                });
                closeBtn.on(isClick, function(){
                    closeGuide();
                });
            });
        },
        /**
         *预留函数
         * @returns {*}
         */
        destroy: function(){
            return this.each(function(){
            });
        },
        /**
         *
         * @param key
         * @param value
         * @returns {*}
         */
        option: function(key, value){
            if(arguments.length == 2)
                return this.each(function(){
                    if(options[key]){
                        options[key] = value;
                    }
                });
        }
    };

    var methodName = "xrLayer";

    var options = {};

    /**
     * 插件入口
     * @returns {*}
     */
    $.fn.xrLayer = function () {
        var method = arguments[0];
        if (methods[method]) {
            method = methods[method];
            arguments = Array.prototype.slice.call(arguments, 1);
        } else if (typeof method === "object" || !method) {
            method = methods.init;
        } else {
            $.error("Method(" + method + ") does not exist on " + methodName);
            return this;
        }
        return method.apply(this, arguments);
    }

    /**
     * 插件使用的数据
     * @type {{data: Array, className: string, postLoad: null}}
     */
    $.fn.xrLayer.defaults = {
    };
})(jQuery);
