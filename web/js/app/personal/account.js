require(['../../config'],function(){
    require(['common']);
    require(['jquery'],function($){
       	var eles = $('.main-personal-account>ul>li');
       	var btns = eles.find('.edit'),
       		cons = eles.find('.con');

       	btns.on('click', function(){
       		var i = btns.index(this);
       		var withdrawL = $(this).hasClass('withdrawPart');
            if(eles.eq(i).hasClass('active')){
                eles.eq(i).removeClass('active');
                cons.eq(i).slideToggle();
                if(withdrawL){
                    $(this).html('注销');
                }else{
                    btns.eq(i).html('编辑');
                }
            }else{
                eles.removeClass('active').eq(i).addClass('active');
                cons.slideUp().eq(i).slideDown();
                btns.html('编辑').eq(i).html('收起');
                if(!withdrawL){
                    $('.withdrawPart').html('注销');
                }
            }
       	});

        var withdrawContinue = $('#withdraw_continue'), backStep = $('.backStep'),
                step1 = $('.step1'), step2 = $('.step2');
        withdrawContinue.on('click', function(){
            step1.hide();
            step2.show();
        });
        backStep.on('click', function(){
            step1.show();
            step2.hide();
        });
   	});
    require(['jquery', 'validate', 'routing', 'loginForm'], function($, rpaValidate, routing, loginForm){
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
        $('#pwd_save').on('click keypress', function(){
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
            var str = $.trim($('#curPwd').val());
            str = $.trim(str);
            if (str == "") {
                eError('请输入当前密码');
                return false;
            }
            $.ajax({
                type: "POST",
                url: Routing.generate('_profile_changepwd'),
                contentType : "application/x-www-form-urlencoded; charset=utf-8",
                data: { curPwd: $.trim($("#curPwd").val()), pwd: $.trim($("#pwd").val()), pwdRepeat: $.trim($("#pwdRepeat").val()), csrf_token: $.trim($("#csrf_token").val())},
                success : function(data) {
                    var msg = data.message;
                    if(data.status == 1){
                        $('.backError').hide();
                        $('.successMess').html(msg).show(1000, function(){
                            setTimeout(closeSlider, 3000); 
                        });
                    }else{
                        if(msg != null && $.trim(msg) != ''){
                            if(msg == 'Need login'){
                                // 跳转到登录画面
                                window.location.href = Routing.generate('_user_login');
                            }else if(msg == 'Access Forbidden'){
                                // 跳转到账户设置首页画面
                                window.location.href = Routing.generate('_profile_index');
                            }else{
                                $('.backError').html(msg).show();
                                $('.successMess').hide();
                            }
                        }
                    }
                }
            });
        }
        //注销
        var withdrawPwd = {
            ele: '#withdrawPwd',
            prompt: {
                isNull: '请输入您的密码',
                isFocus: '请输入您的密码'
            }
        }, withdrawEmail = {
            ele: '#withdrawEmail',
            prompt: {
                isNull: '请输入邮箱地址',
                isFormat: '邮箱地址格式不正确'
            },
            type: 'email'
        };
        new loginForm({pwd: withdrawPwd, email: withdrawEmail, auto: false});
        var reasons = $('.reason-options'),
        withdrawSave = $('#withdraw_save');
        withdrawSave.on('click keypress', function(){
            var loginform = new loginForm({pwd: withdrawPwd, email: withdrawEmail, auto: true});
            if(loginform.run(true)){
                saveWithdraw();
            }
        });
        function saveWithdraw(){
            var checked = [], len = reasons.find('input:checked').length;
            for(var i = 0; i < len; i++){
                checked[i] = $.trim(reasons.find('input:checked').eq(i).val());
            }
            $.ajax({
                type: "POST",
                url: Routing.generate('_profile_withdraw'),
                contentType : "application/x-www-form-urlencoded; charset=utf-8",
                data: {reason: checked, csrf_token: $.trim($("#csrf_token").val()), email: $.trim($("#withdrawEmail").val()), password: $.trim($("#withdrawPwd").val()) },
                success : function(data) {
                    var msg = data.message;
                    if(data.status == 1){
                        window.location.href = Routing.generate('_profile_withdraw_finish');
                    }else{
                        if(msg != null && $.trim(msg) != ''){
                            if(msg == 'Need login'){
                                // 跳转到登录画面
                                window.location.href = Routing.generate('_user_login');
                            }else if(msg == 'Access Forbidden'){
                                // 跳转到账户设置首页画面
                                window.location.href = Routing.generate('_profile_index');
                            }else if(msg == 'Use Not Exist'){
                                $('.backError').html('对不起，您的注销失败了，用户不存在');
                            }else{
                                $('.backError').html('对不起，您的注销失败了，请稍后再试');
                            }
                        }
                    }
                }
            });
        }
    });
});