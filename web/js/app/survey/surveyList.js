require(['../../config'], function() {
    require(['common']);
    require(['jquery', 'sopSurvey', 'backbone', 'routing','jqueryCookie'], function($, survey, backbone, routing) {

        var addSuveyItem = function (el) {

            var surveyList = $('#surveyList');
                // Insert the item as the 2rd row of the table if table has more than 0 row
            if (surveyList.children().length) {
                surveyList.find('li:first').before($(el));
                return;
            }
            // Append the item to the table if already elements exist
            surveyList.append($(el));
        };

        var renderProfilingItems = function (items) {
            _.each(items, function (item) {
                var model = new survey.ProfilingItemModel(item);
                var view  = new survey.ProfilingItemView({ model: model });
                addSuveyItem(view.render().el);
            });
        };
        var renderResearchItems = function (items) {
            _.each(items, function (item) {
                var model = new survey.ResearchItemModel(item);
                var view  = new survey.ResearchItemView({ model: model });
                addSuveyItem(view.render().el);
            });
        };

    var renderFulcrumUserAgreementItems = function (items) {
        _.chain(items)
        .filter(function (item) {
            return item.type == 'Fulcrum';
        })
        .each(function (item) {
            var model = new survey.FulcrumAgreementModel(item);
            var view = new survey.FulcrumUserAgreementView({ model: model });
            view.render();
        });
    };

    var renderFulcrumResearchItems = function (items) {
        _.each(items, function (item) {
            var model = new survey.FulcrumResearchItemModel(item);
            var view  = new survey.FulcrumResearchItemView({ model: model });
            addSuveyItem(view.render().el);
        });
    };

        surveylistCallback = function (res) {

            // return if error code
            if (res.meta.code != '200')  return;

            // return if no data
            if( res.data.profiling.length == 0 && res.data.research.length == 0 ) return;

            // remove no survey label
            $('#surveyList li.no-survey-available').remove();

            // load research data
            renderResearchItems(res.data.research.reverse());

            // load profiling data
            renderProfilingItems(res.data.profiling);

            // load Fulcrum research data
           renderFulcrumResearchItems(res.data.fulcrum_research)
    
            // load Fulcrum user agreemetns
            renderFulcrumUserAgreementItems(res.data.user_agreement)

        // load Cint research data
        // load Cint usr agreemetns
        };

        var $preview = $('#preview').val();
        if ($preview){
            mockResponse();
        }

        $.ajax({
            type: 'GET',
            url: $('#sop_api_url').val(),
            dataType: 'jsonp',
            jsonp: false,
            cache: true
        });
        // preview
        if ($preview){
            function mockResponse() {
                var callback = surveylistCallback;
                surveylistCallback = function(res){
                    dummy_res = { 'meta' : {'code': '200' },
                           'data': {
                               "profiling": [
                                   {
                                       "url": "https://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?sig=2cec964cd9cd901d17725bd08131976a3ced393b160708fcce2d7767802023c5&next=%2Fprofile%2Fp%2Fq004&time=1438677550&app_id=25&sop_locale=&app_mid=13",
                                       "name": "q004",
                                       "title": "profiling"
                                   }
                               ],
                               "research": [
                                   {
                                       "date": "2015-07-21",
                                       "is_answered": "0",
                                       "cpi": "0.00",
                                       "is_closed": "0",
                                       "ir": "0",
                                       "extra_info": {
                                           "point": {
                                               "screenout": "30",
                                               "quotafull": "30",
                                               "complete": "670"
                                           },
                                           "date": {
                                               "end_at": "2015-08-31 00:00:00",
                                               "start_at": "2015-07-21 00:00:00"
                                           },
                                           "content": ""
                                       },
                                       "url": "https://partners.surveyon.com.dev.researchpanelasia.com/resource/auth/v1_1?sig=aaeca59caa406fff786976df7300ddc69992f75ffdbb4ea0616a868cf58062e5&next=%2Fproject_survey%2F393&time=1438677550&app_id=25&sop_locale=&app_mid=13",
                                       "loi": "15",
                                       "title": "Test 4",
                                       "survey_id": "284",
                                       "quota_id": "393"
                                   }
                               ],
                               'user_agreement':[
                                 {
                                   "type": "Fulcrum",
                                   "url": "http://researchpanelasia.com"
                                 },
                                 {
                                   "type": "Cint",
                                   "url": "http://www.d8aspring.com"
                                 }
                               ],
                               'fulcrum_research':[
                                 {
                                   "survey_id": "4",
                                   "quota_id": "10",
                                   "cpi": "0.00",
                                   "ir": "80",
                                   "loi": "10",
                                   "title": "来自Fulcrum的调查问卷",
                                   "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=test2",
                                   "date": "2015-01-01",
                                   "extra_info": {
                                       "point": {"complete": "10"}
                                   }
                                 }
                               ],
                               "cint_research": [
                                   {
                                     "survey_id": "10000",
                                     "quota_id": "20000",
                                     "cpi": "0.00",
                                     "ir": "80",
                                     "loi": "10",
                                     "is_answered": "0",
                                     "is_closed": "0",
                                     "title": "Cint Survey",
                                     "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=test2",
                                     "date": "2015-01-01",
                                     "extra_info": {
                                       "point": {
                                         "complete": "40",
                                         "screenout": "10",
                                         "quotafull": "10"
                                       }
                                     }
                                   },
                                   {
                                     "survey_id": "10002",
                                     "quota_id": "20000",
                                     "cpi": "0.00",
                                     "ir": "80",
                                     "loi": "10",
                                     "is_answered": "1",
                                     "is_closed": "0",
                                     "title": "Cint Survey2",
                                     "url": "https://partners.surveyon.com/resource/auth/v1_1?sig=e523d747983fb8adcfd858b432bc7d15490fae8f5ccb16c75f8f72e86c37672b&next=%2Fproject_survey%2F23456&time=1416302209&app_id=22&app_mid=test2",
                                     "date": "2015-01-01",
                                     "extra_info": {
                                       "point": {
                                         "complete": "40",
                                         "screenout": "10",
                                         "quotafull": "10"
                                       }
                                     }
                                   }
                                 ]
                               }};
             console.log("mockResponse2");
             callback(dummy_res);
           };
         }
        }

    });
});
