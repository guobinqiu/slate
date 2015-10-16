require(['../../config'],function(){
    require(['common']);
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

        var link = window.location.href;
        var channel;
        if(link.indexOf('#') != -1){
            channel = link.substr(link.indexOf('#') + 1, link.length);
            $('#' + channel).show();
        }else{
            channel = '';
            $('#survey').show();
        }        
    });
});