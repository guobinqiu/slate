define(['jquery'],function($){
    var ValidateAlipay = function(option){
        this.alipayInput = option.alipayInput;
        this.realName = option.realName;
        this.isRepeatInput = option.isRepeatInput;
        this.isFocusPrompt = option.isFocusPrompt;
        this.init();
    };

    ValidateAlipay.prototype = {
        init: function(){
            var _self = this;
            if(_self.isFocusPrompt){
                if(_self.alipayInput){
                    $(_self.alipayInput).focus(function(){
                        _self.eFocus(_self.alipayInput);
                    }).blur(function(){
                        var alipayNum = $.trim($(_self.alipayInput).val());
                        if (alipayNum == "" || (_self.isAlipay(alipayNum) == false)) {
                            _self.eError(_self.alipayInput, '请输入有效的支付宝账号');
                            return false;
                        }else{
                            _self.eSucceed(_self.alipayInput);
                            return true;
                        }
                    });
                }
                if(_self.realName){
                    $(_self.realName).focus(function(){
                        _self.eFocus(_self.realName);
                    }).blur(function(){
                        var name = $.trim($(_self.realName).val());
                        if (name == "" || (_self.isRealName(name) == false)) {
                            _self.eError(_self.realName, '请务必填写真实姓名与支付宝匹配，否则有可能兑换失败。');
                            return false;
                        }else{
                            _self.eSucceed(_self.realName);
                            return true;
                        }
                    });
                }
                if(_self.isRepeatInput){
                    var alipayRepeatInput = '#alipayRepeat';
                    $(alipayRepeatInput).focus(function(){
                        _self.eFocus(alipayRepeatInput);
                    }).blur(function(){
                        var alipayNum = $.trim($(_self.alipayInput).val()),
                            alipayRepeatNum = $.trim($(alipayRepeatInput).val());
                        if(!_self.isEqual(alipayNum, alipayRepeatNum)){
                            _self.eError(alipayRepeatInput, '两次输入不一致！');
                            return false;
                        }else{
                            _self.eSucceed(alipayRepeatInput);
                            return true;
                        }
                    });
                }
            }
        },
        isIdCard: function(str){
            return new RegExp("^[1-9]([0-9]{14}|[0-9]{16}([0-9]|X|x))$").test(str);
        },
        isAlipay: function(str){
            return new RegExp("^[A-Za-z0-9_\\-\\u4e00-\\u9fa5]+$").test(str);
        },
        isRealName: function(str){
            return new RegExp("^[A-Za-z\\u4e00-\\u9fa5]+$").test(str);
        },
        isEqual: function(val1, val2){
            return $.trim(val1) == $.trim(val2);
        },
        eFocus: function(inputEle, prompt){
            var alipayInput = $(inputEle),
                alipaySucceed = $(inputEle +'_succeed'),
                alipayError = $(inputEle +'_error');
            alipayInput.removeClass('input_error');
            if(prompt == '' || prompt == undefined){
                alipaySucceed.removeClass();
                alipayError.removeClass();
            }else{
                alipaySucceed.removeClass().addClass('focus').html(prompt);
                alipayError.removeClass().html('');
            }
        },
        eError: function(inputEle, prompt){
            var alipayInput = $(inputEle),
                alipaySucceed = $(inputEle +'_succeed'),
                alipayError = $(inputEle +'_error');
            alipayInput.removeClass().addClass('input_error');
            alipaySucceed.removeClass();
            alipayError.removeClass().addClass('error').html(prompt);
        },
        eSucceed: function(inputEle, prompt){
            var alipayInput = $(inputEle),
                alipaySucceed = $(inputEle +'_succeed'),
                alipayError = $(inputEle +'_error');
            alipayInput.removeClass();
            alipaySucceed.removeClass().addClass('focus').html(prompt);
            alipayError.removeClass().html('');
        }
    };

    return ValidateAlipay;
});