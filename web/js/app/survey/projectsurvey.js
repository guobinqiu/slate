require(['../../config'], function() {
    require(['common']);

    require(['jquery'], function($) {

        console.log("survey_id: " + $('#survey_id').val());
        console.log("url: " + $('#url').val());

        var survey_id = $('#survey_id').val();

        var showMessage = function(message){
            $(".error-message").html(message);
            $(".error-message").show();
        };

        surveylistCallback = function(res) {

            console.log("surveylistCallback: code:" + res.meta.code);
            console.log("surveylistCallback: code:" +survey_id);

            // show alert if error code
            if (res.meta.code != '200') {
                showMessage('Oops! Something went wrong.');
                return;
            }

            // get research
            var research;
            $.each(res.data.research, function( index, value ) {
                if(value.survey_id == survey_id
                        && value.is_answered == '0'){
                    research = value;
                    return ;
                }
              });

            // no reasearch
            if ( !research ){
                showMessage("您好!本次调查已经结束。");
                return;
            }
            // interpolate label
            $('h2').text(research.title);
            $('#no').text('r' + research.survey_id);
            $('#point').text(research.extra_info.point.complete);
            if(research.extra_info.content){
              $('#note').append('<br>※' + research.extra_info.content);
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