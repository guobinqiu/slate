define(['jquery'],function($){
    function BackTop(options){
        this.src = options.src;
        this.init();
    }
    BackTop.prototype = {
        init: function(){
            var _self = this;
            _self.getScrollH();
        },
        addEle: function(){
            var backBtn = $('.backTop');
            if(backBtn.length < 1){
                var div = '<div></div>', $img = $('<img alt="回到顶部"/>');
                $(div).addClass('backTop').html($img.attr('src', this.src)).appendTo($('body'));
            }else{
                backBtn.show();
            }
        },
        backTop: function(timer){
            var currentPosition;
            currentPosition = $(window).scrollTop();
            currentPosition -= 10;
            if(currentPosition > 0){
                $(window).scrollTop(currentPosition);
            }else{
                $(window).scrollTop(0);
                clearInterval(timer);
            }
        },
        runTop: function(){
            var _self = this, timer;
            var backBtn = $('.backTop');
            backBtn.on('click', function(){
                timer = setInterval(function(){ _self.backTop(timer);}, 1);
            });
        },
        getScrollH: function(){
            var _self = this;
            var scrollH;
            $(window).scroll(function(){
                scrollH = $(window).scrollTop();
                if(scrollH > 800){
                    _self.addEle();
                    _self.runTop();
                }else{
                    $('.backTop').hide();
                }
            });
        }
    };
    return BackTop;
});