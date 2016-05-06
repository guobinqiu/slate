define(['jquery'],function($){
    var ValidateMobile = function(option){
        this.mobileInput = option.mobileInput;
        this.isSendCode = option.isSendCode;
        this.isRepeatInput = option.isRepeatInput;
        this.isFocusPrompt = option.isFocusPrompt;
        this.init();
    };

    ValidateMobile.prototype = {
        init: function(){
            var _self = this;
            if(_self.isFocusPrompt){
                $(_self.mobileInput).focus(function(){
                    _self.eFocus(_self.mobileInput);
                }).blur(function(){
                    var mobileNum = $.trim($(_self.mobileInput).val());
                    if (mobileNum == "" || (_self.isPhone(mobileNum) == false)) {
                        _self.eError(_self.mobileInput, '请输入有效的手机号码');
                        return false;
                    }else{
                        _self.eSucceed(_self.mobileInput);
                        return true;
                    }
                });
                if(_self.isRepeatInput){
                    var mobileRepeatInput = '#mobileRepeat';
                    $(mobileRepeatInput).focus(function(){
                        _self.eFocus(mobileRepeatInput);
                    }).blur(function(){
                        var mobileNum = $.trim($(_self.mobileInput).val()),
                            mobileRepeatNum = $.trim($(mobileRepeatInput).val());
                        if(!_self.isEqual(mobileNum, mobileRepeatNum)){
                            _self.eError(mobileRepeatInput, '两次输入不一致！');
                            return false;
                        }else{
                            _self.eSucceed(mobileRepeatInput);
                            return true;
                        }
                    });
                }
            }
        },
        isPhone: function(str){
            return new RegExp("^0?(13|15|18|14|17)[0-9]{9}$").test(str);
        },
        isEqual: function(val1, val2){
            return $.trim(val1) == $.trim(val2);
        },
        eFocus: function(inputEle, prompt){
            var mobileInput = $(inputEle),
                mobileSucceed = $(inputEle +'_succeed'),
                mobileError = $(inputEle +'_error');
            mobileInput.removeClass('input_error');
            mobileSucceed.removeClass();
            if(prompt = '' || prompt == undefined){
                mobileError.removeClass();
            }else{
                mobileError.removeClass().addClass('error').html(prompt);
            }
        },
        eError: function(inputEle, prompt){
            var mobileInput = $(inputEle),
                mobileSucceed = $(inputEle +'_succeed'),
                mobileError = $(inputEle +'_error');
            mobileInput.removeClass().addClass('input_error');
            mobileSucceed.removeClass();
            mobileError.removeClass().addClass('error').html(prompt);
        },
        eSucceed: function(inputEle, prompt){
            var mobileInput = $(inputEle),
                mobileSucceed = $(inputEle +'_succeed'),
                mobileError = $(inputEle +'_error');
            mobileInput.removeClass();
            mobileSucceed.removeClass().addClass('focus').html(prompt);
            mobileError.removeClass().html('');
        }
    };

    return ValidateMobile;
});