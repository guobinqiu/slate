$(document).ready(function(){
	
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
});
