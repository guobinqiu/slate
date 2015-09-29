require(['../config'],function(){
    require(['common']);
    require(['jquery'],function($){
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
            if (str == "" || (isEmail(str) == false)) {
                eError('请输入当前密码');
                return false;
            }
//            $.ajax({
//                type : "POST",
//                url : "../register/regService?r=" + Math.random() + "&"
//                    + location.search.substring(1),
//                contentType : "application/x-www-form-urlencoded; charset=utf-8",
//                data : curPwd.val(),
//                success : function(data) {
                    var data = { result: false};
                    if (data.result) {
                    }else{
                        $('#curPwd').removeClass().addClass('input_error');
                        $('#curPwd_succeed').removeClass();
                        $('#curPwd_error').removeClass().addClass('error').html('您当前密码输入错误');
                    }
//                }
//            });
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
    require(['mobile']);
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