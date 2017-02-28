$(function(){
    filterCity();

    $('#province').on('change', function(){
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