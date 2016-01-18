require(['../../config'],function(){
    require(['common']);
    
    
    //初始化表单
    require(['jquery'], function($){

        console.log("sop_api_url: "+$('#sop_api_url').val());
        
        $.ajax({
            type:       'GET'
            ,url:       $('#sop_api_url').val()
            ,dataType:  'jsonp'
            ,jsonp:     false
            ,cache:     true
        });

        var openSurvey= function (url) {
            console.log("openSurvey:"+url);
            // todo: Mark item as "done"
            window.open(
                url,
                'sop_window',
                'resizable=yes,scrollbars=yes,toolbar=no'
            );
        };

        surveylistCallback = function (res) {

            console.log("surveylistCallback: code:" + res.meta.code);
//            surveylistCallback({"data":{"research":[],"profiling":[{"url":"https://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?app_id=27&app_mid=2&next=%2Fprofile%2Fp%2Fq001&sig=7f44a9aafa6fb3dc219f0cfee29db0dc8623f2eebbd10e12041d0dbbe9c753ff&sop_locale=&time=1453095420","name":"q001","title":"请问您的出生日期是什么？"}]},"meta":{"message":"","code":200}});

            // return if error code
            if (res.meta.code != '200')  return;

            // return if no data
            if( res.data.profiling.length == 0 && res.data.research.length == 0 ) return;

            // remove no survey label
            var tbodyEl = $('#survey-list > tbody:first tr.no-survey-available').remove();

            // load research data
            renderResearchItems(res.data.research.reverse());
            
            // load profiling data
            renderProfilingItems(res.data.profiling);

        };
        
        var renderProfilingItems = function (items) {

            var point = $('#sop_point').val();
            $.each(items, function (item, data) {
                console.log(data);

            var str = '<li><div class="survey-item">'+
                '<div class="title"><a href="#">'+data.title+'</a></div>'+
                '<ul class="con clearfix">'+
                    '<li class="first">'+
                        '<span>问卷编号</span>'+
                        '<p>'+data.name+'</p>'+
                    '</li>'+
                    '<li>'+
                        '<span>问卷类型</span>'+
                        '<p>属性问卷</p>'+
                    '</li>'+
                    '<li>'+
                        '<span>回答期限</span>'+
                        '<p>--</p>'+
                    '</li>'+
                    '<li class="last">'+
                        '<span>积分数</span>'+
                        '<p><strong>'+point+'</strong></p>'+
                    '</li>'+
                '</ul>'+
                '</div>'+
                '<div class="survey-btn" ><a href="#" class="open-link" >可回答</a></div></li>';

            $("div").delegate("a.open-link", "click", function(){
                console.log( $( this ).text() );
                openSurvey(data.url );
            });

            $('#sop-profiling-item-template').append(str); 

            });
        };
        
        var renderResearchItems = function (items) {

        };

    });

});