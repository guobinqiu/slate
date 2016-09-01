/*-------------------
依赖jquery.js, touchSwipe.js
-------------------*/
function num_count(){
    $('.count').each(function(){
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()

        }, {
            duration: 1000,
            easing: 'swing',
            step: function(now){
                $(this).text(Math.ceil(now));
            }
        });
    });
}

$('#pagepiling').pagepiling({
    menu: null,
    direction: 'vertical',
    verticalCentered: true,
    sectionsColor: [],
    anchors: [],
    scrollingSpeed: 700,
    easing: 'swing',
    loopBottom: true,
    loopTop: false,
    css3: true,
    navigation: {
        'textColor': '#000',
        'bulletsColor': '#000',
        'position': 'right',
        'tooltips': ['section1', 'section2', 'section3', 'section4']
    },
    normalScrollElements: null,
    normalScrollElementTouchThreshold: 5,
    touchSensitivity: 5,
    keyboardScrolling: true,
    sectionSelector: '.section',
    animateAnchor: false,

    //events
    onLeave: function(index, nextIndex, direction){
        //after leave section0
        if(index == 1 && direction == 'down'){
             num_count();
        }
    },
    afterLoad: function(anchorLink, index){},
    afterRender: function(){
        $('#pagepiling').show();
    },
});

$('.arrow-down').click(function(){
    $.fn.pagepiling.moveSectionDown();
});

//prevent scroll on mobile
if($(window).width() < 667){
    $.fn.pagepiling.setAllowScrolling(false);
}

//menu dropdown
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