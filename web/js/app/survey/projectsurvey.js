require(['../../config'], function() {
    require(['common']);

    require(['jquery'], function($) {

        console.log("survey_id: " + $('#survey_id').val());

        var survey_id = $('#survey_id').val();

        var showMessage = function(message){
            $(".error-message").html(message);
            $(".error-message").show();
        };

//        $.ajax({
//            type: 'GET',
//            url: $('#sop_api_url').val(),
//            dataType: 'jsonp',
//            jsonp: false,
//            cache: true
//        });

//        var openSurvey = function(pageURL,name) {
//            console.log("openSurvey:" + pageURL);
//            // todo: Mark item as "done"
//            window.open(url, name, 'resizable=yes,scrollbars=yes,toolbar=no');
//        };

        surveylistCallback = function(res) {

            console.log("surveylistCallback: code:" + res.meta.code);
            //            surveylistCallback({"data":{"research":[],"profiling":[{"url":"https://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?app_id=27&app_mid=2&next=%2Fprofile%2Fp%2Fq001&sig=7f44a9aafa6fb3dc219f0cfee29db0dc8623f2eebbd10e12041d0dbbe9c753ff&sop_locale=&time=1453095420","name":"q001","title":"请问您的出生日期是什么？"}]},"meta":{"message":"","code":200}});
            //            surveylistCallback({"data":{"user_agreement":[{"url":"https://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?app_id=27&app_mid=2&next=%2Fcint%2Fuser_agreement&sig=bb321cd66bf13a9b3c29f64fc1144ebebbbd11f124503555d57f0f56f64f73ad&sop_locale=&time=1453107402","type":"Cint"}],"research":[{"date":"2016-01-18","is_answered":"0","cpi":"0.00","is_closed":"0","ir":"0","extra_info":{"point":{"screenout":"10","quotafull":"1","complete":"154"},"date":{"end_at":"2016-04-30 00:00:00","start_at":"2016-01-18 00:00:00"},"content":""},"url":"https://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?app_id=27&app_mid=2&next=%2Fproject_survey%2F452&sig=23107aaf8b54c33229990fdaab4d63c9c166a55d112dc136d990df6cf859518c&sop_locale=&time=1453107402","loi":"10","title":"Test project 02","survey_id":"334","quota_id":"452"},{"date":"2016-01-18","is_answered":"0","cpi":"0.00","is_closed":"0","ir":"0","extra_info":{"point":{"screenout":"10","quotafull":"1","complete":"252"},"date":{"end_at":"2016-03-31 00:00:00","start_at":"2016-01-18 00:00:00"},"content":"mail footer and cover page's information"},"url":"https://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?app_id=27&app_mid=2&next=%2Fproject_survey%2F450&sig=5b1ffb2280eea73e8a8ba99ea1927a4d554102acd92cad4a6d1ba6bd6e521dc9&sop_locale=&time=1453107402","loi":"15","title":"Test project 01","survey_id":"332","quota_id":"450"}],"profiling":[{"url":"https://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?app_id=27&app_mid=2&next=%2Fprofile%2Fp%2Fq036&sig=8e9e45abcdb185e083ca8995ddbd0e7398ec5ed9886dc4ba8208f5c35706ccc8&sop_locale=&time=1453107402","name":"q036","title":"请问当在工作中涉及到合同和以下IT产品的采购是,您将如何形容自己的角色呢?"}]},"meta":{"message":"","code":200}});

            // show alert if error code
            if (res.meta.code != '200') {
                showMessage('Oops! Something went wrong.');
                return;
            }
            // get research
            var research = $.find(res.data.research, function(research){
                return research.survey_id == survey_id
                    && research.is_answered == '0';
            });

            // no reasearch
            if ( !research ){
                showMessage("您好!本次调查已经结束。");
                return;
            }

            // interpolate label
            $('#title > h2').text(research.title);
            $('#no').text('r' + research.survey_id);
            $('#point').text(research.extra_info.point.complete);
            if(research.extra_info.content){
              $('#note').append($('<br>※' + research.extra_info.content));
            }
            $('#survey-link').attr({"href": research.url});
            $('.qnTxt').show();

        };

        $.ajaxSetup({ cache: true });
        $.getScript( $('#url').val() )
         .fail(function( script, textStatus ) {
                showMessage("System Error");
        });

    });

});