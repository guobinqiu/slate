define(['jquery'],function($){
    function RPANumScroll(options){
        this.numScrollEle = options.numScrollEle;
        this.config = options.config;
        this.start();
    }
    RPANumScroll.prototype = {
        fill: function(len){
            var str = '';
            for(var i = 0; i< len; i++){
                str = str + '0';
            }
            return str;
        },
        numToArr: function(){
            var _self = this;
            if(_self.config.num == '' || _self.config.num == undefined){ return;}
            var str = _self.config.num.toString();
            var arr = str.split('');
            if(arr.length < $(_self.numScrollEle).length){
                str = _self.fill($(_self.numScrollEle).length - arr.length) + str;
            }else{
                str = str.substring(arr.length - $(_self.numScrollEle).length, arr.length);
            }
            arr = str.split('');
            return arr;
        },
        numScroll: function(){
            var _self = this;
            var arr = _self.numToArr();

            $(_self.numScrollEle).each(function(i, e){
                $(e).animate({ marginTop: '-' + (arr[i]*(_self.config.digitH)) + 'px'}, _self.config.animateTimer);
            });
        },
        start: function(){
            this.numScroll();
        }
    };
    return RPANumScroll;
});