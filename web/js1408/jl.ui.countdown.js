
(function ($) {
    // 私有方法

    var layerCss = {
        countdownShow : 'countDown'
    }
    function createHtml($this){
        var countdownShow = $('<div></div>');
        countdownShow.addClass(layerCss.countdownShow);
        $this.append(countdownShow);
    }

    //判断输入值
    function isLegal(options){
        if(options.staDate == ''|| options.staDate == undefined|| options.endDate == '' || options.endDate == undefined){
            return false;
        }else{
            //未做日期格式化，现固定日期格式为：yyyy/mm/dd hh:mm:ss
            options.staDate = new Date(options.staDate);
            options.endDate = new Date(options.endDate);
            if(options.staDate == 'Invalid Date' || options.endDate == 'Invalid Date'){
                return false;
            }
        }
        options.curDate = new Date();
        options.endDiff = (options.endDate).getTime() - options.curDate.getTime();
        if(options.endDiff <= 0 ){
            return false;
        }
        return true;
    }
    //对显示数字进行补位
    function fillDigits(num, digit){
        switch (digit){
            case 2: return num < 10 ? ('0' + num) : num;
            case 3: return num < 10 ? ('00' + num):(num < 100 ? ('0' + num) : num);
            default : return num;
        }
    }

    //计算差值 判断并显示倒计时
    function countdown(options){
        var str, startDiff;
        var oneDay = 24*60*60*1000,
            oneHour = 60*60*1000,
            oneMinute = 60*1000,
            oneSecond = 1000;
        var dayDiff, hourDiff, minuteDiff, secondDiff, millisecondDiff;
        options.curDate = new Date();
        options.endDiff = (options.endDate).getTime() - options.curDate.getTime();
        dayDiff = Math.floor(options.endDiff/oneDay);
        hourDiff = Math.floor((options.endDiff%oneDay)/oneHour);
        minuteDiff = Math.floor(((options.endDiff%oneDay)%oneHour)/oneMinute);
        secondDiff = Math.floor((((options.endDiff%oneDay)%oneHour)%oneMinute)/oneSecond);
        millisecondDiff = Math.floor((((options.endDiff%oneDay)%oneHour)%oneMinute)%oneSecond);
        if(dayDiff <= 0 && hourDiff <= 0 && minuteDiff <= 0 && secondDiff <= 0 && millisecondDiff <= 0){
            str = '还剩<span>00</span><strong>：</strong><span>00</span><strong>：</strong><span>00</span>';
            clearInterval(options.countdownStart);
        }else{
            str = '还剩<span>' + fillDigits(hourDiff, 2) + '</span><strong>：</strong><span>' + fillDigits(minuteDiff, 2) + '</span><strong>：</strong><span>' + fillDigits(secondDiff, 2) + '</span>';
        }
        startDiff = options.staDate.getTime() - options.curDate.getTime();
        if(startDiff <= 0){
            $('.' + layerCss.countdownShow).html(str);
        }else{
            $('.' + layerCss.countdownShow).html('即将开始');
        }
    }
    // 公有方法
    var methods = {
        init: function (initOptions) {
            return this.each(function () {
                var $this = $(this),

                options = $.extend({}, options,  $.fn.countdown.defaults, initOptions);
                createHtml($this);
                if(isLegal(options)){
                    options.countdownStart = setInterval(function(){countdown(options)}, 100);
                }
                else{
                    $('.' + layerCss.countdownShow).html('请重新输入日期！');
                }
            });
        },
        destroy: function () {
            return this.each(function () {
            });
        },
        option: function (key, value) {
            if (arguments.length == 2)
                return this.each(function () {
                    if (options[key]) {
                        options[key] = value;
                    }
                });
            else
                return options[key];
        }
    };

    var pluginName = 'countdown';

    var options = {
        curDate: new Date(),
        endDiff: '',
        countdownStart: ''
    };

    $.fn.countdown = function () {
        var method = arguments[0];
        if (methods[method]) {
            method = methods[method];
            arguments = Array.prototype.slice.call(arguments, 1);
        } else if (typeof method === "object" || !method) {
            method = methods.init;
        } else {
            $.error("Method(" + method + ") does not exist on " + pluginName);
            return this;
        }
        return method.apply(this, arguments);
    };

    // 插件的defaults
    $.fn.countdown.defaults = {
        staDate:new Date()
    };
}(jQuery));

