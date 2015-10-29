require(['../../config'],function(){
    require(['common']);
    require(['jquery'], function($){
        var search = $('.search');
        if(search.find('input').val() == '搜索商家名字...' || search.find('input').val() == ''){
            search.find('label').show();
        }else{
            search.find('label').hide();
        }
        search.find('input').bind('focus', function(){    
            search.find('label').hide();
        }).bind('blur', function(){
            var val = $(this).val();
            if(val == '搜索商家名字...' || val == ''){
                search.find('label').show();
            }else{
                search.find('label').hide();
            }
        });
    });
});