require(['../../config'],function(){
    require(['jquery', 'validate', , 'routing'], function($, rpaValidate, routing){
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
                var second = Math.floor(seconds % 59);             // 计算秒
                $("#second").html(second);
            } else {
                if($("#secondNum").length > 0){
                    $("#second").html(parseInt($("#secondNum").html()));    
                }else{
                    $("#second").html('59');    
                }
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
            $.ajax({
               url: Routing.generate("_user_reset", {"email": $('#email').val().trim() }),
               post: "GET",
               success:function(data){
                    if(data==1){
                        $('#email').val('');
                        if($("#secondNum").length > 0){
                            seconds = parseInt($("#secondNum").val().trim());
                        }else{
                            seconds = 59;
                        }
                        s = setInterval(countdown, 1000);
                        eSucceed('重置密码邮件已经发送至您的邮箱，<strong id="second">'+ seconds +'</strong>秒后可重新发送。');
                        sendEmail.addClass('disabled').html('重新发送');
                        sendEmail.onclick = null;
                    }else if(data==2){
                        var email = $('#email').val().trim();
                        var url = Routing.generate("_user_activeEmail", {"email": email });
                        url = url.replace('email',encodeURIComponent( email));
                        eError('邮箱地址未激活，请重新<a href="'+url+'" class="activeEmail">激活</a>');
                    }else{
                        $('#email').val('');
                        eError(data);
                    }
               }
           });
        }
        var sendEmail = $('#sendEmail');
        sendEmail.on('click', function(){
            var email = $('#email').val().trim();
            if(sendEmail.hasClass('disabled')){
                return;
            }else{
                reSendEmail(email);
            }
        });
        emailInput.bind('focus',
            function() {
                var str = $('#email').val().trim();
                eFocus('');
            }).bind('blur',
            function() {
                var str = $('#email').val().trim();
                str = $.trim(str);
                if (str == "" || (isEmail(str) == false)) {
                    eError('请输入有效的邮箱地址');
                    return false;
                }
            });
        if($("#pwd").length > 1){
            var pwdStrengthOptions = { pwdStrength: $("#pwdStrength"), pwdError: $("#pwd_error"), value: $("#pwd").val().trim()};
            var savePwd = $('#savePwd');
            savePwd.on('click', function(){
                $("#pwd").bind("keyup", function(){ rpaValidate.func.pwdStrength(pwdStrengthOptions); }).RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd, true);
                $("#pwdRepeat").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat, true);
                $("#form1").submit();
            });
            $("#pwd").bind("keyup", function(){ rpaValidate.func.pwdStrength(pwdStrengthOptions); }).RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd);
            $("#pwdRepeat").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat);
        }  
    });
});