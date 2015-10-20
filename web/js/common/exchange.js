define(['jquery', 'layer'],function($, layer){
    //计算金额
    //交互模拟结果数据（用户当前积分）
    var has = { result: { money: '2050'}};
    var exchange = function(options){
        this.option = options.option;
        this.need = options.need;
        this.rest = options.rest;
        this.money = options.money;
        this.saveBtn = options.saveBtn;
        this.initMoney();
    };
    exchange.prototype = {
        initMoney: function(){
            var _self = this;
            if(has && has.result.money == undefined){ return false;}
            if(!_self.getSelMoney()){
                $(_self.need).text('0');
                $(_self.rest).text(has.result.money);
                return ;
            }
            $(_self.need).text(_self.getSelMoney());
            return _self.restMoney();
        },
        getSelMoney: function(){
            var _self = this;
            var $money = $(_self.money);
            if($money.length < 1){ $(_self.option).removeClass('active'); return false;}
            var moneySel = $money.find('.points').text();
            return moneySel.substr(0, moneySel.indexOf('积分'));
        },
        restMoney: function(){
            var _self = this;
            var sel = parseInt(_self.getSelMoney()), cur = parseInt(has.result.money);
            if(sel <= cur){
                $(_self.rest).text(cur - sel);
                $(_self.saveBtn).removeClass('disabled');
                return true;
            }else{
                var layerConf = {mainCon: { className: 'mainCon', width: '210', con: '您的余额不足!'}, closeBtn: 'close'};
                new layer(layerConf);
                $(_self.rest).text('余额不足');
                $(_self.saveBtn).addClass('disabled');
                return false;
            }
        }
    };
    return exchange;
});