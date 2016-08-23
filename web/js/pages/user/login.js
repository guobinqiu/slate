/*-------------------
引用jquery.js, common.js, jquery.touchSwipe.min.js, landing.js
-------------------*/
$(function(){
    // /*---登录表单校验---*/
    // var loginPwd = {
    //     ele: '#pwd',
    //     prompt: {
    //         isNull: '请输入您的密码',
    //         isFocus: '请输入您的密码'
    //     }
    // }, loginEmail = {
    //     ele: '#email',
    //     prompt: {
    //         isNull: '请输入邮箱地址',
    //         isFormat: '邮箱地址格式不正确'
    //     },
    //     type: 'email'
    // };
    // new LoginForm({pwd: loginPwd, email: loginEmail, auto: false});
    // var lis = $('.login-con li'), inputs = lis.find('input'), labels = lis.find('label');
    // inputs.each(function(i, e){
    //     if(!$(this).val()){
    //         labels.eq(i).show();
    //     }else{
    //         labels.eq(i).hide();
    //     }
    //     $(this).on('keydown', function(){
    //         labels.eq(i).hide();
    //     });
    //     $(this).on('keyup', function(){
    //         if(!$(this).val()){
    //             labels.eq(i).show();
    //         }else{
    //             labels.eq(i).hide();
    //         }
    //     }); 
    // });
    // var submitBtn = $("#submit_button");
    // submitBtn.on('click', function(e){
    //     var loginform = new LoginForm({pwd: loginPwd, email: loginEmail, auto: true});
    //     if(loginform.run(true)){
    //         submitBtn.submit();
    //     }else{
    //         e.preventDefault();
    //     }
    // });

    // var $emailError = $("#email_error"), $pwdError = $("#pwd_error");
    // $emailError.add($pwdError).on('click', function(){
    //     $(this).addClass('fade');
    // });
    // var errorCode = $('#error_code').val();
    // if(errorCode != undefined){
    //     $emailError.html(errorCode).addClass('error').attr('display', 'block');
    // };

    /*---sinaWeibo and QQ quick login prompt---*/
    var wbLog = $('.weibo-login'),qqLog = $('.qq-login');
    var wqClose = $('.quickLCon .closeBtn').add('.quickLCon .cancelBtn');
    var wbPCon = $('#wbLogCon'),qqPCon = $('#qqLogCon');
    var mask = $('.mask');
    wbLog.on('click', function(){
        mask.show();
        wbPCon.show().addClass('active');
        qqPCon.hide().removeClass('active');
    });
    qqLog.on('click', function(){
        mask.show();
        qqPCon.show().addClass('active');
        wbPCon.hide().removeClass('active');
    });
    wqClose.on('click', function(){
        mask.hide();
        wbPCon.add(qqPCon).hide().removeClass('active');
    });
});