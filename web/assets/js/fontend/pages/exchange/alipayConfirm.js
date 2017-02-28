/*-------------------
引用jquery.js, common.js, alipay.js
-------------------*/
$(function(){
    //表单验证
    var alipaySave = $('#alipay_save');
    var idCardInput = '#idCard';
    var validateIdCard;

    function validateAlipay(){
        var alipayNum = $.trim($(idCardInput).val());
        if (alipayNum == "" || (validateIdCard.isIdCard(alipayNum) == false)) {
            validateIdCard.eError(idCardInput, '请输入有效的身份证号');
            return false;
        }else{
            validateIdCard.eSucceed(idCardInput);
            return true;
        }
    }
    function executeValidate(){
        validateIdCard = new ValidateAlipay({
            alipayInput: null,
            realName: null,
            isRepeatInput: false,
            isFocusPrompt: false
        });
        $(idCardInput).focus(function(){
            validateIdCard.eFocus(idCardInput);
        }).blur(function(){
            if(!validateAlipay()){
                return false;
            }
        });
        alipaySave.on('click', function(){
            if(!validateAlipay()){
                return false;
            }
            $("#form1").submit();
            //ajax
        });
    }
    executeValidate();
});