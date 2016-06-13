require(['../config'],function(){
    //require(['common', 'scrollTop']);
    // require(['feedbackForm']);
    // require(['numScroll'], function(numScroll){
    //     new numScroll({ numScrollEle: '.digits b', config: {
    //         digitH : 30,
    //         num: 3688002,
    //         animateTimer: 5000
    //     }});
    // });
    // require(['jquery', 'loginForm'], function($, loginForm){
    //     var loginPwd = {
    //         ele: '#pwd',
    //         prompt: {
    //             isNull: '请输入您的密码',
    //             isFocus: '请输入您的密码'
    //         }
    //     }, loginEmail = {
    //         ele: '#email',
    //         prompt: {
    //             isNull: '请输入邮箱地址',
    //             isFormat: '邮箱地址格式不正确'
    //         },
    //         type: 'email'
    //     };
    //     new loginForm({pwd: loginPwd, email: loginEmail, auto: false});
    //     var submitBtn = $("#submit_button");
    //     submitBtn.on('click', function(e){
    //         var loginform = new loginForm({pwd: loginPwd, email: loginEmail, auto: true});
    //         if(loginform.run(true)){
    //             submitBtn.submit();
    //         }else{
    //             e.preventDefault();
    //         }
    //     });

    //     var $emailError = $("#email_error"), $pwdError = $("#pwd_error");
    //     $emailError.add($pwdError).on('click', function(){
    //         $(this).addClass('fade');
    //     });
    //     var errorCode = $('#error_code').val();
    //     if(errorCode != undefined){
    //         $emailError.html(errorCode).addClass('error').attr('display', 'block');
    //     };
    //     // 点击登录按钮，呈现输入状态
    //     var logFoc = $("a[title='登录']");
    //     var surList = $("a[title='问卷列表']");
    //     logFoc.add(surList).click(function(){
    //         $('#email').focus(); 
    //     });

    //     //sinaWeibo and QQ quick login prompt
    //     var wbLog = $('.weibo-login');
    //     var qqLog = $('.qq-login');
    //     var wqClose = $('.quickLCon .closeBtn').add('.quickLCon .cancelBtn');
    //     var wbPCon = $('#wbLogCon');
    //     var qqPCon = $('#qqLogCon');
    //     wbLog.on('click', function(){
    //         wbPCon.show();
    //         qqPCon.hide();
    //     });
    //     qqLog.on('click', function(){
    //         qqPCon.show();
    //         wbPCon.hide();
    //     });
    //     wqClose.on('click', function(){
    //         wbPCon.add(qqPCon).hide();
    //     });

    //     //点击问卷列表提示先登录
    //     $('.surUnlog').on('click', function(){
    //         $('.logFirst').fadeIn().delay(4000).fadeOut();
    //     });
    // });
    // require(['jquery'], function($){
    //     var $window = $(window),
    //         win_height_padded = $window.height() * 1.1;

    //     $window.on('scroll', revealOnScroll);

    //     function revealOnScroll() {
    //         var scrolled = $window.scrollTop(),
    //             win_height_padded = $window.height() * 1.1;

    //         // Showed...
    //         $(".party:not(.animateing)").each(function() {
    //             var $this = $(this),
    //                 offsetTop = $this.offset().top;

    //             if (scrolled + win_height_padded > offsetTop) {
    //                 if ($this.data('timeout')) {
    //                     window.setTimeout(function() {
    //                         $this.addClass('animateing ' + $this.data('animation'));
    //                     }, parseInt($this.data('timeout'), 10));
    //                 } else {
    //                     $this.addClass('animateing ' + $this.data('animation'));
    //                 }
    //             }
    //         });
    //     }
    //     revealOnScroll();

    //     //scroll down to certain position while click
    //     $('.arrowScroll').on('click', function(e){
    //         e.preventDefault();
    //         $('html, body').animate({
    //             scrollTop: $($(this).attr('href')).offset().top
    //         }, 800);
    //      });

    //     //resize coin background
    //     window.onresize = function(event){
    //         $(".coinBack").css("width", $(window).width());
    //     }

    //     //support placeholder attribute in IE brower
    //      (function($){
    //          $.support.placeholder = ('placeholder' in document.createElement('input'));
    //      });
    //      if(!$.support.placeholder){
    //          $("[placeholder]").focus(function (){
    //              if($(this).val() == $(this).attr("placeholder")) $(this).val("");
    //          }).blur(function(){
    //              if($(this).val() == "") $(this).val($(this).attr("placeholder"));
    //          }).blur();
    //      } 

    // });
    // require(['jquery', 'jqueryCookie'], function($){
    //     //feedback
    //     function shouldShow(){
    //         var vp  = $.cookie('ShoudShowDialog92');
    //         if (vp == undefined || vp == 1) {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     }
    //     var fbCon = $('.fbCon'), fdWrap = $(".fdWrap"), unfdWrap = $(".unfdWrap"), 
    //         closeTag = $('.closeTag'), closeBtn = $('.closeBtn');
    //     closeTag.on('click', function(){
    //         $.cookie('ShoudShowDialog92', 0, { expires: 10000, path: '/' });
    //         fbCon.hide();
    //     });
    //     closeBtn.on('click', function(){
    //         unfdWrap.animate({right: '-420px'}, 300);
    //         fdWrap.animate({right: '0'}, 300);
    //     });
    //     fdWrap.on('click', function(){
    //         fdWrap.animate({right: '-150px'}, 300);
    //         unfdWrap.animate({right: '0'}, 300);
    //     });
    //     if(shouldShow()){
    //         fbCon.show();
    //     }else{
    //         fbCon.hide();
    //     }
    // });
    require(['jquery', 'scrollTop'], function($){
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
            if(length){

            }
            var pageHtml = '<ul id="pages"><li class="active"></li>';
            for(var i=1;i<length;i++){
                pageHtml += '<li></li>';
            }
            pageHtml += '</ul>';
            $("body").append(pageHtml);
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
                interval: parseInt(endNum/time), 
                remainder: endNum%time
            };
            return num;
        }

        function addCon(endNum, ele){
            var curNum = parseInt($(ele).text());
            var num = calcNum(endNum, 200);
            if(curNum >= endNum){ 
                clearInterval(s); 
                curNum = curNum + num.remainder - num.interval; 
                $(ele).text(curNum + '+');
                return false;
            }
            curNum = curNum + num.interval;
            $(ele).text(curNum + '+');
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
            element.addClass("active").siblings().removeClass("active");
            if($('#section1').hasClass('active')){
                s = setInterval(function(){
                    addCon(8457469, '#userNum');
                    addCon(4874112, '#pointNum');
                    addCon(548775111, '#surveyNum');
                }, 20);    
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
                    if(keyCode == 37||keyCode == 38){
                        SP.moveSectionUp();
                    }else if(keyCode == 39||keyCode == 40){
                        SP.moveSectionDown();
                    }
                },150);
            });
        }

        $("#container").switchPage({
            'loop' : true,
            'keyboard' : true,
        });
        
    });
});