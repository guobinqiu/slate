/*-------------------
引用jquery.js, routing.js, validate.js
-------------------*/
$(function(){
    var emailInput = $('#email');
    var emailSucceed = $('#email_succeed');
    var emailError = $('#email_error');

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

    $('#sendEmail').on('click', function(){
        var email = $.trim(emailInput.val());
        if (email == "" || isEmail(email) == false) {
            eError('请输入有效的邮箱地址');
            return;
        }
        $.ajax({
            type: 'GET',
            url: Routing.generate('_user_reset', { email: email }),
            dataType: 'JSON'
        }).done(function(data){
            countdown();
        }).fail(function(data){
            eError(data.responseJSON.message);
        });
    });

    var wait_seconds = 60;
    var seconds = wait_seconds;
    function countdown() {
        if (seconds == 0) {
            $('#sendEmail').removeAttr("disabled");
            eSucceed('');
            seconds = wait_seconds;
        } else {
            $('#sendEmail').attr("disabled", true);
            eSucceed('重置密码邮件已经发送至您的邮箱，<strong id="second">' + seconds + '</strong>秒后可重新发送。');
            seconds--;
            setTimeout(function(){
                countdown();
            }, 1000);
        }
    }

    emailInput.bind('focus', function() {
        var str = $.trim($('#email').val());
        eFocus('');
    });
});