/*-------------------
引用jquery.js, routing.js, validate.js
-------------------*/
$(function(){
    $('#changeCode').on('click', function(){
        $('#verificationImg').prop('src', Routing.generate("_user_captcha") + '?r=' + 100000*Math.random());
    });
    var signupF = {
        nickname: '#signup_nickname',
        email: '#signup_email',
        pwdF: '#signup_password_first',
        pwdS: '#signup_password_second',
        captcha: '#signup_captcha',
        agreement: '#signup_agreement'
    };
    rpaValidate.prompt.pwdRepeat.elements.pwd = "#signup_password_first";
    $.extend(rpaValidate.func, {
        regValidate : function() {
            $(signupF.nickname).RPAValidate(rpaValidate.prompt.regName, rpaValidate.func.regName, true);
            $(signupF.email).RPAValidate(rpaValidate.prompt.email, rpaValidate.func.email, true);
            $(signupF.pwdF).RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd, true);
            $(signupF.pwdS).RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat, true);
            $(signupF.captcha).RPAValidate(rpaValidate.prompt.authCode, rpaValidate.func.authCode, true);
            return rpaValidate.func.FORM_submit([ "#signup_nickname", "#signup_email", "#signup_password_first", "#signup_password_second","#signup_captcha" ]);
        }
    });
    var pwdStrengthOptions = { pwdStrength: $("#pwdStrength"), pwdError: $("#signup_password_first_error"), value: $.trim($("#signup_password_first").val())}
    $(signupF.nickname).RPAValidate(rpaValidate.prompt.regName, rpaValidate.func.regName);
    $(signupF.email).RPAValidate(rpaValidate.prompt.email, rpaValidate.func.email);
    $(signupF.pwdF).bind("keyup", function(){ rpaValidate.func.pwdStrength(pwdStrengthOptions); }).RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd);
    $(signupF.pwdS).RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat);
    $(signupF.captcha).RPAValidate(rpaValidate.prompt.authCode, rpaValidate.func.authCode);
    $(signupF.captcha).on('blur', function(){
        $("#signup_captcha_error").removeClass().addClass("null");
    });
    function checkReadMe() {
        var readme = $(signupF.agreement),
            protocolError = $("#protocol_error");
        if(readme.prop("checked") == "checked" || readme.prop("checked") == true) {
            protocolError.removeClass();
            return true;
        } else {
            protocolError.removeClass().addClass("error").html("请确认已阅读会员协议");
            return false;
        }
    }
    function validateRegName() {
        var regName = $(signupF.nickname),
            regNameError = $(signupF.nickname+"_error");
        var loginName = $.trim(regName.val());
        if (rpaValidate.rules.isNull(loginName) || loginName == '') {
            regName.val("");
            regName.attr({
                "class": "highlight2"
            });
            regNameError.html("请输入用户名").css('display', 'inline-block').attr({
                "class": "error"
            });
            return false;
        }
        return true;
    }
    function reg() {
        var agreeProtocol = checkReadMe();
        var regNameOk = validateRegName();
        var passed = false;

        passed = rpaValidate.func.regValidate() && regNameOk && agreeProtocol;
        if (passed) {
            $("#submit_button").attr({
                "disabled" : "disabled"
            }).removeClass().addClass("btn-img btn-regist wait-btn");
            return true;
        } else {
            $("#submit_button").removeAttr("disabled").removeClass().addClass(
                "btn-img btn-regist");
            
            return false;
        }
    }
    var tips = $('.tips');
    tips.removeClass('active');
    var lis = $('.login li'), inputs = lis.find('input[type!="checkbox"]'), labels = lis.find('label');
    inputs.each(function(i, e){
        if(!$(this).val()){
            labels.eq(i).show();
        }else{
            labels.eq(i).hide();
        }
        $(this).on('keydown', function(){
            labels.eq(i).hide();
        });
        $(this).on('keyup', function(){
            if(!$(this).val()){
                labels.eq(i).show();
            }else{
                labels.eq(i).hide();
            }
        }); 
    });
    var $emailError = $("#email_error"), $pwdError = $("#pwd_error");
    $('.register li span').on('click', function(){
        $(this).addClass('fade');
    });
    var signup_form = $('#signup_form');
    var backError = signup_form.find('li span>ul');
    if(backError.length >= 1){
        for(var i = 0; i < backError.length; i++){
            backError.eq(i).parent().siblings().removeClass();
            backError.eq(i).parent().removeClass().addClass('error');
        }
    }
    $('#submit_button').on('click', function(e){
        if(reg()){
            signup_form.submit(); 
         }else{
            e.preventDefault();
        }
    });

});