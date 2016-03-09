require(['../../config'], function() {
    require(['common']);

    require(['jquery', 'routing'], function($, routing) {

        var titleMaxLen = 35;

        $.ajax({
            type: 'GET',
            url: $('#sop_api_url').val(),
            dataType: 'jsonp',
            jsonp: false,
            cache: true
        });

        var showTitle = function(title) {
            if (title.length > titleMaxLen) {
                var title = title.slice(0, titleMaxLen);
                title = title+ '...';
                return title;
            }
            return title;
        }

        var openSurvey = function(pageURL, name) {
            // todo: Mark item as "done"
            insertConversionTags();
            window.open(
                pageURL,
                name,
                'resizable=yes,scrollbars=yes,toolbar=no'
            );
            //todo
            //e.preventDefault();
        };

        //todo
        var insertConversionTags = function() {
        };

        surveylistCallback = function(res) {

            // return if error code
            if (res.meta.code != '200') {
                return;
            }

            // return if no data
            if (res.data.profiling.length == 0 && res.data.research.length == 0) {
                return;
            }

            // remove no survey label
            var tbodyEl = $('#survey-list > tbody:first tr.no-survey-available').remove();

            // load research data
            renderResearchItems(res.data.research.reverse());

            // load profiling data
            renderProfilingItems(res.data.profiling);

        };

        var renderProfilingItems = function(items) {

            var point = $('#sop_point').val();

            $.each(items, function(item, data) {
                var title = showTitle(data.title);

                var str = '<li><div class="survey-item">'+
                '<div class="title"><a href="#">'+title+'</a></div>'+
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
                '<div class="survey-btn" ></div></li>';

                var label = '<a href="#" class="open-link">可回答</a>';
                $('#sop-profiling-item-template').append(($(str).find('.survey-btn')).append($(label).on('click', function(){
                    openSurvey(data.url , 'sop_window');
                })).parent());

            });
        };

        var renderResearchItems = function(items) {
            $.each(items, function(item, data) {
                var title = showTitle(data.title);
                var str = '<li><div class="survey-item">'+
                '<div class="title"><a href="#">'+title+'</a></div>'+
                '<ul class="con clearfix">'+
                    '<li class="first">'+
                        '<span>问卷编号</span>'+
                        '<p>r' + data.survey_id + '</p>' +
                    '</li>'+
                    '<li>'+
                        '<span>问卷类型</span>'+
                        '<p>商业问卷</p>'+
                    '</li>'+
                    '<li>'+
                        '<span>回答期限</span>'+
                        '<p>--</p>'+
                    '</li>'+
                    '<li class="last">'+
                        '<span>积分数</span>'+
                        '<p><strong>'+data.extra_info.point.complete+'</strong></p>'+
                    '</li>'+
                '</ul>'+
                '</div>'+
                '<div class="survey-btn" ></div></li>';

                var label;
                if (data.is_answered == 1) {
                    label = '完毕';
                    $('#sop-research-item-template').append(label);
                } else {
                    label = '<a href="#" class="open-link">可回答</a>';
                    $('#sop-research-item-template').append(($(str).find('.survey-btn')).append($(label).on('click', function(){
                        var url = Routing.generate("_project_survey_information", {"survey_id": data.survey_id });
                        openSurvey(url, 'sop_research_window');
                    })).parent());
                }

            });
        };

    });

});
