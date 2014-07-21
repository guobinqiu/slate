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
	
	// task list show
	taskListShow();

    
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
var taskListShow = function(){
    var last;
    var pathname = document.location.pathname;
	last = pathname.charAt(pathname.length-1);
    if(last == "\/"){
        $("#nav .task").children("ul").show();
    }else{
        $("#nav .task").hover(function(){
            $(this).children("ul").show();
        },function(){
          $(this).children("ul").hide();
        })
    }
}
