require(['../../config'],function(){
    require(['common']);
    require(['jquery'], function($){
        var progress = $('.progress');
        var data = { progress: '20%'};
        progress.find('b').text(data.progress);
        progress.find('.colorBar').css('width', data.progress);
        progress.find('.btn').css('left', data.progress);
    });

    //验证表单
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
        $('#profile_save').on('click', function(){
            console.log($('#profile_form').serialize());
        });
//            $.ajax({
//                type : "POST",
//                url : "../register/regService?r=" + Math.random() + "&"
//                    + location.search.substring(1),
//                contentType : "application/x-www-form-urlencoded; charset=utf-8",
//                data : $("#profile_form").serialize(),
//                success : function(result) {
//                    var result = {};
//                    if (result) {
//                        var obj = eval(result);
//                        if (obj.info) {
//                            //showMessage(obj.info);
//                            console.log(obj.info);
//                            verc();
//                            $("#registsubmit").removeAttr("disabled").removeClass()
//                                .addClass("btn-img btn-regist");
//                            isSubmit = false;
//                            return;
//                        }
//                        if (obj.noAuth) {
//                            verc();
//                            window.location = obj.noAuth;
//                            return;
//                        }
//                        if (obj.success == true) {
//                            window.location = obj.dispatchUrl;
//                        }
//                    }
//                }
//            });
    });

    //初始化表单
    require(['jquery'], function($){
        //生日下拉框
        var birthdayY = $('#birthday_year'),
            birthdayM = $('#birthday_month'),
            birthdayD = $('#birthday_day');
        function isLeapYear(year){
            return (year % 4 == 0 || (year % 100 == 0 && year % 400 == 0));
        }
        function addOption(selectbox, num, text) {
            var option = $("<option></option>");
            var str;
            for(var i = 0; i < num ; i++){
                str += (option.attr('value', i).text(text[i]))[0].outerHTML;
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
        initOption();
        birthdayY.on('change', function(){
            setDays(birthdayY, birthdayM, birthdayD);
        });
        birthdayM.on('change', function(){
            setDays(birthdayY, birthdayM, birthdayD);
        });

        //居住地下拉框
        var addressProvince = $('#address_province'),
            addressCity = $('#address_city');
        var data = {province:['直辖市', '河北省'], city: [['上海','北京','天津','重庆'],['衡水市','石家庄市','唐山市','秦皇岛市','邯郸','邢台','保定','张家口市','承德市','沧州市','廊坊市'],[]]};
        function initPro(){
            addOption(addressProvince, data.province.length, data.province);
        }
        function initCity(){
            addressCity.html('<option value="" selected="selected">地区</option>');
        }
        initPro();
        function setCity(index){
            if(addressCity.find('option').length > 1){
                initCity();
            }
            addOption(addressCity, data.city[index].length, data.city[index]);
        }
        addressProvince.on('change', function(){
            if(addressProvince.find('option:selected').text() == '省、市'){
                initCity();
                return;
            }
            setCity($(this).find('option:selected').index()-1);
        });

        //收入
        var incomeSel = $('#income');
        var incomeData = {income:['1000元-1999元', '2000元-2999元', '3000元-3999元', '32000元-35999元', '36000元以上']};
        function initIncome(){
            addOption(incomeSel, incomeData.income.length, incomeData.income);
        }
        initIncome();

        //工作
        var jobP = $('#job_profession'), jobC = $('#job_category'), jobD = $('#job_department');
        var jobData = { profession:['公务员', '经营管理者', '公司职员（一般事务）', '公司职员（技术人员）', '公司职员（律师，医生等专业人士）', '军人'], category
            : ['农业/水产', '金融（银行/证券/保险）', '计算机/IT/数据输入', '电子技术/半导体/集成电路', '会计/审计', '美容'], department: ['总务/人事/管理', '会计/财务', '销售', '公关/宣传', '规划', 'IT开发']};
        function initJob(){
            addOption(jobP, jobData.profession.length, jobData.profession);
            addOption(jobC, jobData.category.length, jobData.category);
            addOption(jobD, jobData.department.length, jobData.department);
        }
        initJob();

        //教育程度
        var education = $('#education');
        var educationData = { education:['高中以下', '高中毕业', '大专毕业', '大学本科毕业', '研究生，博士毕业']};
        function initEducation(){
            addOption(education, educationData.education.length, educationData.education);
        }
        initEducation();
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
        //$('#attachment').fileupload({
        //    dataType: 'json',
        //    done: function (e, data) {
        //        if(data.result.substr(0,7)!='uploads'){
        //            $(".errorInfo").html(data.result);
        //        }else{
                    //$(".img img").attr("src",path+data.result);
        $('#attachment').on('change',function(){
            var data = { result: "uploads\/user\/6\/1369644344.jpeg"},
                path = '../../web/';
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
        });
        //        }
        //    }
        //});
    });
});