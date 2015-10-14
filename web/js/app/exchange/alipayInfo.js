require(['../../config'],function(){
    require(['common']);
    require(['jquery', 'alipay', 'exchange'],function($, alipay, exchange){
        //表单验证
        var alipaySave = $('#alipay_save'), moneyInput = '#money';
        var alipayInput = '#alipay', alipayRepeatInput = '#alipayRepeat', realNameInput = '#realName';
        var exchangeOptions = {option: '.option', need: '#need', rest: '#rest', money: '#money', saveBtn: '#alipay_save'};
        var validateAlipay;
        function validateMoney(){
            var moneyBox = $(moneyInput);
            if(moneyBox.length != 0){
                validateAlipay.eFocus(moneyInput);
            }else{
                validateAlipay.eError(moneyInput, '请选择要兑换的金额！');
                return false;
            }
            return true;
        }
        function executeValidate(isValidate){
            console.log('是否验证支付宝账号：'+isValidate);
            if(isValidate){
                validateAlipay = new alipay({
                    alipayInput: alipayInput,
                    realName: realNameInput,
                    isRepeatInput: true,
                    isFocusPrompt: true
                });
                alipaySave.unbind('click');
                alipaySave.on('click', function(){
                    var alipayNum = $(alipayInput).val().trim(),
                        alipayRepeatNum = $(alipayRepeatInput).val().trim();
                    if (alipayNum == "" || (validateAlipay.isAlipay(alipayNum) == false)) {
                        validateAlipay.eError(alipayInput, '请输入有效的支付宝账号');
                        return false;
                    }
                    if(!validateAlipay.isEqual(alipayNum, alipayRepeatNum)){
                        validateAlipay.eError(alipayRepeatInput, '两次输入不一致！');
                        return false;
                    }
                    var name = $(realNameInput).val().trim();
                    if (name == "" || (validateAlipay.isRealName(name) == false)) {
                        validateAlipay.eError(realNameInput, '请输入您的姓名');
                        return false;
                    }
                    validateAlipay.eSucceed(alipayRepeatInput);
                    if(!validateMoney()){
                        return false;
                    }
                    console.log('输入支付宝信息后---可以提交了！');
                    //ajax
                });
            }else{
                validateAlipay = new alipay({
                    alipayInput: alipayInput,
                    realName: realNameInput,
                    isRepeatInput: false,
                    isFocusPrompt: false
                });
                alipaySave.unbind('click');
                alipaySave.on('click', function(){
                    if (!validateMoney()) {
                        return false;
                    }
                    console.log('可以提交了！');
                    //ajax
                });
            }
        }
        function validateBalance(isValidate){
            var obj = new exchange(exchangeOptions);
            console.log('余额是否充足：'+obj.initMoney());
            if(obj.initMoney() == undefined){
                console.log('没有选取兑换金额！');
                executeValidate(isValidate);
                return false;
            }
            if(!obj.initMoney()){
                console.log('不可以提交，余额不足啊！');
                alipaySave.unbind('click');
                return false;
            }
            executeValidate(isValidate);
            return true;
        }
        var curVal = $('.curVal'),
            modifyInput = $('.modifyInput'),
            modifyBtn = $('#modify');
        var isModify = false;

        modifyBtn.on('click', function(){
            curVal.hide();
            modifyInput.show();
            isModify = true;
            validateBalance(isModify);
        });

        //交互模拟结果数据
        var data = { result: { alipayId: 'wangxiaozu@163.com', alipayName: '王小卒'}};
        //var data = { result: { }};
        if(data.result != undefined && data.result != null){
            curVal.find('#curAlipay').text(data.result.alipayId);
            curVal.find('#curName').text(data.result.alipayName);
            curVal.show();
            modifyInput.hide();
        }else{
            curVal.hide();
            modifyInput.show();
            isModify = true;
        }
        validateBalance(isModify);
        $(exchangeOptions.option).on('click', function(){
            var i = $(exchangeOptions.option).index(this);
            $(exchangeOptions.option).removeClass('active').eq(i).addClass('active');
            $(exchangeOptions.option).removeAttr('id').eq(i).attr("id","money");
            validateMoney();
            validateBalance(isModify);
        });
    });
});