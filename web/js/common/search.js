define(['jquery'],function($){
    var search = $('.search');
    if(search.find('input').val() == '请输入关键字' || search.find('input').val() == ''){
        search.find('label').show();
    }else{
        search.find('label').hide();
    }
    search.find('input').bind('focus', function(){    
        search.find('label').hide();
    }).bind('blur', function(){
        var val = $(this).val();
        if(val == '请输入关键字' || val == ''){
            search.find('label').show();
        }else{
            search.find('label').hide();
        }
    });
});