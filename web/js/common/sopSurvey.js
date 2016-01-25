define(['backbone'], function(Backbone) {
    var exports = {};
    exports.ProfilingItemModel = Backbone.Model.extend({
        name    : ''
        ,title  : ''
        ,url    : ''
        ,titleMaxLen: 35
        ,initialize: function (args) {
            if (args.title.length > this.titleMaxLen) {
                var title = args.title.slice(0, this.titleMaxLen);
                this.set('title', title + '...');
            }
        }
    });
    exports.ResearchItemModel = Backbone.Model.extend({
         title        : ''
        ,is_answered  : ''
        ,survey_id    : ''
        ,url          : ''
        ,extra_info   : {}
        ,titleMaxLen: 35
        ,initialize: function (args) {
            if (args.title.length > this.titleMaxLen) {
                var title = args.title.slice(0, this.titleMaxLen);
                this.set('title', title + '...');
            }
        }
        ,getAnswerLabel: function(){
            var label;
            if ( this.get('is_answered') == 1 ){
                label = '回答完毕！';
            }else{
                label = '<a href="#" class="open-link">可回答</a>';
            }
            return label;
        }
        ,getNumLabel: function(){
            return 'r' + this.get('survey_id');
        }

    });
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
    exports.CintAgreementModel = Backbone.Model.extend({
        defaults: {
             type        : ''
            ,url          : ''
        }
    });
    exports.CintResearchItemModel = Backbone.Model.extend({
        defaults: {
             title        : ''
            ,survey_id    : ''
            ,url          : ''
            ,extra_info   : {}
        }
        ,getAnswerLabel: function(){
            var label;
            if ( this.get('is_answered') == 1 ){
                label = '回答完毕！';
            }else{
                label = '<a href="#" class="open-link">可回答</a>';
            }
            return label;
        }
        ,getNumLabel: function(){
            return 'c' + this.get('survey_id');
        }
    });
    exports.ProfilingItemView = Backbone.View.extend({
        tagName: 'li'
        ,template: '#sop-profiling-item-template'
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
            return this;
        }
        ,openSurvey: function (e) {
            // Mark item as "done"
            $(e.currentTarget).parent().empty().text('回答完毕！');
            this.insertConversionTags();
            window.open(
                this.model.get('url'),
                'sop_window',
                'resizable=yes,scrollbars=yes,toolbar=no'
            );
            e.preventDefault();
        }
        ,insertConversionTags: function () {
            $(document.body).append(
                '<iframe src="/internal_api/cvt?'
                +
                'sop_url=' + encodeURIComponent(this.model.get('url'))
                +
                '" width="1" height="1" frameborder="0"></iframe>'
            );
        }
    });
    exports.ResearchItemView = Backbone.View.extend({
        tagName: 'li'
        ,template: '#sop-research-item-template'
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
            return this;
        }
        ,openSurvey: function (e) {
            window.open(
                 '/index.php/project_survey/information/' + this.model.get('survey_id')
                ,'sop_research_window'
                ,'resizable=yes,scrollbars=yes,toolbar=no'
            );
            e.preventDefault();
        }
    });

    exports.ProfilingItemCampaignView = Backbone.View.extend({
        tagName: 'li'
        ,template: '#sop-profiling-item-template'
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
            return this;
        }
        ,openSurvey: function (e) {
            // Mark item as "done"
            this.insertConversionTags();
            window.open(
                this.model.get('url'),
                'sop_window',
                'resizable=yes,scrollbars=yes,toolbar=no'
            );
            e.preventDefault();
        }
        ,insertConversionTags: function () {
            $(document.body).append(
                '<iframe src="/internal_api/cvt?'
                +
                'sop_url=' + encodeURIComponent(this.model.get('url'))
                +
                '" width="1" height="1" frameborder="0"></iframe>'
            );
        }
    });
    exports.FulcrumUserAgreementView = Backbone.View.extend({
         tagName: 'li'
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
    exports.FulcrumResearchItemView = Backbone.View.extend({
        tagName: 'li'
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
    exports.CintUserAgreementView = Backbone.View.extend({
         tagName: 'li'
        ,template: '#sop-cint-user-agreement-item-template'
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
    exports.CintResearchItemView = Backbone.View.extend({
        tagName: 'li'
        ,template: '#sop-cint-research-item-template'
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
                 '/index.php/cint_project_survey/information/' + this.model.get('survey_id')
                ,'sop_research_window'
                ,'resizable=yes,scrollbars=yes,toolbar=no'
            );
            e.preventDefault();
        }
    });
    return exports;
});

