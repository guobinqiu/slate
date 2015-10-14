require(['../../config'],function(){
    require(['common']);
    require(['jquery'], function($){
        function isNull(obj){
            var str = $(obj.ele).val();
            str = $.trim(str);
            if (str == "" || typeof str != "string") {
                eError(obj.ele, obj.prompt.isNull);
                return false;
            }
            return true;
        }
        function maxLen(obj){
            var len = $(obj.ele).val().length;
            if(len != 0 && len <= obj.max){
                eSucceed(obj.ele, '');
                return true;
            }else{
                eError(obj.ele, obj.prompt.maxLen);
                return false;
            }
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
        function checkOffline(obj, def){
            if(def){
                if(isNull(obj)) {
                    if(maxLen(obj)){
                        return true;
                    }
                }
                return false;
            }else{
                $(obj.ele).bind('focus',
                    function() {
                        var str = $(obj.ele).val();
                        eFocus(obj.ele, obj.prompt.maxLen);
                    }).bind('blur',
                    function() {
                        if(isNull(obj)) {
                            maxLen(obj);
                        }
                    });
            }
        }
        function checkOption(){
            var optionEle = $('.option');
            var optionsVal = [];
            for(var i = 0, j = 0; i < optionEle.length; i++){
                var str = optionEle.eq(i).val();
                str = $.trim(str);
                if(str == "" || typeof str != "string"){
                }else{
                    optionsVal[j] = optionEle.eq(i).val();
                    j++;
                }
            }
            if(optionsVal.length < 3){
                $(options.ele+'_error').removeClass().addClass('error').html(options.prompt.isNull);
                return false;
            }else{
                for(var m = 0; m < optionsVal.length; m++){
                    if(optionsVal[m].length > 20){
                        $(options.ele+'_error').removeClass().addClass('error').html(options.prompt.maxLen);
                        return false;
                    }
                }
                $(options.ele+'_error').removeClass().html('');
            }
            return true;
        }
        var title = {
            ele: '#title',
            prompt: {
                isNull: '请输入标题',
                maxLen: '标题长度不超过20个字符'
            },
            max: 20
        };
        var content = {
            ele: '#content',
            prompt: {
                isNull: '请输入相关说明及描述',
                maxLen: '说明及描述不超过100个字符'
            },
            max: 100
        };
        var options = {
            ele: '#option1',
            prompt: {
                isNull: '请至少输入3个选项内容',
                maxLen: '选项内容不超过20个字符'
            },
            max: 20
        };
        checkOffline(title);
        checkOffline(content);
        var optionItems = $('.option');
        optionItems.bind('focus',
            function() {
                $(options.ele+'_succeed').removeClass();
                $(options.ele+'_error').removeClass().addClass('focus').html(options.prompt.isNull);
            }).bind('blur',function(){
                var i = optionItems.index(this);
                var str = optionItems.eq(i).val();
                if(str != '' && str.length > 20){
                    $(options.ele+'_error').removeClass().addClass('error').html(options.prompt.maxLen);
                }
            });
        var suggestSave = $('#suggest_save');
        suggestSave.on('click', function(){
            checkOffline(title, true);
            checkOffline(content, true);
            checkOption();
            if(checkOffline(title, true)&&checkOffline(content, true)&&checkOption()){
                reSubmit();
            }
        });
        function reSubmit(){
            var str = $('#suggest_form').serialize();
            console.log('表单数据：'+str);
//            $.ajax({
//                url: "{{ path('_user_reset') }}?email="+$('#title').val(),
//                post: "GET",
//                success:function(data){
//                }
//            });
        }
    });
});