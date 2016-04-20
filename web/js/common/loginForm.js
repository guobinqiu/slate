define(['jquery'],function($){
    function isEmail(str){
        return new RegExp("^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$").test(str);
    }
    function isNull(obj, isPrompt){
        var str = $(obj.ele).val();
        str = $.trim(str);
        if (str == "" || typeof str != "string") {
            if(isPrompt){
                eError(obj.ele, obj.prompt.isNull);
            }
            return false;
        }
        return true;
    }
    function eFocus(ele,prompt){
        $(ele).removeClass();
        $(ele+'_succeed').removeClass();
        $(ele+'_error').removeClass().addClass('focus').html(prompt);
    }
    function eError(ele,prompt){
        $(ele).removeClass().addClass('input_error');
        $(ele+'_succeed').removeClass();
        $(ele+'_error').removeClass().addClass('error').html(prompt);
    }
    function eSucceed(ele,prompt){
        $(ele).removeClass();
        $(ele+'_succeed').removeClass().addClass('succeed').html(prompt);
        $(ele+'_error').removeClass().html('');
    }
    function checkInputEmail(obj){
        if(obj.type && obj.type == 'email' && !isEmail($(obj.ele).val().trim())){
            eError(obj.ele, obj.prompt.isFormat);    
            return false;
        }
        return true;
    }
    function checkInput(obj, def){
        if(def){
            if(isNull(obj, true)) {
                return checkInputEmail(obj);
            }
        }else{
            $(obj.ele).bind('focus', function() {
                eFocus(obj.ele,'');
            }).bind('blur', function() {
                if(isNull(obj, true)){
                   return checkInputEmail(obj);
                }
            });
        }
    }
    var pwd = {
        ele: '#pwd',
        prompt: {
            isNull: '请输入您的密码',
            isFocus: '请输入您的密码'
        }
    }, email = {
        ele: '#email',
        prompt: {
            isNull: '请输入邮箱地址',
            isFormat: '邮箱地址格式不正确'
        },
        type: 'email'
    };

    checkInput(email);
    checkInput(pwd);
    var submitBtn = $("#submit_button");
    submitBtn.on('click', function(e){
        if(checkInput(email, true) && checkInput(pwd, true)){
            submitBtn.submit();
        }else{
            e.preventDefault();
        }
    });

    var $emailError = $("#email_error"), $pwdError = $("#pwd_error");
    $emailError.add($pwdError).on('click', function(){
        $(this).addClass('fade');
    });
    var errorCode = $('#error_code').val();
    if(errorCode != undefined){
        $emailError.html(errorCode).addClass('error').attr('display', 'block');
    };
});