define(['jquery'],function($){
    function RPASlider(options){
        this.sliderEle = options.sliderEle;
        this.prevBtn = options.prevBtn;
        this.nextBtn = options.nextBtn;
        this.groupBtn = options.groupBtn;
        this.config = options.config;
        this.start();
    }
    RPASlider.prototype = {
        isNull: function(str){
            if(str == '' || str == undefined || typeof str != 'string'){
                return false;
            }
            return true;
        },
        moveEle: function(){
            var _self = this;
            $(this.groupBtn).removeClass('active').eq(_self.config.index).addClass('active');
            if(_self.config.effect == 'slider'){
                $(_self.sliderEle).stop().animate({ left: '-' + (_self.config.index)*(_self.config.stepWid) + 'px'}, _self.config.animateTimer);
            }else{
                $(_self.sliderEle).find('li').fadeOut('fast').eq(_self.config.index).fadeIn(_self.config.animateTimer);
                $('.main-slider-wrapper').removeClass().addClass('main-slider-wrapper color'+_self.config.index).fadeIn(_self.config.animateTimer);
            }
        },
        nextEle: function(){
            var _self = this;
            _self.config.index++;
            if(_self.config.index > (_self.config.eleLen - 1)){
                _self.config.index = 0;
            }
            _self.moveEle();
        },
        prevEle: function(){
            var _self = this;
            _self.config.index--;
            if(_self.config.index < 0){
                _self.config.index = (_self.config.eleLen - 1);
            }
            _self.moveEle();
        },
        start: function(){
            var _self = this, s, el;
            if(_self.isNull(_self.nextBtn) && _self.isNull(_self.prevBtn)){
                $(_self.nextBtn).on('click', function(){ clearInterval(s); _self.nextEle();});
                $(_self.prevBtn).on('click', function(){ clearInterval(s); _self.prevEle();});
                el = [$(_self.sliderEle), $(_self.nextBtn), $(_self.prevBtn)];
                $.each(el, function(i, e){
                    $(e).hover(function(){
                        clearInterval(s);
                        $(_self.nextBtn).show();
                        $(_self.prevBtn).show();
                    }, function(){
                        $(_self.nextBtn).hide();
                        $(_self.prevBtn).hide();
                        s = setInterval(function(){_self.nextEle();}, _self.config.timer);
                    });
                });
            }else{
                $(_self.sliderEle).hover(function(){
                    clearInterval(s);
                }, function(){
                    s = setInterval(function(){_self.nextEle();}, _self.config.timer);
                });
            }
            _self.moveEle();
            $(_self.groupBtn).hover(function(){
                clearInterval(s);
                var index = $(this).index();
                _self.config.index = index;
                _self.moveEle();
            }, function(){
                s = setInterval(function(){_self.nextEle();}, _self.config.timer);
            });
            if(_self.config.isAuto){
                s = setInterval(function(){_self.nextEle();}, _self.config.timer);
            }
        }
    };
    return RPASlider;
});