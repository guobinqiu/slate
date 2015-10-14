require(['../../config'],function(){
    require(['common']);
    require(['backTop'], function(backTop){
        new backTop({src: '../../web/img/common/backTop.png'});
    });
    require(['jquery'], function($){
        var search = $('.search');
        search.find('input').bind('focus', function(){
            search.find('label').hide();
        }).bind('blur', function(){
            var val = $(this).val();
            if(val == '请输入关键字' || val == ''){
                search.find('label').show();
            }
        });
        function init(){
            var resultCon = $('.search-result');
            var cons = resultCon.find('.partCon'),
                showAll = resultCon.find('.showAll'),
                allCon = resultCon.find('.allCon');
            showAll.unbind('click');
            showAll.on('click', function(){
                var i = showAll.index(this);
                cons.eq(i).hide();
                allCon.eq(i).show();
            });
        }
        function appendData(){
            var resultBox = $('.search-result').find('dl');
            var $titleBox = $('<dt></dt>'),
                conBox = '<dd></dd>';
            var len = data.result.length, str = '', moreBtn = $('<a href="javascript: void(0)"></a>');
            moreBtn.addClass('showAll').html('显示全部');
            for( var i = 0; i < len; i++){
                if((data.result[i].con).length > 228){
                    str += $titleBox.html('» ' + data.result[i].title)[0].outerHTML
                        + $(conBox).addClass('allCon').html(data.result[i].con)[0].outerHTML
                        + $(conBox).addClass('partCon').html((data.result[i].con).substr(0, 228)+'...').append(moreBtn)[0].outerHTML;
                }else{
                    str += $titleBox.html('» ' + data.result[i].title)[0].outerHTML + $(conBox).html(data.result[i].con)[0].outerHTML;
                }
            }
            resultBox.append(str);
        }
        var data = { result: [{title: '生日错误 ', con: '您输入的生日信息需要与您在注册91问问账户时填写的生日信息相同。' +
        '在91问问调查网，生日是一个非常重要的信息，我们以此来判断一个用户的年龄，进而提供给他合适的问卷。' +
        '如果您忘记了您的生日信息或者您当初填入了错误的日期，您可以提供您的身份证明或者您当初填入了错误的日期您可以提供您的身份证明或者您当初填入了错误的日期您可以提供您的身份证明或者您当初填入了错误的日期，' +
        '您可以份证或者您当初填入了错误的日期，您可以提供您的身份证或者您当初填入了错误的日期，您身份证或其他可以证明您生日的证件），' +
        '以邮件附件的形式提供给我们，会有客服人员帮您更新您的身份信息'}, {title: '生日错误', con: '您输入的生日信息需要与您在注册91问问账户时填写的生日信息相同。' +
        '在91问问调查网，生日是一个非常重要的信息，我们以此来判断一个用户的年龄，进而提供给他合适的问卷。' +
        '如果您忘记了您的生日信息或者您当初填入了错误的日期，您可以提供您的身份证明或者您当初填入了错误的日期，' +
        '您可以份证或者您当初填入了错误的日期，您可以提供您的身份证或者您当初填入了错误的日期，您身份证或其他可以证明您生日的证件），' +
        '以邮件附件的形式提供给我们，会有客服人员帮您更新您的身份信息'}, {title: '问卷频度 ', con: '我们将会不定期的通过邮件向您发送新的问卷邀请。' +
        '每7天能回答2份属性问卷。高分问卷无此限制。当您参加的问卷越多，填写的信息越齐全，您所收到调查问卷邀请也会越多。'}, {title: '问卷频度 ',
            con: '我们将会不定期的通过邮件向您发送新的问卷邀请。每7天能回答2份属性问卷。高分问卷无此限制。当您参加的问卷越多，填写的信息越齐全，您所收到调查问卷邀请也会越多。'}]};
        var resultMore = $('.more-result a');
        init();
        resultMore.on('click', function(){
            appendData();
            init();
        });
    });
});