require(['../../config'],function(){
    require(['common']);
    require(['jquery', 'layDate'], function($, layDate){
        $('.laydate-icon').on('click', function(){
            laydate();
        });

        var href = window.location.href;
	    var channel, nav = $('.main-personal-left li');
	    var arr = {'exchange': '兑换历史', 'adtaste': '任务历史', 'message': '我的消息', 'info': '个人资料', 'account': '账户设置'};
	    $.each(arr, function(i, e){
	        if(href == 'http://wang-jili.com/user/info'){ 
	            nav.find('a').parent().removeClass('active').eq(0).addClass('active');
	            return false;
	        }
	        if(href.indexOf(i) != -1){
	            var navs = nav.find('a');
	            var len = navs.length;
	            for(var j = 0; j < len; j++){
	                if(navs.eq(j).text() == e){
	                    nav.find('a').parent().removeClass('active').eq(j).addClass('active');
	                    return false;        
	                }
	            }
	        }else{
	            nav.find('a').parent().removeClass('active');
	        }    
	    });
    });
});