define(['jquery'],function($){
    function RPATab(options){
        this.tabBtn = options.tabBtn;
        this.tabCon = options.tabCon;
        this.start();
    }
    RPATab.prototype = {
        tab: function(btn, con){
            var _self = this;
            var btns = btn.find('li'),
                cons = con.find('.con'),
                conT = con.find('h2');

            if(btns.length <=0){ btns = btn.find('span'); }
            if(btns.length >0 && cons.length > 0){
                cons.hide().eq(0).show();
                btns.on('click', function(){
                    var index = $(this).index();
                    if(conT.length > 0){
                        if(index == 0){
                            conT.addClass('first');
                        }else{
                            conT.removeClass('first');
                        }
                        conT.html(btns.eq(index).html());
                    }
                    btns.removeClass('active').eq(index).addClass('active');
                    cons.hide().eq(index).show();
                });
                cons.each(function(i, e){
                    if(i != 0){
                        _self.tabToggle(e);
                    }
                });
            }
        },
        tabToggle: function(e){
            var pts = $(e).find('li'),
                cts = $(e).find('p');

            if(pts.length >=0 && cts.length >= 0){
                pts.on('click', function(){
                    var index = $(this).index();

                    if(pts.eq(index).hasClass('active')){
                        pts.eq(index).removeClass('active');
                        cts.eq(index).slideToggle();
                    }else{
                        cts.slideUp().eq(index).slideDown();
                        pts.removeClass('active').eq(index).addClass('active');
                    }
                });
            }
        },
        start: function(){
            var _self = this;
            if(_self.tabBtn && _self.tabCon){
                $(_self.tabBtn).each(function(i, e){
                    _self.tab($(e), $(_self.tabCon).eq(i));
                });
            }
        }
    };
    var tab = new RPATab({tabBtn: '.tabNav', tabCon: '.tabCon'});
});