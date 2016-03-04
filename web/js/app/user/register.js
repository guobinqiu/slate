require(['../../config'],function(){
    require(['jquery'], function(){
        var expandBtn = $('.expand-btn'),
        expandCon = $('.expand-con');

        $.each(expandCon, function(i, e){
            expandBtn.eq(i).hover(function(){
                expandCon.eq(i).show();
            }, function(){
                expandCon.eq(i).hide();
            });
        });
    });
    require(['jquery', 'validate', 'routing'], function($, validate){
        $('#changeCode').on('click', function(){
            $('#verificationImg').prop('src', Routing.generate("_user_captcha") + '?r=' + 100000*Math.random());
        });
        validate.prompt.pwdRepeat.elements.pwd = "#signup_password_first";
        $.extend(validate.func, {
            regValidate : function() {
                $("#signup_nickname").RPAValidate(validate.prompt.regName, validate.func.regName, true);
                $("#signup_email").RPAValidate(validate.prompt.email, validate.func.email, true);
                $("#signup_password_first").RPAValidate(validate.prompt.pwd, validate.func.pwd, true);
                $("#signup_password_second").RPAValidate(validate.prompt.pwdRepeat, validate.func.pwdRepeat, true);
                $("#signup_captcha").RPAValidate(validate.prompt.authCode, validate.func.authCode, true);
                return validate.func.FORM_submit([ "#signup_nickname", "#signup_email", "#signup_password_first", "#signup_password_second","#signup_captcha" ]);
            }
        });
        var pwdStrengthOptions = { pwdStrength: $("#pwdStrength"), pwdError: $("#signup_password_first_error"), value: $("#signup_password_first").val().trim()}
        $("#signup_nickname").RPAValidate(validate.prompt.regName, validate.func.regName);
        $("#signup_email").RPAValidate(validate.prompt.email, validate.func.email);
        $("#signup_password_first").bind("keyup", function(){ validate.func.pwdStrength(pwdStrengthOptions); }).RPAValidate(validate.prompt.pwd, validate.func.pwd);
        $("#signup_password_second").RPAValidate(validate.prompt.pwdRepeat, validate.func.pwdRepeat);
        $("#signup_captcha").RPAValidate(validate.prompt.authCode, validate.func.authCode);
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
            var loginName = regName.val().trim();
            if (validate.rules.isNull(loginName) || loginName == '') {
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

            passed = validate.func.regValidate() && regNameOk && agreeProtocol;
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
    });
});
