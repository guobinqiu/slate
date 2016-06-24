/*-------------------
引用jquery.js, common.js, scrollTop.js, numScroll.js, loginForm.js
-------------------*/
$(function(){
    new numScroll({ numScrollEle: '.digits b', config: {
        digitH : 30,
        num: 3688002,
        animateTimer: 5000
    }});
    /*---登录表单校验---*/
    var loginPwd = {
        ele: '#pwd',
        prompt: {
            isNull: '请输入您的密码',
            isFocus: '请输入您的密码'
        }
    }, loginEmail = {
        ele: '#email',
        prompt: {
            isNull: '请输入邮箱地址',
            isFormat: '邮箱地址格式不正确'
        },
        type: 'email'
    };
    new loginForm({pwd: loginPwd, email: loginEmail, auto: false});
    var submitBtn = $("#submit_button");
    submitBtn.on('click', function(e){
        var loginform = new loginForm({pwd: loginPwd, email: loginEmail, auto: true});
        if(loginform.run(true)){
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
    /*---点击登录按钮，呈现输入状态---*/
    var logFoc = $("a[title='登录']");
    var surList = $("a[title='问卷列表']");
    logFoc.add(surList).click(function(){
        $('#email').focus(); 
    });

    /*---sinaWeibo and QQ quick login prompt---*/
    var wbLog = $('.weibo-login');
    var qqLog = $('.qq-login');
    var wqClose = $('.quickLCon .closeBtn').add('.quickLCon .cancelBtn');
    var wbPCon = $('#wbLogCon');
    var qqPCon = $('#qqLogCon');
    wbLog.on('click', function(){
        wbPCon.show();
        qqPCon.hide();
    });
    qqLog.on('click', function(){
        qqPCon.show();
        wbPCon.hide();
    });
    wqClose.on('click', function(){
        wbPCon.add(qqPCon).hide();
    });

    /*---点击问卷列表提示先登录---*/
    $('.surUnlog').on('click', function(){
        $('.logFirst').fadeIn().delay(4000).fadeOut();
    });

    var $window = $(window),
        win_height_padded = $window.height() * 1.1;

    $window.on('scroll', revealOnScroll);

    function revealOnScroll() {
        var scrolled = $window.scrollTop(),
            win_height_padded = $window.height() * 1.1;

        /*---Showed...---*/
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

    /*---scroll down to certain position while click---*/
    $('.arrowScroll').on('click', function(e){
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top
        }, 800);
     });

    /*---resize coin background---*/
    window.onresize = function(event){
        $(".coinBack").css("width", $(window).width());
    }

});