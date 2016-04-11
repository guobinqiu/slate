require(['../config'],function(){
    require(['common']);
    //require(['jquery','slider'], function($, slider){
    //    var sliderA = new slider({sliderEle: '.main-slider ul', prevBtn: '.arrowL', nextBtn: '.arrowR', groupBtn: '.btn-group b', config: {
    //        index: 0,
    //        stepWid: 1000,
    //        timer: 2000,
    //        animateTimer: 1000,
    //        eleLen: 4,
    //        isAuto: true,
    //        effect: 'fade'
    //    }});
    //});
    require(['countdown']);
    require(['jquery', 'jqueryCookie'], function($){
        //读取cookie
        var res = document.cookie.substring(5,10);
        //如果没有cookie执行以下操作
        //新手引导部分
        if(res!="guide"){
            var omar = $('.main-con').height() + 385;
            $('#newguideWrap').css('margin-top','-'+ omar +'px');
            $('#mask, #newguideWrap, #newguideWrap div:eq(0)').show();
            $('#newguideWrap a.ngbtn').click(function(){
                var current = $(this).parent().parent();
                current.hide();
                current.next().show();
            });

            $(document.body).click(function(event){
                var target = $(event.target);
                if(target.is('.ngbtn1, .ngbtn2')){ return false; }
                $('#mask, #newguideWrap').hide();
            });
            //添加cookie
            var oDate = new Date();
            oDate.setDate(oDate.getDate() + 10000);
            document.cookie="name=guide;expires=" + oDate;
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

        var renderResearchItems = function (items, num) {
            if(num == 1){
                var model = new survey.FulcrumResearchItemModel(items[0]);
                var view  = new survey.FulcrumResearchItemView({ model: model });
                addSuveyItem(view.render().el);
            }else{
                _.each(items, function (item, index) {
                    if(index <= 1){
                        var model = new survey.ResearchItemModel(item);
                        var view  = new survey.ResearchItemView({ model: model });
                        addSuveyItem(view.render().el);
                    }
                });
            }
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

        var renderFulcrumResearchItems = function (items, num) {
            if(num == 1){
                var model = new survey.FulcrumResearchItemModel(items[0]);
                var view  = new survey.FulcrumResearchItemView({ model: model });
                addSuveyItem(view.render().el);
            }else{
                _.each(items, function (item) {
                    var model = new survey.FulcrumResearchItemModel(item);
                    var view  = new survey.FulcrumResearchItemView({ model: model });
                    addSuveyItem(view.render().el);
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
                view.render();
            });
        };

        var renderCintResearchItems = function (items, num) {
            if(num == 1){
                var model = new survey.CintResearchItemModel(items[0]);
                var view  = new survey.CintResearchItemView({ model: model });
                addSuveyItem(view.render().el);
            }else{
                _.each(items, function (item) {
                    var model = new survey.CintResearchItemModel(item);
                    var view  = new survey.CintResearchItemView({ model: model });
                    addSuveyItem(view.render().el);
                });
            }
        };
        
        var showSurveyOther1 = function(res){
            var ssiSurveyLen = $('#SurveySSILen').val();
            if(res.data.cint_research.length != 0){
                renderCintResearchItems(res.data.cint_research, 1);
            }else if(res.data.fulcrum_research.length != 0){
                renderFulcrumResearchItems(res.data.fulcrum_research, 1);
            }else if(parseInt(ssiSurveyLen) != 0){
                // console.log('show one ssi survey.');
            }else{
                // console.log('no survey!');
                $('#surveyHome').hide();
            }
        };

        var showSurveyOther2 = function(res){
            var ssiSurveyLen = $('#SurveySSILen').val();
            if(res.data.cint_research.length >= 2){
                renderCintResearchItems(res.data.cint_research, 2);
                return;
            }else if(res.data.cint_research.length == 1){
                renderCintResearchItems(res.data.cint_research, 1);
                if(res.data.fulcrum_research.length != 0){
                    renderFulcrumResearchItems(res.data.fulcrum_research, 1);
                }else if(parseInt(ssiSurveyLen) != 0){
                    // console.log('show one ssi survey.');
                }
                return;
            }else{
                if(res.data.fulcrum_research.length >= 2){
                    renderFulcrumResearchItems(res.data.fulcrum_research, 2);
                }else if(res.data.fulcrum_research.length == 1){
                    renderFulcrumResearchItems(res.data.fulcrum_research, 1);
                    if(parseInt(ssiSurveyLen) != 0){
                        // console.log('show one ssi survey.');
                    }
                }else{
                    if(parseInt(ssiSurveyLen) >= 2){
                        // console.log('show two ssi survey.');
                    }else{
                        // console.log('no survey!');
                        $('#surveyHome').hide();
                    }
                }
            } 
        };

        var showSurvey = function(res, curNum){
            switch(res.data.user_agreement.length){
                case 0: 
                    var ssiAgreeLen = $('#UserAgreementSSILen').val();
                    if(parseInt(ssiAgreeLen) == 1){
                        console.log('show ssi user agreement survey.');
                        if(res.data.cint_research.length != 0){
                            renderCintResearchItems(res.data.cint_research, 1);
                        }else if(res.data.fulcrum_research.length != 0){
                            renderFulcrumResearchItems(res.data.fulcrum_research, 1);
                        }
                    }else{
                        if(curNum == 1){
                            showSurveyOther1(res);    
                        }else if(curNum == 0){
                            showSurveyOther2(res);    
                        }                        
                    }
                    break;
                case 1: 
                    if(res.data.user_agreement[0].type == 'Cint'){
                        renderCintUserAgreementItems(res.data.user_agreement);
                        if(curNum == 0){
                            renderFulcrumResearchItems(res.data.fulcrum_research, 1);   
                        }  
                    }else if(res.data.user_agreement[0].type == 'Fulcrum'){
                        renderFulcrumUserAgreementItems(res.data.user_agreement);
                        if(curNum == 0){
                            renderCintResearchItems(res.data.cint_research, 1);  
                        }
                    }
                    break;
                case 2: 
                    renderCintUserAgreementItems(res.data.user_agreement);
                    break;
                default: break;
            }
        }

        surveylistCallback = function (res) {

            // return if error code
            if (res.meta.code != '200'){
                if($('#surveyList').children().length == 0) $('#surveyHome').hide();
                return;
            }  

            // return if no data
            if( res.data.profiling.length == 0 && res.data.research.length == 0 ) {
                if($('#surveyList').children().length == 0) $('#surveyHome').hide();
                return;
            }

            if( res.data.research.length >= 2 ){
                renderResearchItems(res.data.research.reverse(), 2);
            }else{
                if( res.data.research.length == 1){
                    if(res.data.profiling.length == 1){
                        renderResearchItems(res.data.research.reverse(), 1);
                        renderProfilingItems(res.data.profiling);
                        return;
                    }else{
                        renderResearchItems(res.data.research.reverse(), 1);
                        showSurvey(res, 1);
                    }
                }else{
                    if(res.data.profiling.length == 1){
                        renderProfilingItems(res.data.profiling);
                        showSurvey(res, 1);
                    }else{
                        showSurvey(res, 0);
                    }
                }
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
        if (1){
            mockResponse();
        }
    });
});