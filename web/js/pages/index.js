/*-------------------
引用jquery.js, common.js, jquery.touchSwipe.min.js, landing.js
-------------------*/
$(function(){
    var menu = $('ul.menu');
    $('.expandBtn').on('click', function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            menu.removeClass('active');    
        }else{
            $(this).addClass('active');
            menu.addClass('active');
        }
    });
});