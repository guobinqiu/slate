require(['../config'],function(){
    require(['expand']);
    require(['jquery', 'validate'], function($, rpaValidate){
        $.extend(rpaValidate.func, {
            regValidate : function() {
                $("#regName").RPAValidate(rpaValidate.prompt.regName, rpaValidate.func.regName, true);
                $("#email").RPAValidate(rpaValidate.prompt.email, rpaValidate.func.email, true);
                $("#pwd").RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd, true);
                $("#pwdRepeat").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat, true);
                $("#authcode").RPAValidate(rpaValidate.prompt.authCode, rpaValidate.func.authCode, true);
                return rpaValidate.func.FORM_submit([ "#regName", "#email", "#pwd", "#pwdRepeat","#authcode" ]);
            }
        });
        var isSubmit = false;
        $("#regName").RPAValidate(rpaValidate.prompt.regName, rpaValidate.func.regName);
        $("#email").RPAValidate(rpaValidate.prompt.email, rpaValidate.func.email);
        $("#pwd").bind("keyup", function(){ rpaValidate.func.pwdStrength(); }).RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd);
        $("#pwdRepeat").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat);
        $("#authcode").RPAValidate(rpaValidate.prompt.authCode, rpaValidate.func.authCode);
        function verc() {
            $("#JD_Verification1").click();
        }
        function checkReadMe() {
            var  readme = $("#readme"),
                protocolError = $("#protocol_error");
            console.log((readme.prop("checked") == "checked") +"--------------"+(readme.prop("checked") == true));
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
            if (isSubmit) {
                return;
            }

            var agreeProtocol = checkReadMe();
            var regNameOk = validateRegName();
            var passed = false;

console.log($("#signup_form").serialize());
            //console.log(agreeProtocol);
            passed = rpaValidate.func.regValidate() && regNameOk && agreeProtocol;
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