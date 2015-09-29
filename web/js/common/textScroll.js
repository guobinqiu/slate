define(['jquery'],function($){
    function RPATextScroll(options){
        this.scrollEle = options.scrollEle;
        this.config = options.config;
        this.init();
    }
    RPATextScroll.prototype = {
        init: function(){
            var _self = this, s;
            s = setInterval(function(){_self.exchangeScroll();}, _self.config.timer);
            _self.scrollEle.hover(function(){
                clearInterval(s);
            }, function(){
                s = setInterval(function(){_self.exchangeScroll();}, _self.config.timer);
            });
        },
        exchangeScroll: function(){
            var _self = this;
            var stepWid = _self.config.direction == 'left'? _self.scrollEle.find('li:first').outerWidth():_self.scrollEle.find('li:first').outerHeight();

            if(_self.config.direction == 'left'){
                _self.scrollEle.css({width: '1000%'});
                _self.scrollEle.stop().animate({left : '-'+ (stepWid + 30) +'px'}, _self.config.animateTimer, function(){
                    _self.scrollEle.css('left', '0px');
                    _self.scrollEle.find('li:last').after(_self.scrollEle.find('li:first'));
                });
            }else{
                _self.scrollEle.eq(0).stop().animate({top:'-'+ stepWid +'px'}, _self.config.animateTimer, function(){
                    _self.scrollEle.css({ top: 0});
                    _self.scrollEle.find('li:first').insertAfter(_self.scrollEle.find('li:last'));
                });
            }
        }
    };
    var textScroll = new RPATextScroll({
        scrollEle: $('.exchange-dynamics ul'),
        config: {
            animateTimer: 1000,
            timer: 2000,
            direction: 'top'}
    });
});