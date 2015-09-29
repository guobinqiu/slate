define(['jquery'],function($){
    function AutoJump(options){
        this.ele = options.ele;
        this.secs = options.secs;
        this.init();
    }
    AutoJump.prototype = {
        init: function(){
            var _self = this;
            _self.oper();
        },
        oper: function(){
            var _self = this, s,
                url = 'http://www.91wenwen.net/';
            s = setInterval(function(){
                if(_self.secs <= 0){
                    clearInterval(s);
                    window.location.href = url;
                    return;
                }
                $(_self.ele).text(_self.secs+'s');
                _self.secs--;
            }, 1000);
        }
    };
    return AutoJump;
});