define(['jquery'],function($){
    var disScroll;
    var lastScrollTop = 0;
    var delat = 5;
    var navHight = $('#top-header').outerHeight();

    $(window).scroll(function(event){
        disScroll = true;
    });

    setInterval(function (){
        if(disScroll){
            hasScrolled();
            disScroll = false;
        }
    }, 250);

    function hasScrolled(){
        var st = $(this).scrollTop();
        var clientHeight =document.documentElement.clientHeight;
        if(Math.abs(st - lastScrollTop) <= delat){
            return;
        }
        if(st > navHight+120 && st > lastScrollTop){
            $('#top-header').removeClass('header-show').addClass('header-hide');
        }else{
            if(st + $(window).height() < $(document).height()){
                $('#top-header').removeClass('header-hide').addClass('header-show');
            }
        }
        lastScrollTop = st;
    }
});