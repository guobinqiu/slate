define(['jquery', 'touchSwipe'],function($){
    var defaults = {
        'container' : '#container',//容器
        'sections' : '.section',//子容器
        'easing' : 'ease',//特效方式，ease-in,ease-out,linear
        'duration' : 1000,//每次动画执行的时间
        'pagination' : true,//是否显示分页
        'loop' : false,//是否循环
        'keyboard' : true,//是否支持键盘
        'direction' : 'vertical',//滑动的方向 horizontal,vertical,
        'onpageSwitch' : function(pagenum){}
    };

    var win = $(window),
        container,sections;

    var opts = {},
        canScroll = true;

    var iIndex = 0;

    var arrElement = [];

    var SP = $.fn.switchPage = function(options){
        opts = $.extend({}, defaults , options||{});

        container = $(opts.container),
        sections = container.find(opts.sections);

        sections.each(function(){
            arrElement.push($(this));
        });
        $(opts.sections).eq(0).addClass('active');
        return this.each(function(){
            if(opts.direction == "horizontal"){
                initLayout();
            }

            if(opts.pagination){
                initPagination();
            }

            if(opts.keyboard){
                keyDown();
            }
        });
    }

    //滚轮向上滑动事件
    SP.moveSectionUp = function(){
        if(iIndex){
            iIndex--;
        }else if(opts.loop){
            iIndex = arrElement.length-1;
        }
        scrollPage(arrElement[iIndex]);
    };

    //滚轮向下滑动事件
    SP.moveSectionDown = function(){
        if(iIndex<(arrElement.length-1)){
            iIndex++;
        }else if(opts.loop){
            iIndex = 0;
        }
        scrollPage(arrElement[iIndex]);
    };

    //私有方法
    //页面滚动事件
    function scrollPage(element){
        var dest = element.position();
        if(typeof dest === 'undefined'){ return; }
        initEffects(dest,element);
    }

    //重写鼠标滑动事件
    $(document).on("mousewheel DOMMouseScroll", MouseWheelHandler);
    function MouseWheelHandler(e) {
        e.preventDefault();
        var value = e.originalEvent.wheelDelta || -e.originalEvent.detail;
        var delta = Math.max(-1, Math.min(1, value));
        if(canScroll){
            if (delta < 0) {
                SP.moveSectionDown();
            }else {
                SP.moveSectionUp();
            }
        }
        return false;
    }

    // 触摸手机全屏上下滑动滚屏
     // $(document).ready(function(){
     $(document).on("touchmove", function(){
        $('#container').swipe({
            swipe: function(event, direction, distance, duration, fingerCount){
                if(direction == "up"){
                    SP.moveSectionDown();
                }else if(direction == "down"){
                    SP.moveSectionUp();
                }
                $.event.special.swipe.verticalDistanceThreshold = 5;
            }
        });                   
    });

    //横向布局初始化
    function initLayout(){
        var length = sections.length,
            width = (length*100)+"%",
            cellWidth = (100/length).toFixed(2)+"%";
        container.width(width).addClass("left");
        sections.width(cellWidth).addClass("left");
    }

    //初始化分页
    function initPagination(){
        var length = sections.length;
        if(length){ }
        var pageHtml = '<ul id="pages"><li class="active"></li>';
        for(var i=1;i<length;i++){
            pageHtml += '<li></li>';
        }
        pageHtml += '</ul>';
        $("body").append(pageHtml);
        var pages = $("#pages li");
        pages.on('click', function(){
            var index = pages.index(this);
            scrollPage(arrElement[index]);
            pages.eq(index).addClass("active").siblings().removeClass("active");
            iIndex = index;
        });
    }

    //分页事件
    function paginationHandler(){
        var pages = $("#pages li");
        pages.eq(iIndex).addClass("active").siblings().removeClass("active");
    }

    //是否支持css的某个属性
    function isSuportCss(property){
        var body = $("body")[0];
        for(var i=0; i<property.length;i++){
            if(property[i] in body.style){
                return true;
            }
        }
        return false;
    }

    var s;
    function initNum(){
        $('.refNum').find('b').text('0');
    }
    function calcNum(endNum, time){
        var num = { 
            interval: Math.ceil(endNum/time), 
            remainder: endNum%time
        };
        return num;
    }

    function addCon(endNum, ele){
        var curNum = parseInt($(ele).text());
        var num = calcNum(endNum, 8);
        if(curNum >= endNum){ 
            clearInterval(s); 
            if(num.remainder != 0){
                curNum = curNum - num.remainder;     
            }
            $(ele).text(curNum);
            return false;
        }
        curNum = curNum + num.interval;
        $(ele).text(curNum);
    }
    initNum();
    //渲染效果
    function initEffects(dest,element){
        var transform = ["-webkit-transform","-ms-transform","-moz-transform","transform"],
            transition = ["-webkit-transition","-ms-transition","-moz-transition","transition"];

        canScroll = false;
        if(isSuportCss(transform) && isSuportCss(transition)){
            var traslate = "";
                traslate = "0px, -"+dest.top+"px, 0px";
            container.css({
                "transition":"all "+opts.duration+"ms "+opts.easing,
                "transform":"translate3d("+traslate+")"
            });
            container.on("webkitTransitionEnd msTransitionend mozTransitionend transitionend",function(){
                canScroll = true;
            });
        }else{
            var cssObj = (opts.direction == "horizontal")?{left: -dest.left}:{top: -dest.top};
            container.animate(cssObj, opts.duration, function(){
                canScroll = true;
            });
        }
        element.addClass("active").siblings().removeClass('showNum');
        if(element.find('.refNum').length >= 1){
            element.addClass("showNum");
        }
        if($('#section1').hasClass('showNum')){
            s = setInterval(function(){
                $.each(element.find('.refNum b'), function(i, e){
                    addCon(refNum[i], e);
                });
            }, 200);    
        }else{
            // initNum();
            clearInterval(s); 
        }
        if(opts.pagination){
            paginationHandler();
        }
    }

    //窗口Resize
    var resizeId;
    win.resize(function(){
        clearTimeout(resizeId);
        resizeId = setTimeout(function(){
            reBuild();
        },500);
    });

    function reBuild(){
        var currentHeight = win.height(),
            currentWidth = win.width();

        var element = arrElement[iIndex];
        var offsetTop = element.offset().top;
        if(Math.abs(offsetTop)>currentHeight/2 && iIndex <(arrElement.length-1)){
            iIndex ++;
        }
        
        if(iIndex){
            paginationHandler();
            var cuerrentElement = arrElement[iIndex],
                dest = cuerrentElement.position();
            initEffects(dest,cuerrentElement);
        }
    }

    //绑定键盘事件
    function keyDown(){
        var keydownId;
        win.keydown(function(e){
            clearTimeout(keydownId);
            keydownId = setTimeout(function(){
                var keyCode = e.keyCode;
                if(keyCode == 38){
                    SP.moveSectionUp();
                }else if(keyCode == 40){
                    SP.moveSectionDown();
                }
            },150);
        });
    }

    $('.arrow-down').on('click', function(){
        SP.moveSectionDown();
    });
    $("#container").switchPage({
        'loop' : true,
        'keyboard' : true,
    });
    var refNum = [8, 41500, 5000000];
});