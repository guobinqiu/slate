require(['../../config'],function(){
    require(['common']);
    require(['jquery'], function($){
        var search = $('.search');
        search.find('input').bind('focus', function(){
            search.find('label').hide();
        }).bind('blur', function(){
            var val = $(this).val();
            if(val == '搜索商家名字...' || val == ''){
                search.find('label').show();
            }
        });
    });
});