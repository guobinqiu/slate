$(document).ready(function(){
	getCenter("#signInFrame");
	$("#signInFrame").hide();
	getCenter("#confirmAutoFrame");
	$("#confirmAutoFrame").hide();
	$.ajax({
        url: urls.checkin_clickCount,
        post: "GET",
        success:function(data){
            if(data<3){
              var times = parseInt(3-parseInt(data));
              $("#remain").text(times);
            }else{
              $("#remain").text(0);
              aremove();
            } 
        }
	});

	//关闭签到
	$(".close_checkin").click(function(){
	    $(".blackBg").hide();
	    $("#signInFrame").hide();
	 });
});

function getCenter(ele){
    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;
    var popupHeight = $(ele).height();
    var popupWidth = $(ele).width();
    $(ele).css({
        "position": "absolute",
        "top": windowHeight/2-popupHeight/2,
        "left": windowWidth/2-popupWidth/2
    });
}
// 取当前的autocheckin 是否有设置。
var getAutoCheckinConfig = function() {
    var jili_autocheckin = this.jili_autocheckin || {}; 
    $.ajax({
        //_checkin_issetClick
        url: Routing.generate('autocheckinconfig_get'),
        post: "GET",
        success: function(rsp) {
            if( rsp.code == 200) {
                jili_autocheckin.is_set = rsp.data.flag_data;
            } else {
    //            alert('error report');
            };
        }
    });
    return false;
}; 

var signs = function(){
    getAutoCheckinConfig();
	console.log(jili_autocheckin + '----' + jili_autocheckin.is_set);
    if( typeof jili_autocheckin == "undefined"&&( typeof jili_autocheckin.is_set == "undefined"  ||  jili_autocheckin.is_set == false) ) {
        $("#signInFrame").show();
        $(".blackBg").show();
    } else {
        // redirect to the new page.
		$("#confirmAutoFrame").show();
		$(".blackBg").show();
        //if(confirm( "将在新打开的页面中进行自动签到, 请确认")) {
        //
   // open the new windows  
   // and do the autocheckins.
   // how to pass the checkin shops ??
   // by session ?? how to clear
   // by cache_data ?? how to remove ?
   // is it shared by every one?
        //    console.log();
        //};
    }
};

function aremove(){
  $("#sign").removeClass('signIn');
  $("#sign").addClass('signIn close');
  $("#sign").removeAttr("onclick");
  $("#sign").attr('onMouseOver',null);
}

function goto(cid,aid,points){
    var w = window.open("","_blank");
    $.ajax({
        // 记录商家的access: _advertiserment_click
          url: urls.advertiserment_click+"?id="+aid,
          post: "GET",
          success:function(data){
              if(data==1){
                  // 是否已经签到过cid
                 $.ajax({
                     //_checkin_issetClick
                    url: urls.checkin_issetClick+"?cid="+cid,
                    post: "GET",
                    success:function(data){
						
						
                      if(data == 1){
                          var nowTimes = parseInt(parseInt($("#remain").text())-1);
                          if(nowTimes<=0){
                              $("#signInFrame h5").html("恭喜您签到成功，已获得<font color='#E94C1B'>"+points+"</font>米粒！");
                              $("li #task_checkin_mark").removeClass("mark");
                              $("li #task_checkin_mark").addClass("hasMark");
                              $("#mysign").text("已签到");
                              $("#mysign").css("background","#ccc");
                              $("#mysign").unbind("click",signs);

                              //setTaskNumber();
                          }else{
                              $("#remain").text(nowTimes);
                          }

                      }
                      $(".image"+cid).removeAttr("onclick");
                      $(".goTo "+cid).removeAttr("onclick");
                      // 记录签到商家数，发米粒 .
                      $.ajax({
                          //_checkin_clickInsert
                            url: urls.checkin_clickInsert+"?cid="+cid+"&aid="+aid,
                            post: "GET",
                            success:function(data){ 
                              var points = $("#points").text();
                              var obj ;
                              if (typeof(JSON) == 'undefined'){
                                obj = eval("("+data+")");
	                          }else{
	                            obj = JSON.parse(data);
	                          }

                              // 打开商家，_checkin_location
                              w.location.href = urls.checkin_location+"?aid="+aid+"&type=1";
                              if(obj.code==1){  
                                  aremove();
                                  $("#points").text(parseInt(obj.point)+parseInt(points));
                              }
                            }
                      });
                    }
                 });
              }else if(data==0){
                  window.location.href=urls.user_login;
                  w.close();
              }else{
                  w.close();
              }
          },
          error:function(){
             w.close();
          }
      });
}
