require(['../../config'],function(){
    require(['common']);
    require(['jquery'],function($){
       	var navLinks = $('.main-personal-message .btn a');
	    var navSlider = $('.main-personal-message .btn .border');
		var curIndex = navLinks.parent().find('.active').index();

	    if(curIndex != -1){
	        navSlider.show().animate({ left: 110 * curIndex +'px'}, 100);
	    }
	    navLinks.hover(function(){
	        var i = $(this).index();
	        navSlider.show().animate({ left: 110 * i +'px'}, 100);
	    }, function(){});
	    navLinks.parent().hover(function(){
	    }, function(){
	        var i = navLinks.parent().find('.active').index();
	        if(i != -1){
		        navSlider.show().animate({ left: 110 * i +'px'}, 100);
		    }else{
		    	navSlider.hide();
		    }
	    });
   	});
   	require(['jquery', 'routing'],function($, routing){
   		var oid = null;
		var cid = null;
   		function showCb(id){
			var isRead = '.isRead'+id;
			var show = '#showContent'+id;
			var temp = $(show).is(":hidden");//是否隐藏
			if(temp){
				$.ajax({
				  url : Routing.generate("_user_updateIsRead", {"sendid": id }),
				  type : 'get',
				  success : function(data) { 
				    if(cid){ $('#showContent'+cid).hide(); }
				    var obj = eval(data);
				    var str = obj[0]['content'];
				    $(isRead).removeClass("new");
				    $(show).html(str).show();
				    cid = id;
				    if(obj[0]['isRead'] == 1){
				        var html = parseInt($("#countMessage").text()-1);
				        $("#countMessage").html(html).hide();
				    }      
				  }
				});
			}else{
				$(show).hide();
				cid = null;
			}
		}
   		function showMs(id){
		    var isRead = '.isRead'+id;
		    var show = '#showContent'+id;
		    var temp = $(show).is(":hidden");//是否隐藏
		    if(temp){
		        $.ajax({
		        url : Routing.generate("_user_updateSendMs", {"sendid": id }),
		        type : 'get',
		        success : function(data) {
					if(oid){ $('#showContent'+oid).hide();  }
					var obj = eval(data);
					var str = obj[0]['content'];
					$(isRead).removeClass("new");
					$(show).html(str).show();
					oid = id;
					if(obj[0]['isRead'] == 1){
					  var html = parseInt($("#countMessage").text()-1);
					  $("#countMessage").html(html).hide();
					}   
		        }
		      });
		    }else{
				$(show).hide();
				oid = null;
		    }
		}
       	var titles = $('.main-personal-message .con li'),
       		sysTitles = $('.main-personal-message .sys li');
   		titles.unbind('click');
   		sysTitles.unbind('click');
       	titles.on('click', function(){
            var idStr = $(this).attr('class'), id;
            if(idStr){
            	id = idStr.substr(5, idStr.length-10);
            	showMs(id);
            }
        });
       	sysTitles.on('click', function(){
            var idStr = $(this).attr('class'), id;
            if(idStr.indexOf('new') == -1){
            	id = idStr.substr(6, idStr.length-6);            	
            }else{
            	id = idStr.substr(6, idStr.length-10);
            }
            showCb(id);
        });
   	});
});