require(['../../config'],function(){
    require(['common']);
    require(['jquery', 'mobile'],function($, mobile){
        //表单验证
        var mobileSave = $('#mobile_save');
        var mobileInput = '#mobile', mobileRepeatInput = '#mobileRepeat';
        var validateMobile;
        function executeValidate(isValidateMobile){
            if(isValidateMobile){
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
                    //判断手机
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
                mobileSave.on('click', function(){
                    $("#form1").submit();
                    //ajax
                });
            }
        }

        var curVal = $('.curVal'),
            modifyInput = $('.modifyInput'),
            modifyBtn = $('#modify');

        modifyBtn.on('click', function(){
            $("#existMobile").val(0);
            curVal.hide();
            modifyInput.show();
            executeValidate(true);
        });

        var curMobile = $('#curMobile');
        var num = curMobile.text();
        if(num != undefined && num != ''){
            curVal.show();
            modifyInput.hide();
            executeValidate(false);
        }else{
            curVal.hide();
            modifyInput.show();
            executeValidate(true);
        }
    });
});