define(['jquery'],function($){
    function pwdLevel(value) {
        var pattern_1 = /^.*([\W_])+.*$/i;
        var pattern_2 = /^.*([a-zA-Z])+.*$/i;
        var pattern_3 = /^.*([0-9])+.*$/i;
        var level = 0;
        if (value.length > 10) {
            level++;
        }
        if (pattern_1.test(value)) {
            level++;
        }
        if (pattern_2.test(value)) {
            level++;
        }
        if (pattern_3.test(value)) {
            level++;
        }
        if (level > 3) {
            level = 3;
        }
        return level;
    }

    var validateRegExp = {
        decmal: "^([+-]?)\\d*\\.\\d+$",
        // 浮点数
        decmal1: "^[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*$",
        // 正浮点数
        decmal2: "^-([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*)$",
        // 负浮点数
        decmal3: "^-?([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*|0?.0+|0)$",
        // 浮点数
        decmal4: "^[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*|0?.0+|0$",
        // 非负浮点数（正浮点数 + 0）
        decmal5: "^(-([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*))|0?.0+|0$",
        // 非正浮点数（负浮点数 +
        // 0）
        intege: "^-?[1-9]\\d*$",
        // 整数
        intege1: "^[1-9]\\d*$",
        // 正整数
        intege2: "^-[1-9]\\d*$",
        // 负整数
        num: "^([+-]?)\\d*\\.?\\d+$",
        // 数字
        num1: "^[1-9]\\d*|0$",
        // 正数（正整数 + 0）
        num2: "^-[1-9]\\d*|0$",
        // 负数（负整数 + 0）
        ascii: "^[\\x00-\\xFF]+$",
        // 仅ACSII字符
        chinese: "^[\\u4e00-\\u9fa5]+$",
        // 仅中文
        color: "^[a-fA-F0-9]{6}$",
        // 颜色
        date: "^\\d{4}(\\-|\\/|\.)\\d{1,2}\\1\\d{1,2}$",
        // 日期
        email: "^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$",
        // 邮件
        idcard: "^[1-9]([0-9]{14}|[0-9]{16}([0-9]|X|x))$",
        // 身份证
        ip4: "^(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)$",
        // ip地址
        letter: "^[A-Za-z]+$",
        // 字母
        letter_l: "^[a-z]+$",
        // 小写字母
        letter_u: "^[A-Z]+$",
        // 大写字母
        mobile: "^0?(13|15|18|14|17)[0-9]{9}$",
        // 手机
        notempty: "^\\S+$",
        // 非空
        // password: "^.*[A-Za-z0-9\\w_-]+.*$",
        password: "^.​*(?=.*​?[A-Za-z])(?=.​*?[0-9]).*​$",
        // 密码
        fullNumber: "^[0-9]+$",
        // 数字
        picture: "(.*)\\.(jpg|bmp|gif|ico|pcx|jpeg|tif|png|raw|tga)$",
        // 图片
        qq: "^[1-9]*[1-9][0-9]*$",
        // QQ号码
        rar: "(.*)\\.(rar|zip|7zip|tgz)$",
        // 压缩文件
        tel: "^[0-9\-()（）]{7,18}$",
        // 电话号码的函数(包括验证国内区号,国际区号,分机号)
        url: "^http[s]?:\\/\\/([\\w-]+\\.)+[\\w-]+([\\w-./?%&=]*)?$",
        // url
        username: "^[A-Za-z0-9_\\-\\u4e00-\\u9fa5]+$",
        // 用户名
        deptname: "^[A-Za-z0-9_()（）\\-\\u4e00-\\u9fa5]+$",
        // 单位名
        zipcode: "^\\d{6}$",
        // 邮编
        realname: "^[A-Za-z\\u4e00-\\u9fa5]+$",
        // 真实姓名
        companyname: "^[A-Za-z0-9_()（）\\-\\u4e00-\\u9fa5]+$",
        companyaddr: "^[A-Za-z0-9_()（）\\#\\-\\u4e00-\\u9fa5]+$",
        companysite: "^http[s]?:\\/\\/([\\w-]+\\.)+[\\w-]+([\\w-./?%&#=]*)?$"
    };
    //验证规则
    var validateRules = {
        isNull: function(str){
            return (str == "" || typeof str != "string");
        },
        betweenLength: function(str, _min, _max){
            return (str.length >= _min && str.length <= _max);
        },
        isUid: function(str){
            return new RegExp(validateRegExp.username).test(str);
        },
        fullNumberName: function(str){
            return new RegExp(validateRegExp.fullNumber).test(str);
        },
        isPwd: function(str){
            return /^.*(?=.*?[A-Za-z])(?=.*?[0-9]).*$/i.test(str);
        },
        isPwdRepeat: function(str1, str2) {
            return (str1 == str2);
        },
        isEmail: function(str) {
            return new RegExp(validateRegExp.email).test(str);
        },
        isMobile: function(str) {
            return new RegExp(validateRegExp.mobile).test(str);
        },
        checkType: function(element) {
            return (element.attr("type") == "checkbox" || element.attr("type") == "radio" || element.attr("rel") == "select");
        },
        simplePwd: function(str) {
            return pwdLevel(str) == 1;
        }
    };
    //验证文本
    var validatePrompt = {
        regName: {
            onFocus:"1-100位字符,支持汉字、字母、数字及\"-\"、\"_\"组合",
            succeed: "OK!",
            isNull: "请输入用户名",
            error: {
                beUsed: "该昵称已被使用，请重新输入。",
                badLength: "用户名长度只能在1-100位字符之间",
                badFormat: "用户名只能由中文、英文、数字及\"-\"、\"_\"组成",
                fullNumberName: "用户名不能是纯数字，请重新输入"
            },
            onFocusExpand: function() {
                //$("#morePinDiv").removeClass().addClass("intelligent-error hide");
            }
        },
        email:{
            onFocus:"电子邮件就是今后您登录91问问的账号",
            succeed: "OK!",
            isNull: "请输入您的邮箱",
            error: {
                beUsed: "该邮箱已被注册,请更换其它邮箱,或使用该<a href=\"signin.php?Email={#Email#}\" name=\"email_login_link\" class=\"more\">邮箱登录</a>",
                badLength: "邮箱地址长度应在4-50个字符之间",
                badFormat: "邮件地址格式不正确，请重新输入"
            },
            onFocusExpand: function() {
                //$("#morePinDiv").removeClass().addClass("intelligent-error hide");
            }
        },
        pwd: {
            elements: {
                regName: "#signup_nickname",
                pwdStrength: "#pwdStrength"
             },
            onFocus: "5-100位字符，密码至少包含1位字母和1位数字",
            succeed: "OK!",
            isNull: "请输入密码",
            error: {
                badLength: "密码长度只能在5-100位字符之间",
                badFormat: "密码至少包含1位字母和1位数字",
                simplePwd: "该密码比较简单，建议您更改"
            },
            onFocusExpand: function() {
                $("#pwdStrength").hide();
            }
        },
        pwdRepeat: {
            elements: {
                pwd: "#pwd"
             },
            onFocus: "请再次输入密码",
            succeed: "OK!",
            isNull: "请确认密码",
            error: {
                badLength: "密码长度只能在5-100位字符之间",
                badFormat2: "两次输入密码不一致",
                badFormat1: "密码至少包含1位字母和1位数字"
            }
        },
        protocol: {
            onFocus: "",
            succeed: "",
            isNull: "请先阅读并同意《用户注册协议》",
            error: ""
        },
        authCode: {
            onFocus: "请输入验证码",
            succeed: "OK!",
            isNull: "请输入验证码",
            error: {
                badMsg: "输入的验证码不正确"
            }
        },
        empty: {
            onFocus: "",
            succeed: "",
            isNull: "",
            error: ""
        }
    };
    //配置
    var validateSettings = {
        onFocus: {
            state: null,
            container: "_error",
            style: "focus",
            run: function(option, str, expands){
                if(!validateRules.checkType(option.element)){
                    option.element.removeClass(validateSettings.INPUT_style2).addClass(validateSettings.INPUT_style1);
                }
                option.succeedEle.removeClass(validateSettings.succeed.style);
                option.onFocusEle.removeClass().addClass(validateSettings.onFocus.style).html(str);
                if(expands) expands();
            }
        },
        isNull: {
            state: 0,
            container: "_error",
            style: "null",
            run: function(option, str){
                option.element.attr("sta", 0);
                if(!validateRules.checkType(option.element)){
                    if(str == ""){
                        option.element.removeClass(validateSettings.INPUT_style2).addClass(validateSettings.INPUT_style1);
                    }else{
                        option.element.removeClass(validateSettings.INPUT_style1).addClass(validateSettings.INPUT_style2);
                    }
                }
                option.succeedEle.removeClass(validateSettings.succeed.style);
                if(str == ""){
                    option.isNullEle.removeClass().addClass(validateSettings.isNull.style).html(str);
                }else{
                    option.isNullEle.removeClass().addClass(validateSettings.error.style).html(str);
                }
            }
        },
        error: {
            state: 1,
            container: "_error",
            style: "error",
            run: function(option, str) {
                option.element.attr("sta", 1);
                if (!validateRules.checkType(option.element)) {
                    option.element.removeClass(validateSettings.INPUT_style1).addClass(validateSettings.INPUT_style2);
                }
                option.succeedEle.removeClass(validateSettings.succeed.style);
                option.errorEle.removeClass().addClass(validateSettings.error.style).html(str);
            }
        },
        succeed: {
            state: 2,
            container: "_succeed",
            style: "succeed",
            run: function(option) {
                option.element.attr("sta", 2);
                option.errorEle.empty();
                if (!validateRules.checkType(option.element)) {
                    option.element.removeClass(validateSettings.INPUT_style1).removeClass(validateSettings.INPUT_style2);
                }
                option.succeedEle.addClass(validateSettings.succeed.style).html("OK!");
                option.errorEle.removeClass();
            }
        },
        INPUT_style1: "",
        INPUT_style2: "input_error"
    };
    //回调函数
    var validateFunction = {
        regName: function(option){
            var regName = option.value;
            if(validateRules.isNull(regName) || regName == ""){
                option.element.removeClass(validateSettings.INPUT_style2).removeClass(validateSettings.INPUT_style1);
                option.errorEle.removeClass().empty();
                return;
            }
            checkPinF(option);
        },
        email: function(option){
            checkEmail(option);
        },
        pwd: function(option){
            var str1 = option.value;
            var pwdStrength = $(option.prompts.elements.pwdStrength);
            var regName = $(option.prompts.elements.regName).val();
            if((regName != undefined) && (validateRules.isNull(regName) == false) && (regName != "") && (regName.trim()) ==str1){
                pwdStrength.hide();
                validateSettings.error.run(option, "您的密码与昵称重合度太高，有被盗风险，请换一个密码");
                return;
            }
            var format = validateRules.isPwd(option.value);
            var length = validateRules.betweenLength(option.value, 5, 100);
            var pwdStrengthOptions = { pwdStrength: pwdStrength, pwdError: option.errorEle, value: str1};
            pwdStrength.hide();
            if(!length){
                validateSettings.error.run(option, option.prompts.error.badLength);
            }else{
                if (!format) {
                    validateSettings.error.run(option, option.prompts.error.badFormat);
                }else{
                    validateSettings.succeed.run(option);
                    validateFunction.pwdStrength(pwdStrengthOptions);
//                if (validateRules.simplePwd(str1)) {
//                    $("#pwd_error").removeClass().addClass("focus");
//                    $("#pwd_error").empty().html(option.prompts.error.simplePwd);
//                    return;
//                }
                }
            } 
        },
        pwdRepeat: function(option) {
            var str1 = option.value;
            var str2 = $(option.prompts.elements.pwd).val().trim();
            var length = validateRules.betweenLength(option.value, 5, 100);
            var format2 = validateRules.isPwdRepeat(str1, str2);
            var format1 = validateRules.isPwd(str1);
            if (!length) {
                validateSettings.error.run(option, option.prompts.error.badLength);
            } else {
                if (!format1) {
                    validateSettings.error.run(option, option.prompts.error.badFormat1);
                } else {
                    if (!format2) {
                        validateSettings.error.run(option, option.prompts.error.badFormat2);
                    } else {
                        validateSettings.succeed.run(option);
                    }
                }
            }
        },
        authCode: function(option){
            if ($('#vcode').css("display") == "none") {
                option.element.attr("sta", validateSettings.succeed.state);
                return false;
            }
            var authCode = option.value;
            //模拟交互数据true：验证码正确，flase：验证码错误
            var flg = true;
            if (flg == false) {
                validateSettings.error.run(option, option.prompts.error.badMsg);
            }else{
                option.element.attr("sta", validateSettings.succeed.state);
            }
        },
        protocol: function(option) {
            if (option.element.attr("checked") == true) {
                option.element.attr("sta", validateSettings.succeed.state);
                option.errorEle.html("");
            } else {
                option.element.attr("sta", validateSettings.isNull.state);
                option.succeedEle.removeClass(validateSettings.succeed.style);
            }
        },
        pwdStrength: function(option) {
            var pwdStrength = option.pwdStrength,
                pwdError = option.pwdError,
                value = option.value;
            if (value.length >= 6 && validateRules.isPwd(value)) {
                pwdError.removeClass('focus');
                pwdError.empty();
                pwdStrength.show();
                var level = pwdLevel(value);
                switch (level) {
                    case 1:
                        pwdStrength.find('b').removeClass().eq(0).addClass("c1");
                        break;
                    case 2:
                        pwdStrength.find('b').removeClass();
                        pwdStrength.find('b').eq(0).addClass("c1");
                        pwdStrength.find('b').eq(1).addClass("c1");
                        break;
                    case 3:
                        pwdStrength.find('b').removeClass().addClass("c1");
                        break;
                    default:
                        break;
                }
            } else {
                pwdStrength.hide();
            }
        },
        checkGroup: function(elements) {
            for (var i = 0; i < elements.length; i++) {
                if (elements[i].checked) {
                    return true;
                }
            }
            return false;
        },
        checkSelectGroup: function(elements) {
            for (var i = 0; i < elements.length; i++) {
                if (elements[i].value == -1) {
                    return false;
                }
            }
            return true;
        },
        FORM_submit: function(elements) {
            var bool = true;
            for (var i = 0; i < elements.length; i++) {
                if ($(elements[i]).attr("sta") == 2) {
                    bool = true;
                } else {
                    bool = false;
                    break;
                }
            }
            return bool;
        }
    };

    // 主函数
    $.fn.RPAValidate = function(option, callback, def) {
            var ele = this;
            var id = ele.attr("id");
            var type = ele.attr("type");
            var rel = ele.attr("rel");
            var _onFocus = $("#" + id + validateSettings.onFocus.container);
            var _succeed = $("#" + id + validateSettings.succeed.container);
            var _isNull = $("#" + id + validateSettings.isNull.container);
            var _error = $("#" + id + validateSettings.error.container);
            if (def == true) {
                var str = ele.val().trim();
                var tag = ele.attr("sta");

                if (str == "" || str == "-1") {
                    validateSettings.isNull.run({
                            prompts: option,
                            element: ele,
                            isNullEle: _isNull,
                            succeedEle: _succeed
                        },
                        option.isNull);
                } else if (tag == 1 || tag == 2) {
                    return;
                } else {
                    callback({
                        prompts: option,
                        element: ele,
                        value: str,
                        errorEle: _error,
                        succeedEle: _succeed
                    });
                }
            } else {
                if (typeof def == "string") {
                    ele.val(def);
                }
                if (type == "checkbox" || type == "radio") {
                    if (ele.attr("checked") == true) {
                        ele.attr("sta", validateSettings.succeed.state);
                    }
                }
                switch (type) {
                    case "text":
                    case "email":
                    case "password":
                        ele.bind("focus",
                            function() {
                                var str = ele.val().trim();
                                if (str == def) {
                                    ele.val("");
                                }
                                validateSettings.onFocus.run({
                                        prompts: option,
                                        element: ele,
                                        value: str,
                                        onFocusEle: _onFocus,
                                        succeedEle: _succeed
                                    },
                                    option.onFocus, option.onFocusExpand);
                            }).bind("blur",
                            function() {
                                var str = ele.val().trim();
                                if (str == "") {
                                    ele.val(def);
                                }
                                if (validateRules.isNull(str)) {
                                    validateSettings.isNull.run({
                                            prompts: option,
                                            element: ele,
                                            value: str,
                                            isNullEle: _isNull,
                                            succeedEle: _succeed
                                        },
                                        '');
                                } else {
                                    callback({
                                        prompts: option,
                                        element: ele,
                                        value: str,
                                        errorEle: _error,
                                        isNullEle: _isNull,
                                        succeedEle: _succeed
                                    },
                                    option.succeed);
                                }
                            });
                        break;
                    default:
                        if (rel && rel == "select") {
                            ele.bind("change",
                                function() {
                                    var str = ele.val().trim();
                                    callback({
                                        prompts: option,
                                        element: ele,
                                        value: str,
                                        errorEle: _error,
                                        isNullEle: _isNull,
                                        succeedEle: _succeed
                                    });
                                })
                        } else {
                            ele.bind("click",
                                function() {
                                    callback({
                                        prompts: option,
                                        element: ele,
                                        errorEle: _error,
                                        isNullEle: _isNull,
                                        succeedEle: _succeed
                                    });
                                })
                        }
                        break;
                }
            }
        };

    // 检查用户名
    var nameOld;
    var nameState = false;
    var checkPin = -10;
    function checkPinF(option) {
        var pin = option.value;
        if (!validateRules.betweenLength(pin.replace(/[^\x00-\xff]/g, "**"), 1, 100)) {
            validateSettings.error.run(option, option.prompts.error.badLength);
            return false;
        }
        if (!validateRules.isUid(pin)) {
            validateSettings.error.run(option, option.prompts.error.badFormat);
            return;
        }
        if (validateRules.fullNumberName(pin)) {
            validateSettings.error.run(option, option.prompts.error.fullNumberName);
            return;
        }
        if (!nameState || nameOld != pin) {
            if (nameOld != pin) {
                nameOld = pin;
                option.errorEle.html("<em style='color:#999'>检验中……</em>");
//                $.getJSON("../validateuser/isPinEngaged?pin=" + escape(pin) + "&r=" + Math.random(),
//                    function(date) {
                        var date = {success: 0, morePin: {}};
                        checkPin = date.success;
                        if (date.success == 0) {
                            validateSettings.succeed.run(option);
                            nameState = true;
                        } else if (date.success == 2) {
                            validateSettings.error.run(option, "用户名包含了非法词");
                            nameState = false;
                        } else {
                            validateSettings.error.run(option, option.prompts.error.beUsed.replace("{1}", option.value));
                            nameState = false;
                        }
//                    });
            } else {
                if (checkPin == 2) {
                    validateSettings.error.run(option, "用户名包含了非法词");
                } else {
                    validateSettings.error.run(option, option.prompts.error.beUsed.replace("{1}", option.value));
                }
                nameState = false;
            }
        } else {
            validateSettings.succeed.run(option);
        }
    }
    // 检查邮件
    var emailOld;
    var emailState = false;
    function checkEmail(option) {
        var pin = option.value;
        if (!validateRules.betweenLength(pin.replace(/[^\x00-\xff]/g, "**"), 4, 50)) {
            validateSettings.error.run(option, option.prompts.error.badLength);
            return false;
        }
        if (!validateRules.isEmail(pin)) {
            validateSettings.error.run(option, option.prompts.error.badFormat);
            return false;
        }
        if (!emailState || emailOld != pin) {
            if (emailOld != pin) {
                emailOld = pin;
                option.errorEle.html("<em style='color:#999'>检验中……</em>");
//                $.getJSON("../validateuser/isPinEngaged?pin=" + escape(pin) + "&r=" + Math.random(),
//                    function(date) {
                //模拟交互数据0：可以提交，其他：不能提交
                var date = {success: 0, morePin: {}};
                if (date.success == 0) {
                    validateSettings.succeed.run(option);
                    emailState = true;
                } else {
                    validateSettings.error.run(option, option.prompts.error.beUsed.replace("{1}", option.value));
                    emailState = false;
                }
//                    });
            } else {
                validateSettings.error.run(option, option.prompts.error.beUsed.replace("{1}", option.value));
                emailState = false;
            }
        } else {
            validateSettings.succeed.run(option);
        }
    }

    var rpaValidate = {
        rules: validateRules,
        prompt: validatePrompt,
        setting: validateSettings,
        func: validateFunction
    };
    return rpaValidate;
});
