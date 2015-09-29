define(['jquery'],function($){
    var mobileInput = $('#mobile'),
        mobileSucceed = $('#mobile_succeed'),
        mobileError = $('#mobile_error');
    function isPhone(str){
        return new RegExp("^0?(13|15|18|14|17)[0-9]{9}$").test(str);
    }
    function eError(prompt){
        mobileInput.removeClass().addClass('input_error');
        mobileSucceed.removeClass();
        mobileError.removeClass().addClass('error').html(prompt);
    }
    function eSucceed(prompt){
        mobileInput.removeClass();
        mobileSucceed.removeClass().addClass('focus').html(prompt);
        mobileError.removeClass().html('');
    }
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
        if (code == "" || (isPhone(code) == false)) {
            eError('请输入有效的手机号码');
            return false;
        }
//            $.ajax({
//                url: "{{ path('_user_reset') }}?email="+$('#email').val(),
//                post: "GET",
//                success:function(data){
        var data = 1;
        if(data==1){
            seconds = 10;
            s = setInterval(countdown, 1000);
            eSucceed('<strong id="second">10</strong>秒');
            sendCode.addClass('disabled').html('重新发送');
            sendCode.onclick = null;
        }else if(data==2){
            var code = $('#email').val();
            //var url = "{{ path('_user_activeEmail',{'email': "email" }) }}";
            var url = "http://www.91jili.com";
            url = url.replace('email',encodeURIComponent( code));
            var html = "<p>邮箱地址未激活，请重新<a href='"+url+"'>激活</a></p>";
            message.show().html(html);
        }else{
            $('#email').val('');
        }
//                }
//            });
    }
    var sendCode = $('#send_code');
    sendCode.on('click', function(){
        var code = $('#mobile').val();
        if(sendCode.hasClass('disabled')){
            return;
        }else{
            reSendCode(code);
        }
    });
    var mobileSave = $('#mobile_save');
    mobileSave.on('click', function(){
        var code = $('#mobile').val();
        code = $.trim(code);
        if (code == "" || (isPhone(code) == false)) {
            eError('请输入有效的手机号码');
            return false;
        }
        //ajax提交
    });
});