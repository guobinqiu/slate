$(document).ready(function(){
	getCenter();
	$("#signInFrame").hide();
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

function getCenter(){
    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;
    var popupHeight = $("#signInFrame").height();
    var popupWidth = $("#signInFrame").width();
    $("#signInFrame").css({
        "position": "absolute",
        "top": windowHeight/2-popupHeight/2,
        "left": windowWidth/2-popupWidth/2
    });
}

function signs(){
  $("#signInFrame").show();
  $(".blackBg").show();
}

function aremove(){
  $("#sign").removeClass('signIn');
  $("#sign").addClass('signIn close');
  $("#sign").removeAttr("onclick");
  $("#sign").attr('onMouseOver',null);
}

function goto(cid,aid,points){
    var w = window.open("","_blank");
    $.ajax({
          url: urls.advertiserment_click+"?id="+aid,
          post: "GET",
          success:function(data){
              if(data==1){
                 $.ajax({
                     url: Routing.generate("_checkin_issetClick", {"cid":cid}),
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
                      $.ajax({
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
