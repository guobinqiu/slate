require(['../config'],function(){
    require(['common']);
    require(['textScroll']);
    require(['jquery'], function($){
        var description = $('.description'),
            btn = description.find('.btn');

        btn.on('click', function(){
            var curH = description.css('height');
            if(curH == '30px'){
                description.css('height', description.find('p').css('height'));
            }else{
                description.css('height', '30px');
            }
        });

        var optionBox = $('.options'),
            options = optionBox.find('dd');

        var results = {data: ['30%', '15%', '8%', '3%', '11%', '2%', '14%', '6%', '1%', '10%']};

        options.each(function(i, e){
            $(e).find('p').css('width', results.data[i]);
        });
    });
});