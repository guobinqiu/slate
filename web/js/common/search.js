define(['jquery'],function($){
    function RPAExpand(options){
        this.expandBtn = options.expandBtn;
        this.expandCon = options.expandCon;
        this.init();
    }
    RPAExpand.prototype = {
        init: function(){
            var _self = this;
            $.each(_self.expandCon, function(i, e){
                $(this).eq(i).hide();
                _self.oper(i);
            });
        },
        oper: function(index){
            var _self = this;
            var curExpandBtn = _self.expandBtn.eq(index);
            var curExpandCon = _self.expandCon.eq(index);
            curExpandBtn.hover(function(){
                curExpandCon.slideToggle("fast");
            }, function(){
                curExpandCon.slideToggle("fast", function(){

                });
            });
        }
    };
    var expand = new RPAExpand({
        expandBtn: $('.expand-btn'),
        expandCon: $('.expand-con')
    });
    var search = $('.search');
    search.find('input').bind('focus', function(){
        search.find('label').hide();
    }).bind('blur', function(){
        var val = $(this).val();
        if(val == '搜索商家名字...' || val == ''){
            search.find('label').show();
        }
    });
});