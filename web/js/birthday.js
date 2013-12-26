var path = '{{ asset("") }}';
$(document).ready(function(){
       
    function showPreview(coords){
      var rx = 100 / coords.w;
      var ry = 100 / coords.h;
      $('#preview').css({
        width: Math.round(rx * $('#target').width()) + 'px',
        height: Math.round(ry * $('#target').height()) + 'px',
        marginLeft: '-' + Math.round(rx * coords.x) + 'px',
        marginTop: '-' + Math.round(ry * coords.y) + 'px'
      });
    }
    function checkCoords(){
      if (parseInt($('#w').val())) return true;
      alert('Please select a crop region then press submit.');
      return false;
    }
    function updateCoords(c){
      jQuery('#x').val(c.x);
      jQuery('#y').val(c.y);
      jQuery('#w').val(c.w);
      jQuery('#h').val(c.h);
    }
    

  $('#attachment').fileupload({
        dataType: 'json',
        done: function (e, data) {
          if(data.result.substr(0,7)!='uploads'){
            $(".errorInfo").html("<font color='red'>"+data.result+"</font>");
          }else{
            //$(".img img").attr("src",path+data.result);
            $(".resDiv").show();
            $(".imageInfo").html("<img src='"+path+data.result+"' id='target'/>");
            $("#resizePath").val(data.result);
            var Jcrop_api;
            $('#target').Jcrop({
              aspectRatio: 1,
              onChange: showPreview,
              onSelect: showPreview,
              onSelect: updateCoords
            },function(){
              this.animateTo([0,0,256,256]);
            });
            $(".resizeimage").html("<img src='"+path+data.result+"' id='preview'/>");
            $(".resizeSubmit").html("<input type='submit' value='上传图片' name='resize' class='resBut'/><br/><input type='submit' value='取消上传' name='cancer' class='cancerBut'/>");
          }  
        }
    });

	{% if codeflag %}
      $("#info").hide();
      $("#update").show();
  {% else %}
      $("#update").hide();
  {% endif %}

  $(".upload").click(function (){
      $("#form1").submit();
  });
	$("#change").click(function (){
		$("#info").hide();
		$("#update").show();
    var oldcity = {{ user.city }};
    var html = '';
    {% if user.province %}
      $.ajax({
            url: "{{ path('_user_getCity') }}?cid={{ user.province }}",
            post: "GET",
            success:function(data){
              var obj = eval(data);
              for(var i=0;i<obj.length;i++){ 
                 var selected = (oldcity == obj[i]['id'])?"selected='selected'":""; 
                  html += "<option value='"+obj[i]['id']+"'  " + selected+ " >"+obj[i]['cityName']+"</option>";                 
              }
              $("#city").html(html); 
            }
        });
    {% endif %}
    var year = $("#year");  
    var month = $("#month");  
    var newYear = {{ newYear }};
    var newMonth = {{ newMonth }};
    var yearhtml = '';
    var monthhtml = '';
    var date = new Date();  
    var y = date.getFullYear();  
    //初始化年份  
    //year.get(0).options.add(new Option("请选择年", "0"));  
    yearhtml += "<option value='0'>请选择年</option>";   
    for (var i = 1940; i <= y ; i++) {
       
       var selected = (newYear == i)?"selected='selected'":""; 
       
       yearhtml += "<option value='"+i+ "' " + selected+ " >"+i+"</option>";
        // year.get(0).options.add(new Option(i, i));  
    } 
    year.html(yearhtml);
    //初始化月份      
    month.get(0).options.add(new Option("请选择月", "0"));    
    for (var i = 1; i < 13; i++) {  
          var selected = (newMonth == i)?"selected='selected'":""; 
          // month.get(0).options.add(new Option(i, i)); 
           monthhtml += "<option value='"+i+ "' " + selected+ " >"+i+"</option>"; 
    }
     month.html(monthhtml);
	})

});
function selectYear(){
  var month = $("#month"); 
  if($("#year").val()=='0'){
    month.get(0).options.length = 0; 
    month.get(0).options.add(new Option("请选择月", "0"));  
  }else{ 
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
              post: "GET",
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