require(['../../config'],function(){
    require(['common']);
    require(['jquery', 'mobile', 'exchange'],function($, mobile, exchange){
        //表单验证
        var mobileSave = $('#mobile_save'), moneyInput = '#money';
        var mobileInput = '#mobile', mobileRepeatInput = '#mobileRepeat';
        var curPoints = $('#curPoints').val();
        console.log('获取用户当前积分：'+ curPoints);
        var exchangeOptions = {option: '.option', need: '#need', rest: '#rest', money: '#money', points: curPoints, saveBtn: '#mobile_save'};
        var validateMobile;
        function validateMoney(){
            var moneyBox = $(moneyInput);
            if(moneyBox.length != 0){
                validateMobile.eFocus(moneyInput);
            }else{
                validateMobile.eError(moneyInput, '请选择要兑换的金额！');
                return false;
            }
            return true;
        }
        function executeValidate(isValidate){
            console.log('是否验证手机：'+isValidate);
            if(isValidate){
                validateMobile = new mobile({
                    mobileInput: mobileInput,
                    isSendCode: false,
                    isRepeatInput: true,
                    isFocusPrompt: true
                });
                mobileSave.unbind('click');
                mobileSave.on('click', function(){
                    var mobileNum = $(mobileInput).val().trim(),
                        mobileRepeatNum = $(mobileRepeatInput).val().trim();
                    if (mobileNum == "" || (validateMobile.isPhone(mobileNum) == false)) {
                        validateMobile.eError(mobileInput, '请输入有效的手机号码');
                        return false;
                    }else{
                        if(!validateMobile.isEqual(mobileNum, mobileRepeatNum)){
                            validateMobile.eError(mobileRepeatInput, '两次输入不一致！');
                            return false;
                        }
                        validateMobile.eSucceed(mobileRepeatInput);
                    }
                    if(!validateMoney()){
                        return false;
                    }
                    console.log('输入手机号后---可以提交了！');
                    //获取当前选择的选项内容
                    var selOption = $('#money').find('.points').text();
                    var selNum = selOption.substr(0, selOption.indexOf('积分'));
                    $('#changes').val(selNum);
                    $("#form1").submit();
                    //ajax
                });
            }else{
                validateMobile = new mobile({
                    mobileInput: mobileInput,
                    isSendCode: false,
                    isRepeatInput: false,
                    isFocusPrompt: false
                });
                mobileSave.unbind('click');
                mobileSave.on('click', function () {
                    if (!validateMoney()) {
                        return false;
                    }
                    console.log('可以提交了！');
                    //获取当前选择的选项内容
                    var selOption = $('#money').find('.points').text();
                    var selNum = selOption.substr(0, selOption.indexOf('积分'));
                    $('#changes').val(selNum);
                    $("#form1").submit();
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
                mobileSave.unbind('click');
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
            $("#existMobile").val('1');
            curVal.hide();
            modifyInput.show();
            isModify = true;
            validateBalance(isModify);
        });

        var curMobile = $('#curMobile');
        var num = curMobile.text();
        if(num != '' && num != null){
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
            $(exchangeOptions.option).removeAttr('name').eq(i).attr("name","money");
            validateMoney();
            validateBalance(isModify);
        });
    });
});