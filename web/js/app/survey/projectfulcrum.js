require(['../../config'], function() {
    require(['common']);
    require(['jquery','backbone',], function($, backbone) {

    var survey_id = $('#survey_id').val();
    var preview = $('#preview').val();

    var showMessage = function(message){
        $(".error-message").html(message);
        $(".error-message").show();
    };

    surveylistCallback = function (res) {
        // show alert if error code
        if (res.meta.code != '200') {
            showMessage('Oops! Something went wrong.');
            return;
        }
        // get research
        var research = _.find(res.data.fulcrum_research, function(research){
            return research.survey_id == survey_id;
        });


        // no reasearch
        if ( !research ){
            showMessage("您好!本次调查已经结束。");
            return;
        }

        // interpolate label
        $('#title > h1').text(research.title);
        $('#no').text('f' + research.survey_id);
        $('#point > span').text(research.extra_info.point.complete);
        if(research.extra_info.content){
          $('#note').append($('<li>' + research.extra_info.content+ '</li>'));
        }
        $('#survey-link').attr({"href": research.url});
        $('.info-contents').show();
    };

    if (preview) { 
//        mockResponse();
     } 

    $.ajaxSetup({ cache: true });
    $.getScript( $('#url').val()  )
     .fail(function( script, textStatus ) {
            showMessage("System Error");
    });


    if (preview) { 
         // You can preview this page without valid data by accessing fulcrum_project_survey/information/preview
         function mockResponse () {
           var callback = surveylistCallback;
           surveylistCallback = function(res){
             dummy_res = { 'meta' : {'code': '200' },
                           'data': {
                             'fulcrum_research':[
                               {
                                 "survey_id": "preview",
                                 "quota_id": "10",
                                 "cpi": "0.00",
                                 "ir": "80",
                                 "loi": "10",
                                 "title": "Survey from Fulcrum",
                                 "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=test2",
                                 "date": "2015-01-01",
                                 "extra_info": {
                                     "point" : {"complete": "10"}
                                 }
                               }
                             ]}};
             callback(dummy_res);
           };
         }
    }
  });

});
