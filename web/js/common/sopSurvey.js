define(['backbone'], function(Backbone) {
    var exports = {};
    exports.FulcrumAgreementModel = Backbone.Model.extend({
        defaults: {
             type        : ''
            ,url          : ''
        }
    });


    exports.FulcrumResearchItemModel = Backbone.Model.extend({
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


    exports.FulcrumUserAgreementView = Backbone.View.extend({
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
            exports.open(
                this.model.get('url'),
                'sop_exports',
                'resizable=yes,scrollbars=yes,toolbar=no'
            );
            e.preventDefault();
        }
    });

    exports.FulcrumResearchItemView = Backbone.View.extend({
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
            exports.open(
                 '/index.php/fulcrum_project_survey/information/' + this.model.get('survey_id')
                ,'sop_research_exports'
                ,'resizable=yes,scrollbars=yes,toolbar=no'
            );
            e.preventDefault();
        }
    });
    return exports;
});

