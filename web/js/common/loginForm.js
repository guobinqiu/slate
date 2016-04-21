define(['jquery'],function($){
    var LoginForm = function(options){
        this.email = options.email;
        this.pwd = options.pwd;
        this.auto = options.auto;
        this.run(this.auto);
    };
    LoginForm.prototype = {
        isEmail: function(str){
            return new RegExp("^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$").test(str);
        },
        isNull: function(obj, isPrompt){
            var _self = this;
            var str = $(obj.ele).val();
            str = $.trim(str);
            if (str == "" || typeof str != "string") {
                if(isPrompt){
                    _self.eError(obj.ele, obj.prompt.isNull);
                }
                return false;
            }
            return true;
        },
        eFocus: function(ele,prompt){
            $(ele).removeClass();
            $(ele+'_succeed').removeClass();
            $(ele+'_error').removeClass().addClass('focus').html(prompt);
        },
        eError: function(ele,prompt){
            $(ele).removeClass().addClass('input_error');
            $(ele+'_succeed').removeClass();
            $(ele+'_error').removeClass().addClass('error').html(prompt);
        },
        eSucceed: function(ele,prompt){
            $(ele).removeClass();
            $(ele+'_succeed').removeClass().addClass('succeed').html(prompt);
            $(ele+'_error').removeClass().html('');
        },
        checkInputEmail: function(obj){
            var _self = this;
            if(obj.type && obj.type == 'email' && !_self.isEmail($(obj.ele).val().trim())){
                _self.eError(obj.ele, obj.prompt.isFormat);    
                return false;
            }
            return true;
        },
        checkInput: function(obj, def){
            var _self = this;
            if(def){
                if(_self.isNull(obj, true)) {
                    return _self.checkInputEmail(obj);
                }else{
                    return false;
                }
            }else{
                $(obj.ele).bind('focus', function() {
                    _self.eFocus(obj.ele,'');
                }).bind('blur', function() {
                    if(_self.isNull(obj, true)){
                       return _self.checkInputEmail(obj);
                    }
                });
            }
        },
        run: function(auto){
            var _self = this;
            if(auto){
                return _self.checkInput(_self.email, true)&&_self.checkInput(_self.pwd, true);
            }else{
                _self.checkInput(_self.email);
                _self.checkInput(_self.pwd);
            }
        }
    };
    return LoginForm;
});