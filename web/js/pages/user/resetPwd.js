/*-------------------
引用jquery.js, routing.js, validate.js
-------------------*/
$(function(){
    var emailInput = $('#email');
    var emailSucceed = $('#email_succeed');
    var emailError = $('#email_error');

    function isEmail(str){
        var pattern = /^([a-zA-Z0-9._-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
        return new RegExp(pattern).test(str);
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
            url: Routing.generate('_user_reset'),
            data: $('#emailForm').serialize(),
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