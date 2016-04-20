require(['../../config'],function(){
    require(['tab'], function(){
    	var href = window.location.href;
    	if(href.indexOf("bind") != -1){
    		$('.tabs span').removeClass('active').eq(1).addClass('active');
    		$('.otherRegForm .con').hide().eq(1).show();
    	}
    });
    require(['jquery', 'validate'], function($, validate){
        var validatePrompt = {
            email:{
                onFocus:"邮箱地址就是您登录91问问的账号",
                succeed: "OK!",
                isNull: "请输入您的邮箱",
                error: {
                    beUsed: "该邮箱已被注册,请更换其它邮箱,或使用该<a href=\"signin.php?Email={#Email#}\" name=\"email_login_link\" class=\"more\">邮箱登录</a>",
                    badLength: "邮箱地址长度应在4-50个字符之间",
                    badFormat: "邮箱地址格式不正确"
                }
            },
            pwd: {
                onFocus: "5-100个字符，至少包含1位字母和1位数字",
                succeed: "OK!",
                isNull: "请输入密码",
                error: {
                    badLength: "密码长度只能在5-100位字符之间",
                    badFormat: "密码至少包含1位字母和1位数字",
                    simplePwd: "该密码比较简单，建议您更改"
                },
                onFocusExpand: function() {
                    $("#pwdStrength").hide();
                }
            },
            jili_email: {
                onFocus:"请输入您的91问问账号",
                succeed: "OK!",
                isNull: "请输入您的邮箱",
                error: {
                    badLength: "邮箱地址长度应在4-50个字符之间",
                    badFormat: "邮箱地址格式不正确"
                }
            },
            jili_pwd: {
                onFocus: "请输入您的登录密码",
                succeed: "OK!",
                isNull: "请输入密码",
                error: {
                    badLength: "密码长度只能在5-100位字符之间"
                }
            }
        };

        var weiboEmail = "#weibo_user_regist_email", qqEmail = "#qqregist_email_id", pwd = "#pwd", jili_email = "#jili_email", jili_pwd = "#jili_pwd";
        var href = window.location.href;
        $.extend(validate.func, {
            loginValidate : function() {
            	if(href.indexOf('weibo') != -1){
            		$(weiboEmail).RPAValidate(validatePrompt.email, validate.func.email, true);	
            		$(pwd).RPAValidate(validatePrompt.pwd, validate.func.pwd, true);
                	return validate.func.FORM_submit([weiboEmail, pwd]);
            	}
                if(href.indexOf('qq') != -1){
                	qqCheck();
            		$(pwd).RPAValidate(validatePrompt.pwd, validate.func.pwd, true);
                	return validate.func.FORM_submit([qqEmail, pwd]);
            	}
            },
            bdingValidate: function(){
            	$(jili_email).RPAValidate(validatePrompt.jili_email, validate.func.email, true);
                $(jili_pwd).RPAValidate(validatePrompt.jili_pwd, validate.func.pwd, true);
                return validate.func.FORM_submit([jili_email, jili_pwd]);
            }
        });

		function qqCheck(){
			var val;
	        $(qqEmail).bind('focus', function(){
	    		$(qqEmail + '_error').removeClass().addClass('focus').html('请输入您的qq邮箱');
	    	}).bind('blur', function(){
	    		val = $(qqEmail).val();
	    		if(validate.rules.fullNumberName(val)){
	    			$(qqEmail).attr("sta", 2);
	        		$(qqEmail + '_error').removeClass();
	        		return true;
	        	}
	        	if(val === '' || val === null){
	        		$(qqEmail + '_error').removeClass().addClass('error').html('邮箱不能为空');	
	        		return false;
	        	}
	        	$(qqEmail).attr("sta", 1);
	        	$(qqEmail + '_error').removeClass().addClass('error').html('您输入的格式不对');	
	    	});
		}
        $(weiboEmail).RPAValidate(validatePrompt.email, validate.func.email);
        $(weiboEmail).blur(function(){validate.func.loginValidate();});
        qqCheck();
        $(pwd).RPAValidate(validatePrompt.pwd, validate.func.pwd);
        $(pwd).blur(function(){validate.func.loginValidate();});
        $(jili_email).RPAValidate(validatePrompt.jili_email, validate.func.email);
        $(jili_email).blur(function(){validate.func.bdingValidate();});
        $(jili_pwd).RPAValidate(validatePrompt.jili_pwd, validate.func.pwd);
        $(jili_pwd).blur(function(){validate.func.bdingValidate();});

        $("#reg").on("click", function () {
        	if(validate.func.loginValidate()){
        		$("#form1").submit();	
        	}
        });
        $("#bind").on("click", function () {
            if(validate.func.bdingValidate()){
        		$("#form2").submit();	
        	}
        });
    });
});