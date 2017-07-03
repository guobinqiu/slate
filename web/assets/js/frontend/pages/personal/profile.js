/*-------------------
引用jquery.js, routing.js, common.js, jcrop.js, fileUpload.js
-------------------*/
$(function(){
//初始化表单
    $('#profile_save').on('click', function(){
        $('#profileForm').submit();
    });

    //https://github.com/blueimp/JavaScript-Load-Image/blob/master/README.md
    $('#profile_attachment').on('change', function(e){
        //预览本地图片
        var loadingImage = loadImage(
            e.target.files[0],
            function(img) {
                //console.log(img.src);
                //console.log(img.toDataURL());//canvas to base64

                $('.picCut').show();
                $('.picCut .imageInfo').html("<img src='" + img.toDataURL() + "' id='target' />");

                //http://deepliquid.com/content/Jcrop.html
                $('#target').Jcrop({
                    aspectRatio: 1,
                    onChange: showPreview,//拖动选框时触发
                    onSelect: showPreview,//缩放选框时触发
                    onSelect: updateCoords//缩放选框时触发
                });

                $('.picCut .resizeimage').html("<img src='" + img.toDataURL() + "' id='preview' />");
            },
            {
                minWidth: 512,
                maxWidth: 512,
                canvas: true//blob to canvas
            }
        );
        loadingImage.onerror = function() {
            alert('无法读取该图片');
        }
    });

    $('.resBtn').on('click', function(){
        if (!checkCoords()) {
            return;
        }
        $('#uploadForm').submit();
    });

    $('.cancelBtn').on('click', function(){
        $('.picCut').hide();
    });

    //thumbnail
    function showPreview(coords){
        var rx = 150 / coords.w;
        var ry = 150 / coords.h;
        $('#preview').css({
            width: Math.round(rx * $('#target').width()) + 'px',
            height: Math.round(ry * $('#target').height()) + 'px',
            marginLeft: '-' + Math.round(rx * coords.x) + 'px',
            marginTop: '-' + Math.round(ry * coords.y) + 'px'
        });
    }

    //裁切坐标
    function updateCoords(c){
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
    }

    //检查是否成功选取到了
    function checkCoords(){
        if (parseInt($('#w').val())) return true;
        alert('请先选定裁切区域');
        return false;
    }

    $('#province').on('change', function(){
        filterCity();
    });

    $(document).ready(function() {
        filterCity();
    });

    function filterCity() {
        var province = $('#province').find('option:selected').val();
        $('#city').append($('#tmp').children());//把不符合的城市合并回来以获得全量列表
        $('#tmp').append($('#city').children("[data-province!='" + province + "']"));//把不符合条件的城市暂存到另外地方
        $('#city').children("[data-province!='" + province + "']").remove();//把不符合条件的城市从列表中删除
        $('#city').removeAttr("disabled");
    }
});