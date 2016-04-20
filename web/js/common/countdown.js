define(['jquery'],function($){
    function RPACountdown(options){
        this.countdownEle = options.countdownEle;
        this.start();
    }
    RPACountdown.prototype = {
        fillDigits: function(num, digit){
            var _self = this;
            var v = '0000' + num;
            return v.substr(v.length - digit);
        },
        countdown: function(){
            var _self = this;
            $(_self.countdownEle).each(function(i, e){
                var str, endDiff, localDate, endDate;
                var timelimit_ut = $(this).parent().find('input[name="timelimit_ut"]').val();
                var oneDay = 24*60*60*1000,
                    oneHour = 60*60*1000,
                    oneMinute = 60*1000,
                    oneSecond = 1000;
                var dayDiff, hourDiff, minuteDiff, secondDiff, millisecondDiff;
                localDate = new Date();
                endDate = new Date(timelimit_ut*1000);
                endDiff = endDate - localDate;
                dayDiff = _self.fillDigits(Math.floor(endDiff/oneDay), 2);
                hourDiff = _self.fillDigits(Math.floor((endDiff%oneDay)/oneHour), 2);
                minuteDiff = _self.fillDigits(Math.floor(((endDiff%oneDay)%oneHour)/oneMinute), 2);
                secondDiff = _self.fillDigits(Math.floor((((endDiff%oneDay)%oneHour)%oneMinute)/oneSecond), 2);
                millisecondDiff = _self.fillDigits(Math.floor((((endDiff%oneDay)%oneHour)%oneMinute)%oneSecond), 2);
                str = '<span>' + hourDiff + '</span><b>:</b><span>' + minuteDiff + '</span><b>:</b><span>'+ secondDiff + '</span>';
                $(this).html(str);
            });
            setTimeout(function(){ _self.countdown();}, 1000);
        },
        start: function(){
            var _self = this;
            _self.countdown();
        }
    };
    var countdown = new RPACountdown({ countdownEle: '.countdown'});
});