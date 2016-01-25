require(['../../config'],function(){
    require(['jquery'], function(){
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
                clearInterval(s);
            }
        }
        function reSendEmail(email){
            $("#prompt").hide();
            $("#sendAgain").hide();
            $(".again").click(function(){
                $.ajax({
                   url: Routing.generate("_user_reSend", {"email": $('#email').val(),"id":$('#uid').val(), "code":$('#code').val(), "nick": $('#nick').val()}),
                   post: "GET",
                   success:function(data){
                        if(msg==1){
                            if($("#secondNum").length > 0){
                                seconds = parseInt($("#secondNum").val());
                            }else{
                                seconds = 59;
                            }
                            s = setInterval(countdown, 1000);
                            $("#prompt").show();
                            $("#sendAgain").show();
                            $('.again').hide();
                        }else{
                            alert('发送失败');
                        }
                   }
               });
            });
        }
    });
});