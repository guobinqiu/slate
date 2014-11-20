// JavaScript Document
(function ($) {
    var treasure = function(options, element){
        this.options = options;
        this.element = $(element);
        this.create();
    };
    treasure.prototype = {
        //ajax请求获得数据 是否玩过宝箱字段，几个宝箱字段
        create: function(){
            var $this = this;
            var opts = $this.options;
            $.ajax({
                url: '',
                type: "GET",
                dataType: 'json',
                success: function(data){ },
                error: function(){
                    $this.debug('第一次请求错误');
                }
            });
            $this.debug("初始化……", opts);
            var initData = {"state": 0, "num": 3};
            $this.randSort(initData);
            $this.setUp(initData);
        },
        debug: function() {
            var opts = this.options;
            if (opts.debug) {
                return window.console && console.log.call(console, arguments);
            }
        },
        //获取当前分类
        setUp: function(initData){
            var instance = this;
            var opts = instance.options;
            instance.debug('第一次加载页面时的分类', $(opts.sortSelector + '.ui-tabs-active').text());
            opts.curEle = $(opts.sortSelector + '.ui-tabs-active').text();
            instance.showBao(initData);
            $(opts.sortSelector).on('click', function(){
                var index = $(opts.sortSelector).index(this);
                $(opts.sortSelector).removeClass('ui-tabs-active').eq(index).addClass('ui-tabs-active');
                opts.curEle = $(this).text();
				var categoryId = $(this).attr('id');
				if(opts.clickCallback){
                    opts.clickCallback(categoryId);
                }
                instance.showBao(initData);
            });
        },
        //解决在ie7和ie8下indexOf方法不能用的问题
        indexOfRedefine: function(){
            if (!Array.prototype.indexOf){
                Array.prototype.indexOf = function(elt /*, from*/){
                    var len = this.length >>> 0;
                    var from = Number(arguments[1]) || 0;
                    from = (from < 0)? Math.ceil(from): Math.floor(from);
                    if (from < 0) from += len;
                    for (; from < len; from++){
                        if (from in this && this[from] === elt)
                            return from;
                    }
                    return -1;
                };
            }
        },
        //根据宝箱个数随机生成几个分类宝箱位置
        randSort: function(initData){
            var opts = this.options;
            var allSorts = $(opts.sortSelector);
            var sortArr = [], randArr = [], randNum;
            if(allSorts && (initData.num > allSorts.length || initData.num <= 0)){ this.debug('初始宝箱数不对'); return;}
            for(var i = 0, j = 0; randArr.length < initData.num; i++){
                randNum = Math.floor(Math.random()*(0-(allSorts.length - 1)) + (allSorts.length - 1));
                this.indexOfRedefine();
                if(randArr.indexOf(randNum) == -1){
                    randArr[j] = randNum;
                    sortArr[j] = allSorts.eq(randArr[j]).text();
                    j++;
                }
            }
            opts.box.sortsArr = sortArr;
            this.debug("随机数", randArr, "随机分类", sortArr);
        },
        //当点击到随机生成的分类的时候 随机生成位置 设置宝箱位置并显示宝箱
        showBao: function(initData){
            var $this = this;
            var opts = $this.options;
            var divLayer = "<div></div>";
            var imgLayer = '<img alt="宝箱" src="' + opts.box.img + '"/>';
            (new Image()).src = opts.box.img;
            var treasureBox = $('.'+opts.box.className);
            $this.debug('是否显示宝箱……',((opts.box.sortsArr).indexOf(opts.curEle)),initData.state);
            if((opts.box.sortsArr).indexOf(opts.curEle) != -1 && initData.state == 0){
                //根据坐标范围生成随机位置
                var randNumX = Math.floor(Math.random()*(1-opts.box.posNum.col) + opts.box.posNum.col),
                    randNumY = Math.floor(Math.random()*(1-opts.box.posNum.row) + opts.box.posNum.row),
                    marginW = ($(opts.container).find('li:first').outerWidth(true) - opts.box.size.w),
                    marginH = ($(opts.container).find('li:first').outerHeight(true) - opts.box.size.h);
                if(marginW<0 || marginH<0){
                    marginW = opts.box.gap.gapW;
                    marginH = opts.box.gap.gapH;
                }
				$this.debug("偏移元素", $(opts.container).position());
                opts.box.position = { "x" : $(opts.container).position().left + randNumX*(opts.box.size.w+marginW),
                    "y" : $(opts.container).position().top + randNumY*(opts.box.size.h+marginH)};
                $this.debug("随机位置", opts.box.position, randNumX, randNumY, marginW, marginH);
                //设置宝箱位置和大小并显示宝箱
                if(treasureBox.length){
                    treasureBox.remove();
                }
                $(divLayer).html(imgLayer).css({"left": opts.box.position.x + 'px', "top": opts.box.position.y + 'px', "width": opts.box.size.w + 'px', "height": opts.box.size.h + 'px'}).addClass(opts.box.className).appendTo($(opts.container).parent()).mouseover(function(){
                        $(this).animate({ "opacity": 1}, 1000);
                    }).on('click', function(){
                        $this.showResult(initData, $this.showBao);
                    });
            }else{
                if(treasureBox.length){
                    treasureBox.remove();
                }
            }
        },
        getPoint: function(){
            this.debug('获得米粒');
        },
        //加载动画 动画结束关闭并弹出结果
        loadGif: function(){
            var $this = this;
            var opts = $this.options;
            var divLayer = "<div></div>";
            var winCon = "<div><img alt='宝箱' src='" + opts.box.gif + "'/><span></span></div>";
            //(new Image()).src = opts.box.gif;
            var $body = $('body');
            $(divLayer).addClass(opts.theme.maskClass).appendTo($body);
            $(divLayer).addClass(opts.theme.bgClass).append($(winCon).addClass(opts.theme.conClass)).append($(divLayer).addClass(opts.theme.closeClass).on('click', function(){ $this.closeResult()})).appendTo($body);
            this.debug('运行动画');
        },
        closeResult: function(){
            var opts = this.options;
            $('.'+opts.theme.closeClass).hide();
            $('.'+opts.theme.maskClass).hide();
            $('.'+opts.theme.bgClass).hide();
        },
        //显示获得米粒数
        showPoint: function(resultData){
            var opts = this.options;
            this.debug('显示获得米粒数');
            $('.'+opts.theme.conClass).find('span').addClass(opts.theme.resultClass).append('恭喜您，获得<strong>' + resultData.point + '</strong>米粒');
        },
        noPoint: function(){
            var opts = this.options;
            this.debug('没有获得米粒');
            $('.'+opts.theme.conClass).find('span').addClass(opts.theme.resultClass).append('再接再厉哦！');
        },
        //结果层显示
        showResult: function(initData){
            this.debug('打开宝箱……');
            var $this = this;
			var opts = $this.options;
            initData.state = 1;
            //获取中奖状态
            $.ajax({
                url: '',
                type: "GET",
                dataType: 'json',
                data: initData,
                success: function(data){
                },
                error: function(){
                    $this.debug('第二次请求错误');
                }
            });
            var resultData = { "point": 500};
            $this.loadGif();
            //判断结果是否中奖
            if(resultData.point > 0){
                $this.getPoint();
                setTimeout(function(){ $this.showPoint(resultData);$this.showBao(initData);}, opts.box.gifTime);
            }else{
				setTimeout(function(){ $this.noPoint();}, opts.box.gifTime);
            }
        }
    };
    $.treasure = function(options, element){
        options = $.extend(true, {}, $.treasure.defaults, options);
        $.data(element, 'treasure', new treasure(options, element));
        return element;
    };
    $.treasure.defaults = {
        container: '.proList',
        sortSelector: '.sorts li',
        curEle: '',
        box: {
            position: {"x": 0, "y": 0},
            posNum: {"col": 4, "row": 5},
            size: {"w": 180, "h": 200},
			gap: {"gapW": 10, "gapH": 20},
            img: 'images/baoxiang.gif',
            gif: 'images/baoxiang1.gif',
            gifTime: 2500,
            sortsArr: [],
            className: 'treasure'
        },
        theme: {
            maskClass: 'mask',
            bgClass: 'winLayer',
            conClass: 'winCon',
            resultClass: 'winResult',
            closeClass: 'close'
        },
        debug: true,
		clickCallback: function(){}
    };
    $.fn.treasure = function(options){
        return $.treasure(options, this);
    };
})(jQuery);