require(['../../config'],function(){
    require(['common']);
    require(['jquery', 'mobile', 'exchange'],function($, mobile, exchange){
        //表单验证
        var mobileSave = $('#mobile_save'), moneyInput = '#money';
        var mobileInput = '#mobile', mobileRepeatInput = '#mobileRepeat';
        var curPoints = $('#curPoints').val();
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
        //验证手机
        function executeValidate(isValidate){
            if(isValidate){
                validateMobile = new mobile({
                    mobileInput: mobileInput,
                    isSendCode: false,
                    isRepeatInput: true,
                    isFocusPrompt: true
                });
                mobileSave.unbind('click');
                mobileSave.on('click', function(){
                    var mobileNum = $.trim($(mobileInput).val()),
                        mobileRepeatNum = $.trim($(mobileRepeatInput).val());
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
            if(obj.initMoney() == undefined){
                executeValidate(isValidate);
                return false;
            }
            if(!obj.initMoney()){
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