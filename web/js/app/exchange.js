require(['../config'],function(){
    require(['common']);
    require(['jquery'], function($){
        //修改，切换输入框
        var curVal = $('.curVal'),
            modifyInput = $('.modifyInput'),
            modifyBtn = $('#modify');

        modifyBtn.on('click', function(){
            curVal.hide();
            modifyInput.show();
        });

        var data = { result: { alipayId: 'wangxiaozu@163.com', alipayName: '王小卒'}};
        if(data.result != undefined && data.result != null){
            curVal.find('#curAlipay').text(data.result.alipayId);
            curVal.find('#curName').text(data.result.alipayName);
            curVal.show();
            modifyInput.hide();
        }else{
            curVal.hide();
            modifyInput.show();
        }
        //选择金额
        var options = $('.option');
        options.on('click', function(){
            var i = options.index(this);
            options.removeClass('active').eq(i).addClass('active');
        });
        //表单验证
    });
});