require(['./../../config'], function() {

    require([ 'jquery', 'routing','backbone','jqueryCookie'], function($, routing, Backbone ) {
      console.log($.cookie);
    window.FulcrumAgreementModel = Backbone.Model.extend({
        defaults: {
             type        : ''
            ,url          : ''
        }
    });


    window.FulcrumResearchItemModel = Backbone.Model.extend({
        defaults: {
             title        : ''
            ,survey_id    : ''
            ,url          : ''
            ,extra_info   : {}
        }
        ,getNumLabel: function(){
            return 'f' + this.get('survey_id');
        }
    });


    window.FulcrumUserAgreementView = Backbone.View.extend({
         tagName: 'tr'
        ,template: '#sop-fulcrum-user-agreement-item-template'
        ,events: {
            'click .open-link': 'openSurvey'
        }
        ,initialize: function () {
          _.bindAll(this, 'render', 'openSurvey');
        }
        ,render: function () {
            var tmpl = _.template(
                $(this.template).html(), { model: this.model }
            );
            this.$el.html(tmpl);
            $('#survey-list > tbody:first')
                .find('tr:first')
                .after(this.$el);
            return this;
        }
        ,openSurvey: function (e) {
            window.open(
                this.model.get('url'),
                'sop_window',
                'resizable=yes,scrollbars=yes,toolbar=no'
            );
            e.preventDefault();
        }
    });

    window.FulcrumResearchItemView = Backbone.View.extend({
        tagName: 'tr'
        ,template: '#sop-fulcrum-research-item-template'
        ,initialize: function (args) {
            var events = {
                'click a.open-link': 'openSurvey'
            };
            this.delegateEvents(events);
        }
        ,render: function () {
            var tmpl = _.template(
                $(this.template).html(), { model: this.model }
            );
            this.$el.html(tmpl);
            $('#survey-list > tbody:first')
                .find('tr:first')
                .after(this.$el);
            return this;
        }
        ,openSurvey: function (e) {
            window.open(
                 '/index.php/fulcrum_project_survey/information/' + this.model.get('survey_id')
                ,'sop_research_window'
                ,'resizable=yes,scrollbars=yes,toolbar=no'
            );
            e.preventDefault();
        }
    });

    var renderFulcrumUserAgreementItems = function (items) {
        _.chain(items)
        .filter(function (item) {
            return item.type == 'Fulcrum';
        })
        .each(function (item) {
            var model = new FulcrumAgreementModel(item);
            var view = new FulcrumUserAgreementView({"model": model});
            view.render();
        });
    };
    var renderFulcrumResearchItems = function (items) {
        _.each(items, function (item) {
            var model = new FulcrumResearchItemModel(item);
            var view  = new FulcrumResearchItemView({ model: model });
            addSuveyItem(view.render().el);
        });
    };

    var addSuveyItem = function (el) {

            console.log(el);
return 0;
        var tbodyEl = $('#survey-list > tbody:first');

        $(el).find('span.each').after('<div class="NPSPrompt"><span class="text">此问卷由<em class="darkYellow">60~126道</em>小题组成，可分多次回答，中途关闭不影响继续回答哦！<a class="darkYellow" href="campaign/newProfile" target="_blank">了解详情</a></span><span class="closeTag" onClick="this.close"></span><span class="notShow">不再提示</span></div>');
        $(el).find('span.each').parent().css('position','relative');
            // Insert the item as the 2rd row of the table if table has more than 0 row
        if (tbodyEl.children().length ) {
            tbodyEl.find('tr:first').after($(el));
            return;
        }
        // Append the item to the table if already elements exist
        tbodyEl.append($(el));
    };

    window.surveylistCallback = function (res) {

        console.log('ajax jsonp returned');
        // return if error code
        if (res.meta.code != '200')  return;

        // return if no data
        if( res.data.profiling.length == 0 && res.data.research.length == 0 ) return;

        // remove no survey label
        var tbodyEl = $('#survey-list > tbody:first tr.no-survey-available').remove();

        // load Fulcrum research data
        renderFulcrumResearchItems(res.data.fulcrum_research)
        // load Cint research data
//        renderCintResearchItems(res.data.cint_research)
        // load research data
 //       renderResearchItems(res.data.research.reverse());
        // load profiling data
  //      renderProfilingItems(res.data.profiling);
        // load Fulcrum usr agreemetns
        renderFulcrumUserAgreementItems(res.data.user_agreement)
        // load Cint usr agreemetns
        renderCintUserAgreementItems(res.data.user_agreement)


        closePrompt();
        notShowPrompt();
return 0;
    };

    $(document).ready(function (e) {
        console.log('ready:'+$('#sop-api-url').val());

        $.ajax({
            type:       'GET'
            ,url:       $('#sop-api-url').val()
            ,dataType:  'jsonp'
            ,jsonp:     false
            ,cache:     true
        });
    });

//close popup box when click closeTag
window.closePrompt = function () {
  $(".closeTag").click(function(){
    $(this).parent().hide( "drop", { direction: "right" }, 300 );
  });
};
//This episode is for set cookie for NPS prompt bubble.
window.notShowPrompt = function () {
  if ($.cookie('NPSPrompt') == 'closed') {
      return $('.NPSPrompt').hide();
      }
      $('.NPSPrompt').show();
  $('.notShow').click(function(e) {
      $.cookie('NPSPrompt','closed', { path: '/' });
      $('.NPSPrompt').hide( "drop", { direction: "down" }, 300 );
  });
}
// preview 

function mockResponse(exports) {
  var callback = exports.surveylistCallback;
console.log(callback);
console.log("mockResponse");
console.log(exports);
  exports.surveylistCallback = function(res){

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

  }();
}
mockResponse(window);


   }); //eof  require

});

