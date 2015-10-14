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
    require(['jquery', 'validate'], function($, validate){
        $.extend(validate.func, {
            regValidate : function() {
                $("#regName").RPAValidate(validate.prompt.regName, validate.func.regName, true);
                $("#email").RPAValidate(validate.prompt.email, validate.func.email, true);
                $("#pwd").RPAValidate(validate.prompt.pwd, validate.func.pwd, true);
                $("#pwdRepeat").RPAValidate(validate.prompt.pwdRepeat, validate.func.pwdRepeat, true);
                $("#authcode").RPAValidate(validate.prompt.authCode, validate.func.authCode, true);
                return validate.func.FORM_submit([ "#regName", "#email", "#pwd", "#pwdRepeat","#authcode" ]);
            }
        });

        $("#regName").RPAValidate(validate.prompt.regName, validate.func.regName);
        $("#email").RPAValidate(validate.prompt.email, validate.func.email);
        $("#pwd").bind("keyup", function(){ validate.func.pwdStrength(); }).RPAValidate(validate.prompt.pwd, validate.func.pwd);
        $("#pwdRepeat").RPAValidate(validate.prompt.pwdRepeat, validate.func.pwdRepeat);
        $("#authcode").RPAValidate(validate.prompt.authCode, validate.func.authCode);
        function checkReadMe() {
            var  readme = $("#readme"),
                protocolError = $("#protocol_error");
            console.log((readme.prop("checked") == "checked") +"------是否阅读用户协议--------"+(readme.prop("checked") == true));
            if (readme.prop("checked") == "checked" || readme.prop("checked") == true) {
                protocolError.removeClass();
                return true;
            } else {
                protocolError.removeClass().addClass("error").html("请确认已阅读会员协议");
                return false;
            }
        }
        function validateRegName() {
            var regName = $("#regName"),
                regNameError = $("#regName_error");
            var loginName = regName.val();
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
        var isSubmit = false;
        function reg() {
            if (isSubmit) {
                return;
            }
            var agreeProtocol = checkReadMe();
            var regNameOk = validateRegName();
            var passed = false;

            console.log($("#signup_form").serialize());
            //console.log(agreeProtocol);
            passed = validate.func.regValidate() && regNameOk && agreeProtocol;
            //console.log(passed);
            if (passed) {
                $("#submit_button").attr({
                    "disabled" : "disabled"
                }).removeClass().addClass("btn-img btn-regist wait-btn");
//            $.ajax({
//                type : "POST",
//                url : "../register/regService?r=" + Math.random() + "&"
//                    + location.search.substring(1),
//                contentType : "application/x-www-form-urlencoded; charset=utf-8",
//                data : $("#signup_form").serialize(),
//                success : function(result) {
//                    var result = {};
//                    if (result) {
//                        var obj = eval(result);
//                        if (obj.info) {
//                            //showMessage(obj.info);
//                            console.log(obj.info);
//                            verc();
//                            $("#registsubmit").removeAttr("disabled").removeClass()
//                                .addClass("btn-img btn-regist");
//                            isSubmit = false;
//                            return;
//                        }
//                        if (obj.noAuth) {
//                            verc();
//                            window.location = obj.noAuth;
//                            return;
//                        }
//                        if (obj.success == true) {
//                            window.location = obj.dispatchUrl;
//                        }
//                    }
//                }
//            });
            } else {
                $("#submit_button").removeAttr("disabled").removeClass().addClass(
                    "btn-img btn-regist");
                isSubmit = false;
            }
        }
        $('#submit_button').on('click', function(){
            reg();
        });
    });
});