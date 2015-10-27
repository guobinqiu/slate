require(['../../config'],function(){
    require(['common']);
    require(['jquery'], function($){
        var category = $('#category');
        var data = {
            title: ['账号类', '问卷类', '积分兑换', '应用程序','其他'],
            items: [['验证邮件', '手机绑定','注销会员','如何注册','生日错误','账户锁定','账号密码','个人信息','无法登陆'], ['高积分问卷问题', '属性问卷问题','幸运问卷','海外调查','快速问答'],
                ['绑定手机','兑换失败','更改支付宝','亚马逊兑换失败','手机兑换失败', '积分返还', '积分失效'], ['无法下载或安装桌面工具','微博账户不支持桌面工具','桌面工具积分未累加','手机应用程序'], ['其他']]
        };

        function initCategory(){
            var items = $('<option></option>');
            var div = $('<div></div>'), str = '';

            for(var i = 0; i < data.title.length; i++){
                var str1 = '', title = $('<optgroup></optgroup>');
                for(var j = 0; j < data.items[i].length; j++){
                    str1 += (items.prop('value', data.items[i][j]).text(data.items[i][j]))[0].outerHTML;
                }
                str += ((title.prop('label', data.title[i])).append(str1))[0].outerHTML;
            }
            category.append(str);
        }
        initCategory();

        var mobile = $('.mobile'), birthday = $('.birthday'), surveyId = $('.surveyId'), surveyName = $('.surveyName'), exchange = $('.exchange'), alipay = $('.alipay');
        var spec = $('.spec');
        spec.hide();
        function changeForm(){
            var optionsSel = $('#category').find('option:selected');
            switch (optionsSel.val()){
                case '手机绑定': spec.hide(); mobile.show(); break;
                case '生日错误': spec.hide(); birthday.show(); break;
                case '高积分问卷问题':
                case '属性问卷问题':
                case '幸运问卷':
                case '海外调查':
                case '快速问答':
                    spec.hide(); surveyId.show(); surveyName.show();
                    break;
                case '兑换失败': spec.hide(); exchange.show(); break;
                case '更改支付宝': spec.hide(); alipay.show(); break;
                default : spec.hide(); break;
            }
        }
        category.on('change', function(){
            changeForm();
        });
    });
    require(['jquery'], function($){
        function isEmail(str){
            return new RegExp("^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$").test(str);
        }
        function isMobile(str){
            return new RegExp("^0?(13|15|18|14|17)[0-9]{9}$").test(str);
        }
        function isNull(obj, isPrompt){
            var str = $(obj.ele).val();
            str = $.trim(str);
            if (str == "" || typeof str != "string") {
                if(isPrompt){
                    eError(obj.ele, obj.prompt.isNull);
                }
                return false;
            }
            return true;
        }
        function eFocus(ele,prompt){
            $(ele).removeClass();
            $(ele+'_succeed').removeClass();
            $(ele+'_error').removeClass().addClass('focus').html(prompt);
        }
        function eError(ele,prompt){
            $(ele).removeClass().addClass('input_error');
            $(ele+'_succeed').removeClass();
            $(ele+'_error').removeClass().addClass('error').html(prompt);
        }
        function eSucceed(ele,prompt){
            $(ele).removeClass();
            $(ele+'_succeed').removeClass().addClass('succeed').html(prompt);
            $(ele+'_error').removeClass().html('');
        }
        function checkInput(obj, def){
            if(def){
                if(isNull(obj, true)) {
                    if(obj.type && obj.type == 'email'){
                        if(!isEmail($(obj.ele).val())){
                            eError(obj.ele,obj.prompt.isFormat);
                        }
                    }
                    if(obj.type && obj.type == 'mobile'){
                        if(!isMobile($(obj.ele).val())){
                            eError(obj.ele,obj.prompt.isFormat);
                        }
                    }
                     return true;
                }
                return false;
            }else{
                $(obj.ele).bind('focus',
                    function() {
                        eFocus(obj.ele,'');
                    }).bind('blur',
                    function() {
                        if(isNull(obj, false)){
                            if(obj.type && obj.type == 'email'){
                                if(!isEmail($(obj.ele).val())){
                                    eError(obj.ele,obj.prompt.isFormat);
                                }
                            }
                            if(obj.type && obj.type == 'mobile'){
                                if(!isMobile($(obj.ele).val())){
                                    eError(obj.ele,obj.prompt.isFormat);
                                }
                            }
                        }
                    });
            }
        }
        function checkSel(obj){
            var isSelected = $(obj.ele).find('option:selected');
            if(isSelected.val() == ''){
                eError(obj.ele,obj.prompt.isNull);
            }else{
                eFocus(obj.ele,'');
            }
        }
        var category = {
            ele: '#category',
            prompt: {
                isNull: '请选择问题的种类'
            }
        }, content = {
            ele: '#content',
            prompt: {
                isNull: '请输入相关内容',
                isFocus: '请输入您想咨询的内容'
            }
        }, name = {
            ele: '#name',
            prompt: {
                isNull: '请输入您的姓名'
            }
        }, email = {
            ele: '#email',
            prompt: {
                isNull: '请输入您常用的邮箱地址',
                isFormat: '邮箱格式错误'
            },
            type: 'email'
        }, mobile = {
            ele: '#mobile',
            prompt: {
                isNull: '请输入您的手机号码',
                isFormat: '手机格式不正确'
            },
            type: 'mobile'
        }, birthday = {
            ele: '#birthday',
            prompt: {
                isNull: '请输入生日错误原因'
            }
        }, surveyId = {
            ele: '#surveyId',
            prompt: {
                isNull: '请输入调查问卷的编号'
            }
        }, surveyName = {
            ele: '#surveyName',
            prompt: {
                isNull: '请输入调查问卷的名称'
            }
        }, exchange = {
            ele: '#exchange',
            prompt: {
                isNull: '请选择申请兑换的日期'
            }
        }, alipay = {
            ele: '#alipay',
            prompt: {
                isNull: '请输入您的支付宝账号'
            }
        };

        checkInput(content);
        checkInput(name);
        checkInput(email);
        checkInput(mobile);

        var feedbackSave = $('#feedback_save');
        feedbackSave.on('click', function(){
            var optionsSel = $('#category').find('option:selected');
            checkInput(content, true);
            checkInput(name, true);
            checkInput(email, true);
            checkSel(category);
            switch (optionsSel.val()){
                case '手机绑定':
                    checkInput(mobile, true);
                    if(checkInput(content, true)&&checkInput(name, true)&&checkInput(email, true)&&checkInput(mobile,true)){
                        reSubmit();
                    }
                    break;
                case '生日错误':
                    checkInput(birthday, true);
                    if(checkInput(content, true)&&checkInput(name, true)&&checkInput(email, true)&&checkInput(birthday,true)){
                        reSubmit();
                    }
                    break;
                case '高积分问卷问题':
                case '属性问卷问题':
                case '幸运问卷':
                case '海外调查':
                case '快速问答':
                    checkInput(surveyId, true);
                    checkInput(surveyName, true);
                    if(checkInput(content, true)&&checkInput(name, true)&&checkInput(email, true)&&checkInput(surveyId,true)&&checkInput(surveyName,true)){
                        reSubmit();
                    }
                    break;
                case '兑换失败':
                    checkInput(exchange, true);
                    if(checkInput(content, true)&&checkInput(name, true)&&checkInput(email, true)&&checkInput(exchange,true)){
                        reSubmit();
                    }
                    break;
                case '更改支付宝':
                    checkInput(alipay, true);
                    if(checkInput(content, true)&&checkInput(name, true)&&checkInput(email, true)&&checkInput(alipay,true)){
                        reSubmit();
                    }
                    break;
                default : if(checkInput(content, true)&&checkInput(name, true)&&checkInput(email, true)){
                        reSubmit();
                    } break;
            }
        });
        function reSubmit(){
            var str = $('#feedback_form').serialize();
            console.log('表单数据：'+ str);
            $.ajax({  
                url: Routing.generate("_default_contact", str),
                type: "POST",
                success:function(data){
                    switch(data){
                        case "1": tips.text('*请输入您的问题'); break;
                        case "2": tips.text('*请输入您的联系方式'); break;
                        case "3": tips.text('*您的联系方式不正确'); break;
                        case "4": tips.text('*系统出错，邮件发送失败'); break;
                        default: break;
                    }
                }
            });
        }
        $(category.ele).on('change', function(){
            checkSel(category);
        });
    });
});