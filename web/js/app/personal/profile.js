require(['../../config'],function(){
    require(['common']);

    //初始化表单
    require(['jquery', 'routing', 'layDate'], function($, routing){
        //生日下拉框
        $('.laydate-icon').on('click', function(){
            laydate();
        });

        function addOption(selectbox, num, text) {
            var str = '';
            for(var i = 0; i < num ; i++){
                str += '<option value="' + i + '">' + text[i] + '</option>';
            }
            selectbox.append(str);
        }

        function addOptions(selectbox, text) {
            var str = '';
            for(var i = 0; i < text.length ; i++){
                str += '<option value="' + text[i].id + '">' + text[i].name + '</option>';
            }
            selectbox.append(str);
        }
        //居住地下拉框
        var addressProvince = $('#province'),
              addressCity = $('#city'),
              curProvince = $('#province').val();
              userCity=$('#user_city').val();

              getCity(curProvince, userCity);

        function getCity(provinceId, cityId ){
            $.ajax({
                url: Routing.generate("_user_getCity", {"cid": provinceId }),
                type: "POST",
                success:function(data){
                    if(data == '') {
                        addressCity.append('');
                    }else{
                        var str = '', citys = eval(data);
                        for(var i = 0; i < citys.length ; i++){
                            var selected = '';
                            if (citys[i].id == cityId ){
                                selected = 'selected = "selected"';
                            }
                            str += '<option value="' + citys[i].id + '"'+selected+'>' + citys[i].cityName + '</option>';
                        }
                        addressCity.html(str);
                    }
                }
            });
        }
        console.log(curProvince);
        addressProvince.on('change', function(){
            var id = $(this).val();
            if(id == 0){
                addressCity.html('<option value="0" selected="selected">地区</option>');
                return false;
            }
            getCity(id, 0);
        });

        var submitBtn = $('#profile_save');
        submitBtn.on('click', function(){
            console.log($('#profileForm').serialize());
            $('#profileForm').submit();
        });
    });

    //图像上传及裁切
    require(['jquery', 'jcrop', 'fileUpload'], function($, jcrop, fileUpload){
        function showPreview(coords){
            var rx = 150 / coords.w;
            var ry = 150 / coords.h;
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
        $('#profile_attachment').fileupload({
           dataType: 'json',
           done: function (e, data) {
                console.log(data.result);
                if(data.result.substr(0,7)!='uploads'){
                    $(".errorInfo").html(data.result);
                }else{
                    //$(".img img").attr("src",path+data.result);
                    var path = '../../';
                    $('.picCut').show();
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
                    $(".resizeSubmit").html("<input type='submit' value='上传图片' name='resize' class='resBtn'/><br/><input type='submit' value='取消上传' name='cancer' class='cancelBtn'/>");
               }
           }
        });
    });
});