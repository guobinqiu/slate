require(['../../config'],function(){
    require(['common']);
    require(['jquery'], function($){
        var progress = $('.progress');
        //ajax获取资料完善度
        var data = { 'meta': {'code': 200,'message': ''},'data':{'progress': '20%'}};
        if(data.meta.code == 200){
            progress.find('b').text(data.data.progress);
            progress.find('.colorBar').css('width', data.data.progress);
            progress.find('.btn').css('left', data.data.progress);
        }else{
            progress.find('b').text('0%');
            progress.find('.colorBar').css('width', '0%');
            progress.find('.btn').css('left', '0%');
        }
    });

    //修改昵称，验证表单
    require(['jquery', 'validate'], function($, rpaValidate){
        $("#regName").RPAValidate(rpaValidate.prompt.regName, rpaValidate.func.regName);
        $('#regName_modify').on('click', function(){
            var regNameValue = $('.regName-value');
            regNameValue.hide();
            $('#regName').show().val(regNameValue.text());
            $(this).hide();
            $('#regName_save').show();
        });
        $('#regName_save').on('click', function(){
            var regNameValue = $('.regName-value'),
                regName = $('#regName');
            //regName.RPAValidate(rpaValidate.prompt.regName, rpaValidate.func.regName, true);
            regName.hide();
            regNameValue.show().text(regName.val());
            $(this).hide();
            $('#regName_modify').show();
            $('#regName_succeed').removeClass('succeed');
//            $.ajax({
//                url: "{{ path('_user_regName_modify') }}?regName="+$('#regName').val(),
//                post: "GET",
//                success:function(data){
                var data = 1;
                if(data==1){

                }else if(data==2){

                }else{

                }
//                }
//            });
        });
    });

    //初始化表单
    require(['jquery', 'routing'], function($, routing){
        //生日下拉框
        var birthdayY = $('#birthday_year'),
            birthdayM = $('#birthday_month'),
            birthdayD = $('#birthday_day');
        function isLeapYear(year){
            return (year % 4 == 0 || (year % 100 == 0 && year % 400 == 0));
        }
        function addOption(selectbox, num, text) {
            var str = '';
            for(var i = 0; i < num ; i++){
                str += '<option value="' + i + '">' + text[i] + '</option>';
            }
            selectbox.append(str);
        }
        function setDays(year, month, day) {
            var monthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            var yea = year.find('option:selected').text();
            var mon = month.find('option:selected').text();
            if(yea == '年' || mon == '月'){ return false;}
            var initD = monthDays[parseInt(mon) - 1];
            if (mon == 2 && isLeapYear(yea)) {
                initD++;
            }
            for (var j = day.find('option').length - 1; j > initD; j--) {
                day.find('option').eq(j).remove();
            }
            var addDays = initArr(day.find('option').length, initD+1);
            addOption(birthdayD, (initD- day.find('option').length) + 1 , addDays);
        }
        function initArr(start, end){
            var arr = [];
            for(var i = 0, j = start; j <= end; i++, j++){
                arr[i] = j;
            }
            return arr;
        }
        function initOption(){
            var curY = new Date().getFullYear(),
                years = initArr(1910, curY),
                months = initArr(1, 12),
                days = initArr(1, 31);
            // 添加年份，从1910年开始
            addOption(birthdayY, curY - 1909, years);
            // 添加月份
            addOption(birthdayM, 12, months);
            // 添加天份，先默认31天
            addOption(birthdayD, 31, days);
        }
        birthdayY.on('change', function(){
            setDays(birthdayY, birthdayM, birthdayD);
        });
        birthdayM.on('change', function(){
            setDays(birthdayY, birthdayM, birthdayD);
        });

        function addOptions(selectbox, text) {
            var str = '';
            for(var i = 0; i < text.length ; i++){
                str += '<option value="' + text[i].id + '">' + text[i].name + '</option>';
            }
            selectbox.append(str);
        }
        //居住地下拉框
        var addressProvince = $('#address_province'),
            addressCity = $('#address_city'),
            curProvince = $('#address_province').val();
        function getCity(id){
            $.ajax({
                url: Routing.generate("_user_getCity", {"cid": id }),
                type: "POST",
                success:function(data){
                    var str = '', citys = eval(data);
                    for(var i = 0; i < citys.length ; i++){
                        str += '<option value="' + citys[i].id + '">' + citys[i].cityName + '</option>';
                    }
                    addressCity.html(str);
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
            getCity(id);
        });

        //工作
        var jobP = $('#job_profession'), jobC = $('#job_category'), jobD = $('#job_department');
        var jobData = { 'meta': {'code': 200, 'message': ''},'data':{ 'profession': [{ 'id': 1, 'name': '公务员'}, 
        { 'id': 2, 'name': '经营管理者'}, { 'id': 3, 'name': '公司职员（一般事务）'}, { 'id': 4, 'name': '公司职员（技术人员）'}, 
        { 'id': 5, 'name': '公司职员（律师，医生等专业人士）'}, { 'id': 6, 'name': '军人'}], 'category': [{'id': 1, 'name': '农业/水产'}, 
        {'id': 2, 'name': '金融（银行/证券/保险）'}, {'id': 3, 'name': '计算机/IT/数据输入'}, {'id': 4, 'name': '电子技术/半导体/集成电路'},
        {'id': 5, 'name': '会计/审计'}, {'id': 6, 'name': '美容'}], 'department': [{'id': 1, 'name': '总务/人事/管理'}, 
        {'id': 2, 'name': '会计/财务'}, {'id': 3, 'name': '销售'}, {'id': 4, 'name': '公关/宣传'}, {'id': 5, 'name': '规划'}, {'id': 6, 'name': 'IT开发'}]}};    
        function initJobData(){
            addOptions(jobP, jobData.data.profession);
            addOptions(jobC, jobData.data.category);
            addOptions(jobD, jobData.data.department);
        }        

        //教育程度
        var education = $('#education');
        var educationData = { 'meta': {'code': 200, 'message': ''},'data':{ 'education': [{ 'id': 1, 'name': '高中以下'}, 
        { 'id': 2, 'name': '高中毕业'}, { 'id': 3, 'name': '大专毕业'}, { 'id': 4, 'name': '大学本科毕业'}, { 'id': 5, 'name': '研究生，博士毕业'}]}};

        //没有填写过详细资料，初始化表单
        function initForm(){
            initOption();
            initJobData();
            addOptions(education, educationData.data.education);
        }

        initForm();
        var submitBtn = $('#profile_save');
        submitBtn.on('click', function(){
            console.log($('#profile_form').serialize());
            $('#form1').submit();
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
        $('#attachment').fileupload({
           dataType: 'json',
           done: function (e, data) {
                console.log(data.result);
                if(data.result.substr(0,7)!='uploads'){
                   $(".errorInfo").html(data.result);
                }else{
                    $(".img img").attr("src",path+data.result);
        // $('#attachment').on('change',function(){
        //     var data = { result: "uploads\/user\/6\/1369644344.jpeg"},
        //         path = '../../web/';
                    var path = '../../web/';
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
        // });
               }
           }
        });
    });
});