/*-------------------
引用jquery.js, jqueryCookie.js, common.js, slider.js
-------------------*/
$(function(){
    if(window.location.hostname == 'www.91wenwen.net'){
        if($('.offerWow').length >= 1){
            var websiteid = $('#websiteid').val(), memberid = $('#memberid').val();
            $('iframe').attr('src', 'http://www.offer-wow.com/affiliate/wall/open.do?websiteid='+websiteid +'&styleIndex=1&memberid='+memberid).add('.taskFlow').show();    
        }
        if($('.offer99').length >= 1){
            var pid = $('#pid').val(), userid = $('#userid').val();
            $('iframe').attr('src', 'http://app.offer99.com/index.php?pid='+pid +'&userid='+userid).add('.taskFlow').show();    
        }
        $('.maintenance').hide();
    }else{
        $('iframe').attr('src', '#').add('.taskFlow').hide();
        $('.maintenance').show();
    }
});