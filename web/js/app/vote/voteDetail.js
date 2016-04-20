require(['../../config'],function(){
    require(['common']);
    require(['textScroll']);
    require(['jquery'], function($){
        var description = $('.description');

        description.on('click', function(){
            var curH = description.css('height');
            if(curH == '33px'){
                description.css({'height': description.find('p').css('height'), 'padding-bottom': '14px'});
            }else{
                description.css({'height': '33px', 'padding-bottom': '0'});
            }
        });
    });
});