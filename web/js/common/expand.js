define(['jquery'],function($){
    var expandBtn = $('.expand-btn'),
        expandCon = $('.expand-con');

        $.each(expandCon, function(i, e){
            expandBtn.eq(i).hover(function(){
                expandCon.eq(i).show();
            }, function(){
                expandCon.eq(i).hide();
            });
        });
});