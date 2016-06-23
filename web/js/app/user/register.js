require(['/js/config.js'],function(){
    require(['jquery', 'validate', 'routing'], function($, validate){
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
        validate.prompt.pwdRepeat.elements.pwd = "#signup_password_first";
        $.extend(validate.func, {
            regValidate : function() {
                $(signupF.nickname).RPAValidate(validate.prompt.regName, validate.func.regName, true);
                $(signupF.email).RPAValidate(validate.prompt.email, validate.func.email, true);
                $(signupF.pwdF).RPAValidate(validate.prompt.pwd, validate.func.pwd, true);
                $(signupF.pwdS).RPAValidate(validate.prompt.pwdRepeat, validate.func.pwdRepeat, true);
                $(signupF.captcha).RPAValidate(validate.prompt.authCode, validate.func.authCode, true);
                return validate.func.FORM_submit([ signupF.nickname, signupF.email, signupF.pwdF, signupF.pwdS,signupF.captcha ]);
            }
        });
        var pwdStrengthOptions = { pwdStrength: $("#pwdStrength"), pwdError: $(signupF.pwdF+"_error"), value: $.trim($(signupF.pwdF).val())}
        $(signupF.nickname).RPAValidate(validate.prompt.regName, validate.func.regName);
        $(signupF.email).RPAValidate(validate.prompt.email, validate.func.email);
        $(signupF.pwdF).bind("keyup", function(){ validate.func.pwdStrength(pwdStrengthOptions); }).RPAValidate(validate.prompt.pwd, validate.func.pwd);
        $(signupF.pwdS).RPAValidate(validate.prompt.pwdRepeat, validate.func.pwdRepeat);
        $(signupF.captcha).RPAValidate(validate.prompt.authCode, validate.func.authCode);
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
            if (validate.rules.isNull(loginName) || loginName == '') {
                regName.val("");
                regName.attr({
                    "class": "highlight2"
                });
                regNameError.html("请输入用户名").attr({
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
                }).removeClass();
                return true;
            } else {
                $("#submit_button").removeAttr("disabled").removeClass();
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
                // tips.addClass('active');
            }
        });

    });
    require(['landing']);
});
