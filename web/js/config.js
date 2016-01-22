//配置要加载的文件
require.config({
    //设置所有引入的js文件的相对根路径
    baseUrl: "/js",
    // 版本号，开发，测试期间可以用时间戳作为版本号，正式发布可用指定版本号
    urlArgs: "bust=" + (new Date()).getTime(),
    paths: {//设置路径
        'jquery': 'lib/jquery-1.11.1.min',
        'jqueryCookie': 'lib/jquery.cookie',
        'installer':'lib/air_installer',
        'swfobject':'lib/swfobject',
        'backbone': 'lib/backbone-1.1.2.min',
        'underscore': 'lib/underscore-min',
        'jquery.ui.widget': 'plugin/fileUpload/jquery.ui.widget',
        'fileUpload': 'plugin/fileUpload/jquery.fileupload',
        'transport': 'plugin/fileUpload/jquery.iframe-transport',
        'jcrop': 'plugin/Jcrop/jquery.Jcrop',
        'layDate': 'plugin/layDate/layDate',
        'iframeResizer': 'plugin/iframeResizer/iframeResizer.min',
        'router': '../bundles/fosjsrouting/js/router',
        'routing':'fos_js_routes',
        'common':'common/common',
        'slider': 'common/slider',
        'countdown': 'common/countdown',
        'textScroll': 'common/textScroll',
        'numScroll': 'common/numScroll',
        'tab': 'common/tab',
        'validate': 'common/validate',
        'mobile': 'common/validate_mobile',
        'alipay': 'common/validate_alipay',
        'expand': 'common/expand',
        'autoJump': 'common/autoJump',
        'layer': 'common/layer',
        'exchange': 'common/exchange',
        'goTop': 'common/backTop',
        'sopSurvey': 'common/sopSurvey'
    },
     // map里面的js意味着 在加载requirejs配置里面的所有js加载前加载。
    map: {
        '*': {
            'css': 'lib/css'
        }
    },
    // shim里面的配置，表示某个js加载前，必须加载指定的js文件
    shim: {
        installer:{
            deps:[],
            exports: 'installer'
        },
        swfobject:{
            deps:[],
            exports: 'swfobject'
        },
        fileUpload: {
            deps: ['jquery','jquery.ui.widget', 'transport'],
            exports: 'fileUpload'
        },
        jcrop: {
            deps: ['jquery','css!plugin/Jcrop/css/jquery.Jcrop.min.css'],
            exports: 'jcrop'
        },
        iframeResizer: {
            deps: [],
            exports: 'iframeResizer'
        },
        routing: {
            deps: ['router'],
            exports: 'routing'
        },
        backbone: {
            deps: ['underscore', 'jquery'],
            exports: 'Backbone'
        },
        underscore: {
            exports: '_'
        },
        jqueryCookie: {
            deps: [ 'jquery'],
        }
                  
    }
});



