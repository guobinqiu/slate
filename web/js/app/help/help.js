require(['../../config'],function(){
    require(['common']);
    require(['tab']);
    require(['jquery'], function($){
        var search = $('.search');
        search.find('input').bind('focus', function(){
            search.find('label').hide();
        }).bind('blur', function(){
            var val = $(this).val();
            if(val == '请输入关键字' || val == ''){
                search.find('label').show();
            }
        });
    });
});