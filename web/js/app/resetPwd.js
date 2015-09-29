require(['../config'],function(){
    require(['jquery', 'validate'], function($, rpaValidate){
        var emailInput = $('#email'),
            emailSucceed = $('#email_succeed'),
            emailError = $('#email_error');
        function isEmail(str){
            return new RegExp("^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$").test(str);
        }
        function eFocus(prompt){
            emailInput.removeClass();
            emailError.removeClass().html(prompt);
        }
        function eError(prompt){
            emailInput.removeClass().addClass('input_error');
            emailSucceed.removeClass();
            emailError.removeClass().addClass('error').html(prompt);
        }
        function eSucceed(prompt){
            emailInput.removeClass();
            emailSucceed.removeClass().addClass('focus').html(prompt);
            emailError.removeClass().html('');
        }
        var seconds, s;
        function countdown(){
            if (seconds > 0) {
                seconds = seconds - 1;
                var second = Math.floor(seconds % 10);             // 计算秒
                $("#second").html(second);
            } else {
                $("#second").html('10');
                $('#sendEmail').removeClass('disabled');
                emailSucceed.removeClass();
                clearInterval(s);
            }
        }
        function reSendEmail(email){
            var sendEmail = $('#sendEmail'),
                message = $(".message");
            email = $.trim(email);
            if (email == "" || (isEmail(email) == false)) {
                eError('请输入有效的邮箱地址');
                return false;
            }
//            $.ajax({
//                url: "{{ path('_user_reset') }}?email="+$('#email').val(),
//                post: "GET",
//                success:function(data){
                    var data = 1;
                    if(data==1){
                        $('#email').val('');
                        seconds = 10;
                        s = setInterval(countdown, 1000);
                        eSucceed('重置密码邮件已经发送至您的邮箱，<strong id="second">10</strong>秒后可重新发送。');
                        sendEmail.addClass('disabled').html('重新发送');
                        sendEmail.onclick = null;
                    }else if(data==2){
                        var email = $('#email').val();
                        //var url = "{{ path('_user_activeEmail',{'email': "email" }) }}";
                        var url = "http://www.91jili.com";
                        url = url.replace('email',encodeURIComponent( email));
                        var html = "<p>邮箱地址未激活，请重新<a href='"+url+"'>激活</a></p>";
                        message.show().html(html);
                    }else{
                        $('#email').val('');
                    }
//                }
//            });
        }
        var sendEmail = $('#sendEmail');
        sendEmail.on('click', function(){
            var email = $('#email').val();
            if(sendEmail.hasClass('disabled')){
                return;
            }else{
                reSendEmail(email);
            }
        });
        emailInput.bind('focus',
            function() {
                var str = $('#email').val();
                eFocus('');
            }).bind('blur',
            function() {
                var str = $('#email').val();
                str = $.trim(str);
                if (str == "" || (isEmail(str) == false)) {
                    eError('请输入有效的邮箱地址');
                    return false;
                }
            });
        var savePwd = $('#savePwd');
        savePwd.on('click', function(){
            $("#pwd").bind("keyup", function(){ rpaValidate.func.pwdStrength(); }).RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd, true);
            $("#pwdRepeat").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat, true);
        });
        $("#pwd").bind("keyup", function(){ rpaValidate.func.pwdStrength(); }).RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd);
        $("#pwdRepeat").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat);
    });
});