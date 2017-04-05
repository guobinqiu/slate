$(function(){
    //验证支付宝账户
    function validateAlipayAccount(exchangeForm){
        if(exchangeForm == undefined){
            return false;
        }
        var alipayInput = '#alipay', 
            alipayRepeatInput = '#alipayRepeat', 
            realNameInput = '#realName',
            alipayNum = $.trim($(alipayInput).val()),
            alipayRepeatNum = $.trim($(alipayRepeatInput).val());
        if (alipayNum == '' || (exchangeForm.isAlipay(alipayNum) == false)) {
            exchangeForm.eError(alipayInput, '请输入有效的支付宝账号');
            return false;
        }
        if(!exchangeForm.isEqual(alipayNum, alipayRepeatNum)){
            exchangeForm.eError(alipayRepeatInput, '两次输入不一致！');
            return false;
        }
        var name = $.trim($(realNameInput).val());
        if (name == '' || (exchangeForm.isRealName(name) == false)) {
            exchangeForm.eError(realNameInput, '请务必填写真实姓名与支付宝匹配，否则有可能兑换失败。');
            return false;
        }
        return true;
    }
    //验证手机账户
    function validateMobileAccount(exchangeForm){
        if(exchangeForm == undefined){
            return false;
        }
        var mobileInput = '#mobile', 
            mobileRepeatInput = '#mobileRepeat',
            mobileNum = $.trim($(mobileInput).val()),
            mobileRepeatNum = $.trim($(mobileRepeatInput).val());
        if (mobileNum == '' || (exchangeForm.isPhone(mobileNum) == false)) {
            exchangeForm.eError(mobileInput, '请输入有效的手机号码');
            return false;
        }else{
            if(!exchangeForm.isEqual(mobileNum, mobileRepeatNum)){
                exchangeForm.eError(mobileRepeatInput, '两次输入不一致！');
                return false;
            }
            exchangeForm.eSucceed(mobileRepeatInput);
        }
        return true;
    }
    //验证兑换金额
    function validateExchangeMoney(exchangeForm){  
        if(exchangeForm == undefined){
            return false;
        }      
        var moneyInput = '#money';
        if($(moneyInput).length != 0){
            exchangeForm.eFocus(moneyInput);
        }else{
            exchangeForm.eError(moneyInput, '请选择要兑换的金额！');
            return false;
        }
        return true;
    }
    //获取兑换类型
    function getExchangeType(){
        if($('#existAlipay').length != 0 ){
            exchangeType = 'alipay';
        }
        if($('#existMobile').length != 0){
            exchangeType = 'mobile';
        }
        return exchangeType;
    }
    //开始验证表单
    function executeValidate(exchangeType, isBind){
        var validateResult = false, exchangeForm;
        if(exchangeType == '' || exchangeType == undefined){
            return false;
        }
        if(exchangeType == 'alipay'){
            exchangeForm = new ValidateAlipay({
                alipayInput: '#alipay',
                realName: '#realName',
                isRepeatInput: true,
                isFocusPrompt: true
            });
            validateResult = validateAlipayAccount(exchangeForm);
        }else if(exchangeType == 'mobile'){
            exchangeForm = new ValidateMobile({
                mobileInput: '#mobile',
                isSendCode: true,
                isRepeatInput: true,
                isFocusPrompt: true
            });
            validateResult = validateMobileAccount(exchangeForm);
        }
        
        return isBind?validateExchangeMoney(exchangeForm):validateResult&&validateExchangeMoney(exchangeForm);
    }
    //计算余额
    function calcBalance(){
        var exchangeOptions = {
                option: '.option', 
                need: '#need', 
                rest: '#rest',  
                money: '#money', 
                points: $('#curPoints').val(), 
                saveBtn: '#saveBtn'
            }, obj;
        obj = new exchange(exchangeOptions);
        //余额不足
        if(!obj.initMoney()){
            $('#saveBtn').unbind('click');
            return false;
        }
    }

    var curVal = $('.curVal'),
        modifyInput = $('.modifyInput');
    var isBind = false;
    
    //初始化兑换页面
    function initPage(){
        var alipayId = $('#curAlipay').text(), 
            alipayName = $('#curName').text(), 
            num = $('#curMobile').text();
        if(alipayId != undefined 
            && alipayId != '' 
            && alipayName != undefined 
            && alipayName != ''){ 
            isBind = true;
        }
        if(num != '' && num != undefined){ 
            isBind = true;
        }
        if(isBind){
            curVal.show();
            modifyInput.hide();
        }else{
            curVal.hide();
            modifyInput.show();
        }
        calcBalance();
    }
    initPage();
    
    //绑定新账户
    var modifyBtn = $('#modify');
    modifyBtn.on('click', function(){
        var exchangeType = getExchangeType();
        switch(exchangeType){
            case 'alipay': $('#existAlipay').val('1'); break;
            case 'moblie': $('#existMobile').val('1'); break;
            default: break;
        }
        isBind = false;
        curVal.hide();
        modifyInput.show();
    });

    //选择兑换金额
    var $options = $('.option');
    $options.on('click', function(){
        var i = $options.index(this);
        $options.removeClass('active').eq(i).addClass('active');
        $options.removeAttr('id').eq(i).attr('id','money');
        $options.removeAttr('name').eq(i).attr('name','money');
        $('#money_error').removeClass('error');
        calcBalance();
    });
    
    //提交表单
    var $saveBtn = $('#saveBtn');
    $saveBtn.on('click', function(){ 
        var exchangeType = getExchangeType();
        if(!executeValidate(exchangeType, isBind)){
            return false; 
        } 
        var selOption = $('#money').find('.points').text();
        var selNum = selOption.substr(0, selOption.indexOf('积分'));
        $('#changes').val(selNum);
        $('#form1').submit();
        //ajax        
    });
    
});