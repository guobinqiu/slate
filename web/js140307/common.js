$(document).ready(function(){
	
	setTaskNumber();
	
	var menuState = false;
	var settingMenu = function(){
		if(!menuState){
			$("#userFrame").show();
			menuState = true;
		}else{
			$("#userFrame").hide();
			menuState = false;
		}
	}
	$("#userbar a.personal").click(function(){
		settingMenu();
	})
	$(window).keypress(function(e){
		var key = e.which; //e.which是按键的值
		if (key == 0) {
			if(menuState){
				settingMenu();
			}
		}
	})
	$(document).bind("click",function(e){
		var target = $(e.target);
		if(target.closest("#userFrame").length == 0 && target.closest("#userbar a.personal").length == 0){
			if(menuState){
				settingMenu();
			}
		}
	}) 
	
	$("#nav .task").hover(function(){
	  $(this).children("ul").show();
	},function(){
	  $(this).children("ul").hide();
	})

	$("div#search>div.searchMenu").click(function(){
       $(this).children("ul").toggle(); 
	});

    $("div#search>div.searchMenu>ul>li").click(function(){
        var el = $(this);

        var rt = search_form_rt_config.commodity.value; // default
        var label_ =search_form_rt_config.commodity.label;

        if( true === search_form_rt_config.hasOwnProperty( el.attr('lang') )) {
           rt = search_form_rt_config[ el.attr('lang') ].value;
           label_ = search_form_rt_config[ el.attr('lang') ].label;
        } 
        $('form#search_box :input#search_rt').val(rt);

        $('div#search>div.searchMenu>span').text( label_ );
    });
    
    $("#task .all").width($("#task ul").width() * $("#task ul").length)
    
    //task任务列表：推荐
    setUndoTaskClass();

	//task任务列表：确认中
	for(i=1; i<=$("#confirmTask li").length; i++){
		if(i%2 == 0){
			$("#confirmTask li:eq(" + (i-1) + ")").addClass("brn")
		}
	}
	//task任务列表：已完成
	for(i=1; i<=$("#finishTask li").length; i++){
		if(i%2 == 0){
			$("#finishTask li:eq(" + (i-1) + ")").addClass("brn")
		}
	}
	setTaskNumber();
	
	//91问问快速回答
	$(".vote").click(function(){
		$.ajax({
            url: urls.wenwenVisit,
            post: "GET",
            success:function(data){
	        	if(data==1){
                	$("li.vote").remove();
                	setTaskNumber();
                	setUndoTaskClass();
                }
            }
        }); 
    });
	
});

//task任务列表数字
var setTaskNumber = function(){
	//nav
	$("#nav li.task em").html($("#nav li.task li").length);
}
var setUndoTaskClass = function(){
	for(i=1; i<=$("#undoTask li").length; i++){
		if(i%2 == 0){
			$("#undoTask li:eq(" + (i-1) + ")").addClass("brn")
		}
	}
}