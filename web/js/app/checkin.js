require(['../config'],function(){
    require(['common']);
    require(['jquery', 'loginForm'], function($, loginForm){
        var firstUrl = $('#firstUrl').val(), lastUrl = $('#lastUrl').val(), type = $('#type').val(), u = $('#u').val();
        function changeWindow(){
			//窗口居中垂直
			var winH = $(window).height();
			var loginH = $("#loginFrame").height();
			//窗口居中水平
			var winW = $(window).width();
			var loginW = $("#loginFrame").width();
			$("#loginFrame").css({"top":(winH-loginH)/2, "left":(winW-loginW)/2});
			$(".quickLogPop").css({"top":(winH-loginH)/2 - 65, "left":(winW-loginW)/2 + 70});
		}
		function hasLogin(){
			$("#loginFrame").hide();
			$(".loadingFrame").show();
			$("#iframeDiv").append("<iframe id='iframe' src='"+ firstUrl +"' name='iframe' style='display:none;'></iframe>").attr('onload', goto());
		}
		function init(u){
			if(u){
				hasLogin();
			}else{
				$("#loginFrame").show();
				$(".loadingFrame").hide();
				execLogin();
			}
		}

		if(location.protocol == 'https:') {
			location.href = location.href.replace(/^https:/, 'http:');
		}
		init(u);
		changeWindow();
		$(window).resize(function(){
			changeWindow();
		});
		
		function goto(){
			switch(parseInt(type)){
				case 1:
					window.location.href = firstUrl; //cps
					break;
				case 2:
					window.location.href = lastUrl; //shop
					break;
			}
		}

		var directGoBtn = $('.directGoBtn');
		directGoBtn.on('click', function(){
			hasLogin();
		});

		function sendLoginInfo(){
		    $.ajax({  
		        url: "{{ path('_default_ad_login') }}",
		        data: { email: $.trim($("#email").val()), pwd: $.trim($("#pwd").val()) }, 
		        type: "POST",
		        success:function(data){
		           if(data=='ok'){
		            	hasLogin();
		           }else{
		        	   	$("#email_error").html(data).addClass('error').attr('display', 'block');
		           }
		        }
		    });
		}

		function execLogin(){
			var loginPwd = {
	            ele: '#pwd',
	            prompt: {
	                isNull: '请输入您的密码',
	                isFocus: '请输入您的密码'
	            }
	        }, loginEmail = {
	            ele: '#email',
	            prompt: {
	                isNull: '请输入邮箱地址',
	                isFormat: '邮箱地址格式不正确'
	            },
	            type: 'email'
	        };
	        new loginForm({pwd: loginPwd, email: loginEmail, auto: false});
	        var submitBtn = $('#submit_button');
			submitBtn.on('click', function(e){
				var loginform = new loginForm({pwd: loginPwd, email: loginEmail, auto: true});
	            if(loginform.run(true)){
	                sendLoginInfo();
	            }else{
	                e.preventDefault();
	            }
			});

	        var $emailError = $("#email_error"), $pwdError = $("#pwd_error");
	        $emailError.add($pwdError).on('click', function(){
	            $(this).addClass('fade');
	        });
	        var errorCode = $('#error_code').val();
	        if(errorCode != undefined){
	            $emailError.html(errorCode).addClass('error').attr('display', 'block');
	        };

	        //sinaWeibo and QQ quick login prompt
	        var wbLog = $('.weibo-login');
	        var qqLog = $('.qq-login');
	        var wqClose = $('.quickLCon .closeBtn').add('.quickLCon .cancelBtn');
	        var wbPCon = $('#wbLogCon');
	        var qqPCon = $('#qqLogCon');
	        wbLog.on('click', function(){
	            wbPCon.show();
	        });
	        qqLog.on('click', function(){
	            qqPCon.show();
	        });
	        wqClose.on('click', function(){
	            wbPCon.add(qqPCon).hide();
	        });
		}
    }); 
});