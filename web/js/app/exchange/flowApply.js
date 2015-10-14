require(['../../config'],function(){
    require(['common']);
    require(['jquery', 'exchange'], function($, exchange){
        var mobileSave = $('#mobile_save'),
            moneySucceed = $('#money_succeed'),
            moneyError = $('#money_error');
        var exchangeOptions = {option: '.option', need: '#need', rest: '#rest', money: '#money', saveBtn: '#mobile_save'};
        function validateMoney(){
            var moneyInput = $('#money');
            if(moneyInput.length != 0){
                moneyInput.removeClass('input_error');
                moneySucceed.removeClass();
                moneyError.removeClass().html('');
            }else{
                moneyInput.removeClass().addClass('input_error');
                moneySucceed.removeClass();
                moneyError.removeClass().addClass('error').html('请选择要兑换的金额！');
                return false;
            }
            return true;
        }
        function validateBalance(){
            var obj = new exchange(exchangeOptions);
            console.log('余额是否充足：'+obj.initMoney());
            if(obj.initMoney() == undefined){
                console.log('没有选取兑换金额！');
                mobileSave.on('click', function(){
                    if(!validateMoney()){
                        mobileSave.unbind('click');
                        return false;
                    }
                });
                return false;
            }
            if(!obj.initMoney()){
                console.log('不可以提交，余额不足啊！');
                mobileSave.unbind('click');
                return false;
            }
            mobileSave.on('click', function(){
                if(!validateMoney()){ return false;}
                console.log('可以提交了！');
                //ajax
            });
            return true;
        }

        //交互模拟结果数据
        //var data = { result: { }};
        var data = { result: { num: '13658965463'}};
        if(data.result.num){
            var curMobile = $('#curMobile');
            curMobile.text(data.result.num).show();
            validateBalance();
        }else{
            console.log('获取当前手机号失败啦！');
        }
        $(exchangeOptions.option).on('click', function(){
            var i = $(exchangeOptions.option).index(this);
            $(exchangeOptions.option).removeClass('active').eq(i).addClass('active');
            $(exchangeOptions.option).removeAttr('id').eq(i).attr("id","money");
            validateMoney();
            validateBalance();
        });
    });
});