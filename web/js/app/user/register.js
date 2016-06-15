require(['/js/config.js'],function(){
    require(['jquery', 'validate', 'routing'], function($, validate){
        $('#changeCode').on('click', function(){
            $('#verificationImg').prop('src', Routing.generate("_user_captcha") + '?r=' + 100000*Math.random());
        });
        var signupF = {
            nickname: '#signup_nickname',
            email: '#signup_email',
            pwdF: '#signup_password_first',
            pwdS: '#signup_password_second',
            captcha: '#signup_captcha',
            agreement: '#signup_agreement'
        };
        validate.prompt.pwdRepeat.elements.pwd = "#signup_password_first";
        $.extend(validate.func, {
            regValidate : function() {
                $(signupF.nickname).RPAValidate(validate.prompt.regName, validate.func.regName, true);
                $(signupF.email).RPAValidate(validate.prompt.email, validate.func.email, true);
                $(signupF.pwdF).RPAValidate(validate.prompt.pwd, validate.func.pwd, true);
                $(signupF.pwdS).RPAValidate(validate.prompt.pwdRepeat, validate.func.pwdRepeat, true);
                $(signupF.captcha).RPAValidate(validate.prompt.authCode, validate.func.authCode, true);
                return validate.func.FORM_submit([ signupF.nickname, signupF.email, signupF.pwdF, signupF.pwdS,signupF.captcha ]);
            }
        });
        var pwdStrengthOptions = { pwdStrength: $("#pwdStrength"), pwdError: $(signupF.pwdF+"_error"), value: $.trim($(signupF.pwdF).val())}
        $(signupF.nickname).RPAValidate(validate.prompt.regName, validate.func.regName);
        $(signupF.email).RPAValidate(validate.prompt.email, validate.func.email);
        $(signupF.pwdF).bind("keyup", function(){ validate.func.pwdStrength(pwdStrengthOptions); }).RPAValidate(validate.prompt.pwd, validate.func.pwd);
        $(signupF.pwdS).RPAValidate(validate.prompt.pwdRepeat, validate.func.pwdRepeat);
        $(signupF.captcha).RPAValidate(validate.prompt.authCode, validate.func.authCode);
        function checkReadMe() {
            var readme = $(signupF.agreement),
                protocolError = $("#protocol_error");
            if(readme.prop("checked") == "checked" || readme.prop("checked") == true) {
                protocolError.removeClass();
                return true;
            } else {
                protocolError.removeClass().addClass("error").html("请确认已阅读会员协议");
                return false;
            }
        }
        function validateRegName() {
            var regName = $(signupF.nickname),
                regNameError = $(signupF.nickname+"_error");
            var loginName = $.trim(regName.val());
            if (validate.rules.isNull(loginName) || loginName == '') {
                regName.val("");
                regName.attr({
                    "class": "highlight2"
                });
                regNameError.html("请输入用户名").attr({
                    "class": "error"
                });
                return false;
            }
            return true;
        }
        function reg() {
            var agreeProtocol = checkReadMe();
            var regNameOk = validateRegName();
            var passed = false;

            passed = validate.func.regValidate() && regNameOk && agreeProtocol;
            if (passed) {
                $("#submit_button").attr({
                    "disabled" : "disabled"
                }).removeClass();
                return true;
            } else {
                $("#submit_button").removeAttr("disabled").removeClass();
                return false;
            }
        }
        var tips = $('.tips');
        tips.removeClass('active');
        var lis = $('.login li'), inputs = lis.find('input'), labels = lis.find('label');
        inputs.each(function(i, e){
            if(!$(this).val()){
                labels.eq(i).show();
            }else{
                labels.eq(i).hide();
            }
            $(this).on('keydown', function(){
                labels.eq(i).hide();
            });
            $(this).on('keyup', function(){
                if(!$(this).val()){
                    labels.eq(i).show();
                }else{
                    labels.eq(i).hide();
                }
            }); 
        });
        var $emailError = $("#email_error"), $pwdError = $("#pwd_error");
        $('.register li span').on('click', function(){
            $(this).addClass('fade');
        });
        var signup_form = $('#signup_form');
        var backError = signup_form.find('li span>ul');
        if(backError.length >= 1){
            for(var i = 0; i < backError.length; i++){
                backError.eq(i).parent().siblings().removeClass();
                backError.eq(i).parent().removeClass().addClass('error');
            }
        }
        $('#submit_button').on('click', function(e){
            if(reg()){
                signup_form.submit(); 
            }else{
                e.preventDefault();
                // tips.addClass('active');
            }
        });

    });
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
            if(length){ }
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
