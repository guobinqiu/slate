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
                    console.log('输入手机号后---可以提交了！');
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
                    console.log('可以提交了！');
                    //ajax
                });
            }
        }

        var curVal = $('.curVal'),
            modifyInput = $('.modifyInput'),
            modifyBtn = $('#modify');

        modifyBtn.on('click', function(){
            curVal.hide();
            modifyInput.show();
            executeValidate(true);
        });

        //交互模拟结果数据
        //var data = { result: { }};
        var data2 = { result: { num: '13658965463'}};
        if(data2.result.num){
            var curMobile = $('#curMobile');
            curMobile.text(data2.result.num).show();
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