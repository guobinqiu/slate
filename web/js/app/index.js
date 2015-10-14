require(['../config'],function(){
    require(['common']);
    require(['numScroll'], function(RPANumScroll){
        new RPANumScroll({ numScrollEle: '.digits b', config: {
            digitH : 30,
            num: 89754,
            animateTimer: 5000
        }});
    });
    require(['jquery', 'validate'], function($, validate){
        var validatePrompt = {
            email:{
                onFocus:"电子邮件就是今后您登录91问问的账号",
                succeed: "OK!",
                isNull: "请输入您的邮箱",
                error: {
                    beUsed: "该邮箱已被注册,请更换其它邮箱,或使用该<a href=\"signin.php?Email={#Email#}\" name=\"email_login_link\" class=\"more\">邮箱登录</a>",
                    badLength: "邮箱地址长度应在4-50个字符之间",
                    badFormat: "邮件地址格式不正确"
                }
            },
            pwd: {
                onFocus: "6-20位字符，建议由字母，数字和符号两种以上组合",
                succeed: "OK!",
                isNull: "请输入密码",
                error: {
                    badLength: "密码长度只能在6-20位字符之间",
                    badFormat: "密码只能由英文、数字及标点符号组成",
                    simplePwd: "该密码比较简单，建议您更改"
                },
                onFocusExpand: function() {
                    $("#pwdStrength").hide();
                }
            },
            authCode: {
                onFocus: "请输入验证码",
                succeed: "OK!",
                isNull: "请输入验证码",
                error: {
                    badMsg: "验证码不正确"
                }
            }
        };
        $.extend(validate.func, {
            loginValidate : function() {
                $("#email").RPAValidate(validatePrompt.email, validate.func.email, true);
                $("#pwd").RPAValidate(validatePrompt.pwd, validate.func.pwd, true);
                $("#authcode").RPAValidate(validatePrompt.authCode, validate.func.authCode, true);
                return validate.func.FORM_submit([ "#email", "#pwd", "#authcode" ]);
            }
        });

        $("#email").RPAValidate(validatePrompt.email, validate.func.email);
        $("#pwd").RPAValidate(validatePrompt.pwd, validate.func.pwd);
        $("#authcode").RPAValidate(validatePrompt.authCode, validate.func.authCode);

        var isSubmit = false;
        var submitBtn = $("#submit_button");
        var errors = ['#email_error', '#pwd_error', '#authcode_error'];
        function login() {
            var passed = false;
            for(var i = 0; i < errors.length; i++){
                $(errors[i]).removeClass('fade');
            }
            passed = validate.func.loginValidate();
            if (passed) {
                isSubmit = true;
                console.log('不容易呀，验证成功了');
            }else{
                isSubmit = false;
                for(var j = 0; j < errors.length; j++){
                    $(errors[j]).on('click', function(){
                        $(this).addClass('fade');
                    });
                }
            }
        }
        submitBtn.on('click', function(){
            login();
        });
    });
    require(['jquery'], function($){
        var $window = $(window),
            win_height_padded = $window.height() * 1.1;

        $window.on('scroll', revealOnScroll);

        function revealOnScroll() {
            var scrolled = $window.scrollTop(),
                win_height_padded = $window.height() * 1.1;

            // Showed...
            $(".party:not(.animateing)").each(function() {
                var $this = $(this),
                    offsetTop = $this.offset().top;

                if (scrolled + win_height_padded > offsetTop) {
                    if ($this.data('timeout')) {
                        window.setTimeout(function() {
                            $this.addClass('animateing ' + $this.data('animation'));
                        }, parseInt($this.data('timeout'), 10));
                    } else {
                        $this.addClass('animateing ' + $this.data('animation'));
                    }
                }
            });
        }

        revealOnScroll();
    });
});