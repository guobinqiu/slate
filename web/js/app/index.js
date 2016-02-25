require(['../config'],function(){
    require(['common']);
    require(['feedbackForm']);
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
                // $("#authcode").RPAValidate(validatePrompt.authCode, validate.func.authCode, true);
                // return validate.func.FORM_submit([ "#email", "#pwd", "#authcode" ]);
                return validate.func.FORM_submit([ "#email", "#pwd"]);
            }
        });

        $("#email").RPAValidate(validatePrompt.email, validate.func.email);
        $("#pwd").RPAValidate(validatePrompt.pwd, validate.func.pwd);
        // $("#authcode").RPAValidate(validatePrompt.authCode, validate.func.authCode);

        var isSubmit = false;
        var submitBtn = $("#submit_button");
        var errors = ['#email_error', '#pwd_error'];
        // var errors = ['#email_error', '#pwd_error', '#authcode_error'];
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
        // console.log('服务器返回的错误代码：'+$('#error_code').val());
        var errorCode = $('#error_code').val();
        if(errorCode != undefined){
            $('#email_error').html(errorCode).addClass('error').attr('display', 'block');
        }
        submitBtn.on('click', function(){
            // login();
            $('#form1').submit();
        });

        // 点击登录按钮，呈现输入状态
        var logFoc = $("a[title='登录']");
        var surList = $("a[title='问卷列表']");
        logFoc.add(surList).click(function(){
            $("#email").focus(); 
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
    require(['jquery', 'jqueryCookie'], function($){
        //feedback
        function shouldShow(){
            var vp  = $.cookie('ShoudShowDialog92');
            if (vp == undefined || vp == 1) {
                return true;
            } else {
                return false;
            }
        }
        $('.closeTag').on('click', function(){
            $.cookie('ShoudShowDialog92', 0, { expires: 10000, path: '/' });
            $('.fbCon').hide();
        });
        $('.closeBtn').on('click', function(){
            $(".unfdWrap").animate({right: '-420px'}, 300);
            $(".fdWrap").animate({right: '0'}, 300);
        });
        $('.fdWrap').on('click', function(){
            $(".fdWrap").animate({right: '-130px'}, 300);
            $(".unfdWrap").animate({right: '0'}, 300);
        });
        if(shouldShow()){
            $('.fbCon').show();
        }else{
            $('.fbCon').hide();
        }
    });
});