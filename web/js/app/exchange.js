require(['../config'],function(){
    require(['common']);
    require(['jquery'], function($){
        //�޸ģ��л������
        var curVal = $('.curVal'),
            modifyInput = $('.modifyInput'),
            modifyBtn = $('#modify');

        modifyBtn.on('click', function(){
            curVal.hide();
            modifyInput.show();
        });

        var data = { result: { alipayId: 'wangxiaozu@163.com', alipayName: '��С��'}};
        if(data.result != undefined && data.result != null){
            curVal.find('#curAlipay').text(data.result.alipayId);
            curVal.find('#curName').text(data.result.alipayName);
            curVal.show();
            modifyInput.hide();
        }else{
            curVal.hide();
            modifyInput.show();
        }
        //ѡ����
        var options = $('.option');
        options.on('click', function(){
            var i = options.index(this);
            options.removeClass('active').eq(i).addClass('active');
        });
        //����֤
    });
});