define(['jquery', 'autoJump', 'routing'],function($, autoJump){
    function isEmail(str){
        return new RegExp("^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$").test(str);
    }
    function isNull(obj, isPrompt){
        var str = $(obj.ele).val();
        str = $.trim(str);
        if (str == "" || typeof str != "string") {
            if(isPrompt){
                eError(obj.ele, obj.prompt.isNull);
            }
            return false;
        }
        return true;
    }
    function betweenLength(str, _min, _max){
        return (str.length >= _min && str.length <= _max);
    }
    function eFocus(ele,prompt){
        $(ele).removeClass();
        $(ele+'_succeed').removeClass();
        $(ele+'_error').removeClass().addClass('focus').html(prompt);
    }
    function eError(ele,prompt){
        $(ele).removeClass().addClass('input_error');
        $(ele+'_succeed').removeClass();
        $(ele+'_error').removeClass().addClass('error').html(prompt);
    }
    function eSucceed(ele,prompt){
        $(ele).removeClass();
        $(ele+'_succeed').removeClass().addClass('succeed').html(prompt);
        $(ele+'_error').removeClass().html('');
    }
    function checkInputEmail(obj){
        if(obj.type && obj.type == 'email' && !isEmail($(obj.ele).val())){
            eError(obj.ele, obj.prompt.isFormat);
            return false;
        }
        if(obj.type && obj.type == 'con' && !betweenLength($(obj.ele).val(), 1, 500)){
            eError(obj.ele, obj.prompt.isBetweenLength);
            return false;
        }
        return true;
    }
    function checkInput(obj, def){
        if(def){
            if(isNull(obj, true)) {
                return checkInputEmail(obj);
            }
        }else{
            $(obj.ele).bind('focus', function() {
                eFocus(obj.ele,'');
            }).bind('blur', function() {
                if(isNull(obj, true)){
                   return checkInputEmail(obj);
                }
            });
        }
    }
    var content = {
        ele: '#content',
        prompt: {
            isNull: '请输入相关内容',
            isBetweenLength: '仅允许输入1到500个字符',
            isFocus: '请输入您想咨询的内容'
        },
        type: 'con'
    }, email = {
        ele: '#feedbackEmail',
        prompt: {
            isNull: '请输入您常用的邮箱地址',
            isFormat: '邮箱格式错误'
        },
        type: 'email'
    };

    checkInput(content);
    checkInput(email);

    var feedbackSave = $('#feedback_save');
    feedbackSave.on('click', function(){
        if(checkInput(content, true) && checkInput(email, true)){
            reSubmit();
        } 
    });
    var fbCon = $('.fbCon'), fdWrap = $(".fdWrap"), unfdWrap = $(".unfdWrap"), 
        closeTag = $('.closeTag'), closeBtn = $('.closeBtn');
    function countDown(){
        var secs = 6, s;
        $('#secs').text(secs+'秒后自动返回');
        s = setInterval(function(){
            if(secs <= 0){
                clearInterval(s);
            }
            $('#secs').text(secs+'秒后自动返回');
            secs--;
        }, 1000);
    }
    function submitCallback(){
        var submitSucceed = $('.submitSucceed'), feedbackForm = $('#feedback_form');
        if(submitSucceed.length >= 1){
            $('#sys_error').hide();
            feedbackForm.hide();
            submitSucceed.show(500, function(){
                countDown();
                setTimeout(function(){
                    $(this).hide();
                    feedbackForm.show();
                }, 6500);
            });
            $("#content").val('');  
            $("#feedbackEmail").val(''); 
        }else{
            window.location.href = Routing.generate("_default_feedback_finished");
        }
    }
    function reSubmit(){
        var sys_error = $('#sys_error'),
            con = $("#content").val().toString().replace(/\s+/g,""),
            email = $("#feedbackEmail").val().toString().replace(/\s+/g,"");
        $.ajax({  
            url: Routing.generate("_default_contact", {
                "content": con,
                "email": email
            }),
            type: "POST",
            success:function(data){
                switch(data){
                    case "1": sys_error.text('*请输入您的问题'); break;
                    case "2": sys_error.text('*请输入您的联系方式'); break;
                    case "3": sys_error.text('*您的联系方式不正确'); break;
                    case "4": sys_error.text('*系统出错，邮件发送失败'); break;
                    default: submitCallback(); break;
                }
            }
        });
    }
});