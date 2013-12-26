function showAmazon(info,reward,uid){
    if(info&&info==1){
       isExistInfo();
    }else if(info==2){
       $('.blackBg').show();
       $('.amazon').show();
       if($('body').height() < $('.amazon').height() + 240){
          $('.blackBg').height($('.amazon').height() + 400)
       }else{
          $('.blackBg').height($('body').height())
       }
       $('#update').hide();
       $("#reward").show();
       $(".codeReward").html(reward);
       $('.amazonHref').attr('href',"http://count.chanet.com.cn/click.cgi?a=480534&d=338264&u="+uid+"&e=45"); 
    }else if(info==3){
       $("#update").hide();
       $("#amazonEmpty").show();    
    }
}
function isExistInfo(){
  $.ajax({
            url: "{{ path('_user_isExistInfo') }}",
            type: "GET",
            success:function(data){
                if(data==1){
                  $.ajax({
                        url: "{{ path('_default_infoVisit') }}",
                        type: "GET",
                        success:function(data){
                            if(data==1){
                                 $('.blackBg').show();
                                 $('.amazon').show();
                                 if($('body').height() < $('.amazon').height() + 240){
                                    $('.blackBg').height($('.amazon').height() + 400)
                                 }else{
                                    $('.blackBg').height($('body').height())
                                 }
                            }
                        }
                  });    
                }
            }
          });
}
function getIncome(){
	$.ajax({
		  url: "{{ path('_user_income') }}",
		  type: "GET",
		  success:function(data){
		    var obj = eval(data);
		    var html = '';
		    for(var i=0;i<obj.length;i++){ 
			html += "<input name='income' type='radio' value='"+obj[i]['id']+"' >"+obj[i]['income'];  
		    }
		    $(".income").html(html); 
		  }
	      });
}
function getHobby(){
	$.ajax({
		  url: "{{ path('_user_hobby') }}",
		  type: "GET",
		  success:function(data){
		    var obj = eval(data);
		    var html = '';
		    for(var i=0;i<obj.length;i++){ 
			html += "<input name='hobby' type='checkbox' value='"+obj[i]['id']+"' >"+obj[i]['hobby'];  
		    }
		    $(".hobby").html(html); 
		  }
	      });
}

function getProince(){
	$.ajax({
		  url: "{{ path('_user_province') }}",
		  type: "GET",
		  success:function(data){
		    var obj = eval(data);
		    var html = "<option value='0'>请选择省</option>";
		    for(var i=0;i<obj.length;i++){ 
			html += "<option value='"+obj[i]['id']+"' >"+obj[i]['provinceName']+"</option>";  
		    }
		    $("#province").html(html); 
		  }
	      });
}

function getYear(){
      var year = $("#year");  
      var month = $("#month");  
    
      var yearhtml = '';
      var monthhtml = '';
      var date = new Date();  
      var y = date.getFullYear();  
      yearhtml += "<option value='0'>请选择年</option>";   
      for (var i = 1940; i <= y ; i++) {  
 
         yearhtml += "<option value='"+i+ "' >"+i+"</option>";
      } 
      year.html(yearhtml);
      monthhtml += "<option value='0'>请选择月</option>";   
      for (var i = 1; i < 13; i++) {  
    
          monthhtml += "<option value='"+i+ "' >"+i+"</option>"; 
      }
      month.html(monthhtml);
}
function selectYear(){
  var month = $("#month"); 
  if($("#year").val()=='0'){
    month.get(0).options.length = 0; 
    month.get(0).options.add(new Option("请选择月", "0"));  
  }else{
    month.get(0).options.length = 0; 
    month.get(0).options.add(new Option("请选择月", "0"));  
    for (var i = 1; i < 13; i++) {  
            month.get(0).options.add(new Option(i, i));  
    }
  }
}

function selectCity(){
  var html = ''; 
  if($("#province").val()!=0){
     $.ajax({
              url: "{{ path('_user_getCity') }}?cid="+$('#province').val(),
              type: "GET",
              success:function(data){
                var obj = eval(data);
                for(var i=0;i<obj.length;i++){ 
                    html += "<option value='"+obj[i]['id']+"'>"+obj[i]['cityName']+"</option>";                 
                }
                $("#city").html(html); 
              }
          });
  }else{
      html += "<option value='0'>请选择地区</option>";   
      $("#city").html(html);
  }
}