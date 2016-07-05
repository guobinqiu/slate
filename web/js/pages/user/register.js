/*-------------------
引用jquery.js, routing.js, validate.js
-------------------*/
$(function(){
    $('#changeCode').on('click', function(){
        $('#verificationImg').prop('src', Routing.generate("_user_captcha") + '?r=' + 100000*Math.random());
    });
    rpaValidate.prompt.pwdRepeat.elements.pwd = "#signup_password_first";
    $.extend(rpaValidate.func, {
        regValidate : function() {
            $("#signup_nickname").RPAValidate(rpaValidate.prompt.regName, rpaValidate.func.regName, true);
            $("#signup_email").RPAValidate(rpaValidate.prompt.email, rpaValidate.func.email, true);
            $("#signup_password_first").RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd, true);
            $("#signup_password_second").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat, true);
            $("#signup_captcha").RPAValidate(rpaValidate.prompt.authCode, rpaValidate.func.authCode, true);
            return rpaValidate.func.FORM_submit([ "#signup_nickname", "#signup_email", "#signup_password_first", "#signup_password_second","#signup_captcha" ]);
        }
    });
    var pwdStrengthOptions = { pwdStrength: $("#pwdStrength"), pwdError: $("#signup_password_first_error"), value: $.trim($("#signup_password_first").val())}
    $("#signup_nickname").RPAValidate(rpaValidate.prompt.regName, rpaValidate.func.regName);
    $("#signup_email").RPAValidate(rpaValidate.prompt.email, rpaValidate.func.email);
    $("#signup_password_first").bind("keyup", function(){ rpaValidate.func.pwdStrength(pwdStrengthOptions); }).RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd);
    $("#signup_password_second").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat);
    $("#signup_captcha").RPAValidate(rpaValidate.prompt.authCode, rpaValidate.func.authCode);
    function checkReadMe() {
        var readme = $("#signup_agreement"),
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
        var regName = $("#signup_nickname"),
            regNameError = $("#signup_nickname_error");
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
    var signup_form = $('#signup_form');
    var backError = signup_form.find('li span>ul');
    if(backError.length >= 1){
        for(var i = 0; i < backError.length; i++){
            backError.eq(i).parent().siblings().removeClass();
            backError.eq(i).parent().removeClass().addClass('error');
        }
    }
    $('#submit_button').on('click', function(){
        if(reg()){
            signup_form.submit(); 
        }
    });

    //sinaWeibo and QQ quick login prompt
    var wbLog = $('.weibo-login');
    var qqLog = $('.qq-login');
    var wqClose = $('.quickLCon .closeBtn').add('.quickLCon .cancelBtn');
    var wbPCon = $('#wbLogCon');
    var qqPCon = $('#qqLogCon');
    wbLog.on('click', function(){
        wbPCon.show();
    });
    qqLog.on('click', function(){
        qqPCon.show();
    });
    wqClose.on('click', function(){
        wbPCon.add(qqPCon).hide();
    });
});
