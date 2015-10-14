require(['../../config'],function(){
    require(['common']);
    require(['jquery', 'layDate'], function($, layDate){
        $('.laydate-icon').on('click', function(){
            laydate();
        });
    });
});