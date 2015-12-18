require(['../../config'],function(){
    require(['common']);
    require(['jquery', 'routing'],function($, routing){
       	var eles = $('.main-personal-account>ul>li');
       	var btns = eles.find('.edit'),
       		cons = eles.find('.con');

       	btns.on('click', function(){
       		var i = btns.index(this);
       		if(eles.eq(i).hasClass('active')){
                eles.eq(i).removeClass('active');
                cons.eq(i).slideToggle();
                btns.eq(i).html('编辑');
            }else{
                eles.removeClass('active').eq(i).addClass('active');
                cons.slideUp().eq(i).slideDown();
                btns.html('编辑').eq(i).html('收起');
            }
       	});

        var withdrawContinue = $('#withdraw_continue');
        withdrawContinue.on('click', function(){
            $('.step1').hide();
            $('.step2').show();
        });
   	});
    //修改密码（交互）
    require(['jquery', 'validate'], function($, rpaValidate){
        $("#pwd").RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd);
        $("#pwdRepeat").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat);
        $('#pwd_save').on('click', function(){
            $("#pwd").RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd, true);
            $("#pwdRepeat").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat, true);
            savePwd();
        });
        var curPwdInput = $('#curPwd'),
            curPwdSucceed = $('#curPwd_succeed'),
            curPwdError = $('#curPwd_error');

        function eError(prompt){
            curPwdInput.removeClass().addClass('input_error');
            curPwdSucceed.removeClass();
            curPwdError.removeClass().addClass('error').html(prompt);
        }
        function savePwd(){
            var str = $('#curPwd').val();
            str = $.trim(str);
            if (str == "") {
                eError('请输入当前密码');
                return false;
            }
            $.ajax({
                type : "POST",
                url : Routing.generate('_profile_changepwd'),
                contentType : "application/x-www-form-urlencoded; charset=utf-8",
                data: { curPwd: $("#curPwd").val(), pwd: $("#pwd").val(), pwdRepeat: $("#pwdRepeat").val(), csrf_token: $("#csrf_token").val()},

//                success : function(data) {
//                    var data = { result: false};
//                    if (data.result) {
//                    }else{
//                        $('#curPwd').removeClass().addClass('input_error');
//                        $('#curPwd_succeed').removeClass();
//                        $('#curPwd_error').removeClass().addClass('error').html('您当前密码输入错误');
//                    }
//                }

                success : function(msg) {
                    if(msg != null && data.trim() != ''){
                        if(msg == 'Need login'){
                            // 跳转到登录画面

                        }else if(msg == 'Access Forbidden'){
                            // 跳转到账户设置首页画面

                        }else{
                            alert(msg);
                        }
                    }
                }
            });
        }
//        function eFocus(prompt){
//            curPwdInput.removeClass();
//            curPwdError.removeClass().html(prompt);
//        }
//        curPwdInput.bind('focus',
//            function() {
//                var str = $('#curPwd').val();
//                eFocus('');
//            }).bind('blur',
//            function() {
//                var str = $('#curPwd').val();
//                str = $.trim(str);
//                if (str == "" || (isEmail(str) == false)) {
//                    eError('请输入当前密码');
//                    return false;
//                }
//            });
    });
    //修改手机（交互）
    require(['jquery', 'mobile'],function($, mobile){
        var mobileSave = $('#mobile_save'),
            mobileSucceed = $('#mobile_succeed'),
            mobileError = $('#mobile_error');
        var mobileInput = '#mobile';
        var seconds, s;
        function countdown(){
            if (seconds > 0) {
                seconds = seconds - 1;
                var second = Math.floor(seconds % 10);             // 计算秒
                $("#second").html(second);
            } else {
                $("#second").html('10');
                $('#send_code').removeClass('disabled');
                mobileSucceed.removeClass();
                clearInterval(s);
            }
        }
        function reSendCode(code){
            var sendCode = $('#send_code'),
                message = $(".message");
            code = $.trim(code);
            if (code == "" || (obj.isPhone(code) == false)) {
                obj.eError(mobileInput, '请输入有效的手机号码');
                return false;
            }
            //交互模拟结果数据
            var data = 1;
            if(data==1){
                seconds = 10;
                s = setInterval(countdown, 1000);
                obj.eSucceed(mobileInput, '<strong id="second">10</strong>秒');
                sendCode.addClass('disabled').html('重新发送');
                sendCode.onclick = null;
            }else if(data==2){
                code = $('#email').val();
                //var url = "{{ path('_user_activeEmail',{'email': "email" }) }}";
                var url = "http://www.91jili.com";
                url = url.replace('email',encodeURIComponent(code));
                var html = "<p>邮箱地址未激活，请重新<a href='"+url+"'>激活</a></p>";
                message.show().html(html);
            }else{
                $('#email').val('');
            }
        }
        var obj = new mobile({ mobileInput: mobileInput, isSendCode: false, isRepeatInput: false, isFocusPrompt: true});
        var sendCode = $('#send_code');
        sendCode.on('click', function(){
            var code = $('#mobile').val().trim();
            if(sendCode.hasClass('disabled')){
                return false;
            }else{
                reSendCode(code);
            }
        });
        mobileSave.on('click', function(){
            var code = $('#mobile').val().trim();
            if (code == "" || (obj.isPhone(code) == false)) {
                obj.eError(mobileInput, '请输入有效的手机号码');
                return false;
            }
            //ajax提交
        });
    });
    //绑定、退订、注销
    require(['jquery'],function($){
        var weiboSave = $('#weibo_save');
        weiboSave.on('click', function(){
            saveWeibo();
        });
        function saveWeibo(){
            var isBding = true;
            if(isBding){
                weiboSave.hide();
                $('#weibo_state').text('目前状态：已绑定');
            }else{
                $('#weibo_state').text('目前状态：未绑定');
            }
        }

        var subscribeSave = $('#subscribe_save');
        subscribeSave.on('click', function(){
            saveSubscribe();
        });
        function saveSubscribe(){
            var isSubscribe = true;
            var subscribe = $('#subscribe');
            console.log(subscribe.prop('checked'));
            isSubscribe = subscribe.prop('checked');
        }

        var reasons = $('.reason-options'),
            withdrawSave = $('#withdraw_save');
        withdrawSave.on('click', function(){
            saveWithdraw();
        });
        function saveWithdraw(){
            var checked = [], len = reasons.find('input:checked').length;
            for(var i = 0; i < len; i++){
                checked[i] = reasons.find('input:checked').eq(i).val();
            }
        }
    });
});