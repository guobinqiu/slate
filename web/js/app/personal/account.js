require(['../../config'],function(){
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
    require(['jquery', 'validate', 'routing'], function($, rpaValidate, routing){
        //修改密码（交互）
        $.extend(rpaValidate.func, {
            updatePwd : function() {
                $("#pwd").RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd, true);
                $("#pwdRepeat").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat, true);
                return rpaValidate.func.FORM_submit([ "#pwd", "#pwdRepeat"]);
            }
        });
        $("#pwd").RPAValidate(rpaValidate.prompt.pwd, rpaValidate.func.pwd);
        $("#pwdRepeat").RPAValidate(rpaValidate.prompt.pwdRepeat, rpaValidate.func.pwdRepeat);
        $('#pwd_save').on('click', function(){
            if(rpaValidate.func.updatePwd()){
                savePwd();
            }
        });
        var curPwdInput = $('#curPwd'),
            curPwdSucceed = $('#curPwd_succeed'),
            curPwdError = $('#curPwd_error');

        function closeSlider(){
            var eles = $('.main-personal-account>ul>li');
            var btns = eles.find('.edit'),
                cons = eles.find('.con');
            eles.removeClass('active');
            cons.slideUp();
            btns.html('编辑');
        }
        function eError(prompt){
            curPwdInput.removeClass().addClass('input_error');
            curPwdSucceed.removeClass();
            curPwdError.removeClass().addClass('error').html(prompt);
        }
        function savePwd(){
            var str = $('#curPwd').val().trim();
            str = $.trim(str);
            if (str == "") {
                eError('请输入当前密码');
                return false;
            }
            $.ajax({
                type: "POST",
                url: Routing.generate('_profile_changepwd'),
                contentType : "application/x-www-form-urlencoded; charset=utf-8",
                data: { curPwd: $("#curPwd").val().trim(), pwd: $("#pwd").val().trim(), pwdRepeat: $("#pwdRepeat").val().trim(), csrf_token: $("#csrf_token").val().trim()},
                success : function(data) {
                    var msg = data.message;
                    if(data.status == 1){
                        alert(msg);
                        closeSlider();
                    }else{
                        if(msg != null && msg.trim() != ''){
                            if(msg == 'Need login'){
                                // 跳转到登录画面
                                window.location.href = Routing.generate('_user_login');
                            }else if(msg == 'Access Forbidden'){
                                // 跳转到账户设置首页画面
                                window.location.href = Routing.generate('_profile_index');
                            }else{
                                $('.backError').html(msg);
                            }
                        }
                    }
                }
            });
        }
        //注销
        var reasons = $('.reason-options'),
            withdrawSave = $('#withdraw_save');
        withdrawSave.on('click', function(){
            saveWithdraw();
        });
        function saveWithdraw(){
            var checked = [], len = reasons.find('input:checked').length;
            for(var i = 0; i < len; i++){
                checked[i] = reasons.find('input:checked').eq(i).val().trim();
            }

            $.ajax({
                type: "POST",
                url: Routing.generate('_profile_withdraw'),
                contentType : "application/x-www-form-urlencoded; charset=utf-8",
                data: {reason: checked, csrf_token: $("#csrf_token").val().trim()},
                success : function(data) {
                    var msg = data.message;
                    alert(data.status);
                    alert(data.message);
                    if(data.status == 1){
                        window.location.href = Routing.generate('_profile_withdraw_finish');
                        //closeSlider();
                    }else{
                        if(msg != null && msg.trim() != ''){
                            if(msg == 'Need login'){
                                // 跳转到登录画面
                                window.location.href = Routing.generate('_user_login');
                            }else if(msg == 'Access Forbidden'){
                                // 跳转到账户设置首页画面
                                window.location.href = Routing.generate('_profile_index');
                            }else{
                                $('.backError').html(msg);
                            }
                        }
                    }
                }
            });
        }
    });
});