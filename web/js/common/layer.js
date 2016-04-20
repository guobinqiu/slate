define(['jquery'],function($){
    var layer = function(option){
        this.mainCon = option.mainCon;
        this.closeBtn = option.closeBtn;
        this.init();
    };

    layer.prototype = {
        init: function(){
            this.addMask();
        },
        addMask: function(){
            var _self = this;
            var div = '<div></div>', pageMask = 'pageMask';
            if($('.' + pageMask).length < 1){
                $(div).addClass(pageMask).appendTo($('body'));
            }else{
                $('.' + pageMask).show();
            }
            _self.addCon();
        },
        addCon: function(){
            var _self = this;
            var div = '<div></div>', pageMask = 'pageMask', maskConWrap ='maskConWrap', maskCon = 'maskCon';
            var cHei = document.body.clientHeight,
                eWid = parseInt(_self.mainCon.width);
            var $maskConWrap = $(div).addClass(maskConWrap).css({'top': cHei/3 + 'px', 'left': '50%', 'marginLeft': '-' + eWid/2 + 'px', 'width': eWid + 'px'});
            var $maskCon = $(div).addClass(maskCon);
            var $con = $(div).addClass(_self.mainCon.className).html(_self.mainCon.con);
            var $close = $(div).addClass(_self.closeBtn).html('关闭').on('click', function(){
                $('.' + pageMask).hide();
                $('.' + maskConWrap).hide();
            });
            if($('.' + maskConWrap).length < 1){
                $('body').append($maskConWrap.append($maskCon.append($con).append($close)));
            }else{
                $('.' + maskConWrap).show();
            }
        }
    };
    return layer;
});