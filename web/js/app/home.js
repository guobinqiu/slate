require(['../config'],function(){
    require(['common']);
    // require(['jquery','slider'], function($, slider){
    //    var sliderA = new slider({sliderEle: '.main-slider ul', prevBtn: '.arrowL', nextBtn: '.arrowR', groupBtn: '.btn-group b', config: {
    //        index: 0,
    //        stepWid: 1000,
    //        timer: 2000,
    //        animateTimer: 1000,
    //        eleLen: 4,
    //        isAuto: true,
    //        effect: 'fade'
    //    }});
    // });
    require(['countdown']);
    require(['jquery', 'jqueryCookie'], function($){
        //新手引导部分
        function shouldShow(){
            var vp  = $.cookie('guide');
            if (vp == undefined || vp == 1) {
                return true;
            } else {
                return false;
            }
        }
        $('#mask, #newguideWrap, #newguideWrap div:eq(0)').show();
        $('#newguideWrap a.ngbtn').click(function(){
            var current = $(this).parent().parent();
            current.hide();
            current.next().show();
        });
        $(document.body).click(function(event){
            var target = $(event.target);
            if(target.is('.ngbtn1, .ngbtn2')){ return false; }
            $.cookie('guide', 0, { expires: 10000, path: '/' });
            $('#mask, #newguideWrap').hide();
        });
        if(shouldShow()){
            $('#mask, #newguideWrap').show();
        }else{
            $('#mask, #newguideWrap').hide();
        }
    });


    require(['jquery', 'sopSurvey', 'backbone', 'routing','jqueryCookie'], function($, survey, backbone, routing) {

        var pop_survey_window = function(element) {
            window.open(
                element.getAttribute('href'),
                'enquete_window',
                'resizable=yes,scrollbars=yes,toolbar=no'
            );
        };

        var addSuveyItem = function (el) {

            var surveyList = $('#surveyList'), $div = $('<div></div>');
                // Insert the item as the 2rd row of the table if table has more than 0 row
            if (surveyList.children().length) {
                surveyList.append($(el));
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

        var renderResearchItems = function (items, num) {
            var flag = 0;
            _.each(items, function (item, index) {
                if(flag == num) return true;
                if(item.is_answered == 0){
                    var model = new survey.ResearchItemModel(item);
                    var view  = new survey.ResearchItemView({ model: model });
                    addSuveyItem(view.render().el);
                    flag++;                    
                }
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
                // view.render();
                addSuveyItem(view.render().el);
            });
        };

        var renderFulcrumResearchItems = function (items, num) {
            if(num == 1){
                var model = new survey.FulcrumResearchItemModel(items[0]);
                var view  = new survey.FulcrumResearchItemView({ model: model });
                addSuveyItem(view.render().el);
            }else{
                _.each(items, function (item, index) {
                    if(index <= 1){
                        var model = new survey.FulcrumResearchItemModel(item);
                        var view  = new survey.FulcrumResearchItemView({ model: model });
                        addSuveyItem(view.render().el);
                    }
                });
            }
        };

        var renderCintUserAgreementItems = function (items) {
            _.chain(items)
            .filter(function (item) {
                return item.type == 'Cint';
            })
            .each(function (item) {
                var model = new survey.CintAgreementModel(item);
                var view = new survey.CintUserAgreementView({"model": model});
                // view.render();
                addSuveyItem(view.render().el);
            });
        };

        var renderCintResearchItems = function (items, num) {
            var flag = 0;
            _.each(items, function (item, index) {
                if(flag == num) return true;
                if(item.is_answered == 0){
                    var model = new survey.CintResearchItemModel(item);
                    var view  = new survey.CintResearchItemView({ model: model });
                    addSuveyItem(view.render().el);
                    flag++;                    
                }
            });
        };

        var fillOtherSurvey = function(res, num, type){
            if(calcAnswerableSurvey(res.data.cint_research) > 0 && type == 'Cint'){
                renderCintResearchItems(res.data.cint_research, 1);
                return true;
            }
            if(res.data.fulcrum_research.length != 0 && type == 'Fulcrum'){
                renderFulcrumResearchItems(res.data.fulcrum_research, 1);
                return true;
            }
            if(res.data.user_agreement.length != 0 && type == 'UserAgreement'){
                if(res.data.user_agreement.length == 1){
                    if(res.data.user_agreement[0].type == 'Cint'){
                        renderCintUserAgreementItems(res.data.user_agreement);
                    }else if(res.data.user_agreement[0].type == 'Fulcrum'){
                        renderFulcrumUserAgreementItems(res.data.user_agreement);
                    }
                }else{
                    renderCintUserAgreementItems(res.data.user_agreement);
                }
                return true;
            }
            if(res.data.profiling.length != 0 && type == 'Profiling'){
                renderProfilingItems(res.data.profiling);
                return true;
            }
            if(calcAnswerableSurvey(res.data.research) > 0 && type == 'Research'){
                renderResearchItems(res.data.research, 1);
                return true;
            }
            return false;
        };

        var calcAnswerableSurvey = function(data){
            var answerableNum = 0;
            for(var i = 0; i < data.length; i++){
                if(data[i].is_answered == 0){
                    answerableNum++;
                }
            }
            return answerableNum;
        };

        var showSopTypeSurvey = function(func, resData, type){
            var lackNum;
            if(type == 'Fulcrum'){
                if(resData.length >= 2){
                    func(resData, 2);
                    lackNum = 0;
                }else if(resData.length == 1){
                    func(resData, 1);
                    lackNum = 1;
                }
            }else{
                if(calcAnswerableSurvey(resData) >= 2){
                    func(resData, 2);
                    lackNum = 0;
                }else if(calcAnswerableSurvey(resData) == 1){
                    func(resData, 1);
                    lackNum = 1;
                }
            }
            return lackNum;
        };

        var showSopSurvey = function(res, num){
            if(num == 1){
                if(fillOtherSurvey(res, 1, 'Cint')||fillOtherSurvey(res, 1, 'Fulcrum')||fillOtherSurvey(res, 1, 'UserAgreement')||fillOtherSurvey(res, 1, 'Profiling')||fillOtherSurvey(res, 1, 'Research')){
                    return true;
                }
            }else{
                var lackNum = showSopTypeSurvey(renderCintResearchItems, res.data.cint_research.reverse(), 'Cint');
                if(lackNum == 0){ return;}
                if(lackNum == 1){
                    if(fillOtherSurvey(res, 1, 'Fulcrum') || fillOtherSurvey(res, 1, 'UserAgreement') || fillOtherSurvey(res, 1, 'Profiling') || fillOtherSurvey(res, 1, 'Research') || showSsiSurvey(1)){ return true;}
                }else{
                    lackNum = showSopTypeSurvey(renderFulcrumResearchItems, res.data.fulcrum_research.reverse(), 'Fulcrum');
                    if(lackNum == 0){ return;}
                    if(lackNum == 1){
                        if(fillOtherSurvey(res, 1, 'UserAgreement') || fillOtherSurvey(res, 1, 'Profiling') || fillOtherSurvey(res, 1, 'Research') || showSsiSurvey(1)){ return true;}
                    }else{
                        lackNum = showUserAgreementSurvey(res, 2);
                        if(lackNum == 0){ return;}
                        if(lackNum == 1){
                            if(fillOtherSurvey(res, 1, 'Profiling') || fillOtherSurvey(res, 1, 'Research') || showSsiSurvey(1)){ return true;}
                        }else{
                            if(res.data.profiling.length != 0){
                                renderProfilingItems(res.data.profiling);
                                if(fillOtherSurvey(res, 1, 'Research') || showSsiSurvey(1)){ return true;}
                            }else{
                                lackNum = showSopTypeSurvey(renderResearchItems, res.data.research, 'Research');
                                if(lackNum == 0){ return;}
                                if(lackNum == 1){
                                    if(!showSsiSurvey(1)){ return;}
                                }else{
                                    showSsiSurvey(2);
                                }
                            }
                        }
                    }
                }
            }
        };

        var hideSurvey = function(){
            if($('#surveyList').children().length == 0){
                $('#surveyHome').hide();  
            }else{
                showSsiSurvey(2);
            }
        };

        var hideSsiSurvey = function(){
            if($('#surveyList').children().length != 0){
                var ssi = $('.ssi');
                if(ssi.length != 0){
                    ssi.hide();
                    return;
                }
            }
        };

        var showSsiSurvey = function(num){
            if($('#surveyList').children().length != 0){
                var ssi = $('.ssi');
                if(ssi.length != 0){
                    if(num == 1){
                        ssi.eq(0).insertAfter($('#surveyList>li:last')).show();
                        return true;    
                    }else{
                        ssi.hide();
                        for(var i = 0; i < 2; i++){
                            ssi.eq(i).show();
                        }    
                        return true;
                    }
                }
            }
        };

        var showUserAgreementSurvey = function(res, num){
            var lackNum;
            if(num == 1){
                if(res.data.user_agreement.length == 1){
                    if(res.data.user_agreement[0].type == 'Cint'){
                        renderCintUserAgreementItems(res.data.user_agreement);
                    }else if(res.data.user_agreement[0].type == 'Fulcrum'){
                        renderFulcrumUserAgreementItems(res.data.user_agreement);
                    }
                    lackNum = 0;
                }else{
                    lackNum = 1;
                }
            }else{
                if(res.data.user_agreement.length >= 2){
                    renderCintUserAgreementItems(res.data.user_agreement);
                    renderFulcrumUserAgreementItems(res.data.user_agreement);
                    lackNum = 0;
                }else if(res.data.user_agreement.length == 1){
                    renderCintUserAgreementItems(res.data.user_agreement);
                    renderFulcrumUserAgreementItems(res.data.user_agreement);
                    lackNum = 1;
                }
            }
            return lackNum;
        };

        var calcSopSurvey = function(res){
            var surveySopLen = res.data.profiling.length + res.data.user_agreement.length + res.data.fulcrum_research.length;
            var researchLen = calcAnswerableSurvey(res.data.research), cintResearchLen = calcAnswerableSurvey(res.data.cint_research);
            var totalLen = surveySopLen + researchLen + cintResearchLen;
            return totalLen;
        };

        surveylistCallback = function (res) {

            // return if error code
            if (res.meta.code != '200'){
                hideSurvey();
                return;
            }  

            // return if no data
            var surveySopLen = calcSopSurvey(res);
            if(surveySopLen == 0){
                hideSurvey(); 
                return;
            }else if(surveySopLen == 1){
                //sop
                showSopSurvey(res, 1);
                //ssi
                showSsiSurvey(1);
            }else{
                //ssi
                hideSsiSurvey();
                //sop
                showSopSurvey(res, 2);
            }
        };

        $.ajax({
            type: 'GET',
            url: $('#sop_api_url').val(),
            dataType: 'jsonp',
            jsonp: false,
            cache: true
        });

        function mockResponse() {
            var callback = surveylistCallback;
            surveylistCallback = function(res){
                dummy_res = { 
                    'meta' : {'code': '200' },
                    'data' : {
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
                    }
                };
                callback(dummy_res);
            };
        }
        // preview
        var $preview = $('#preview').val();
        if ($preview){
            mockResponse();
        }
    });
});