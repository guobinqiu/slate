function fillArr(arr){
    for(var i = 0; i < arr.length; i++){
        if(isNaN(arr[i])){
            arr[i] = 0;
        }
    }
    return arr;
}
function transferData(sourceData){
    var labels = [], 
        maleArr = [], 
        femaleArr = [], 
        otherArr = [];
    for(var i = 0, j = 0; i < sourceData.length; i++){
        var index = labels.indexOf(sourceData[i].areaName);
        if( index == -1){
            labels[j] = sourceData[i].areaName;
            if(sourceData[i].gender == "1"){
                maleArr[j] = sourceData[i].cnt;
            }else if(sourceData[i].gender == "2"){
                femaleArr[j] = sourceData[i].cnt;
            }else{
                otherArr[j] = sourceData[i].cnt;
            } 
            j++;   
        }else{
            if(sourceData[i].gender == "1"){
                maleArr[index] = sourceData[i].cnt;
            }else if(sourceData[i].gender == "2"){
                femaleArr[index] = sourceData[i].cnt;
            }else{
                otherArr[index] = sourceData[i].cnt;
            } 
        }
    }  
    var len = labels.length;
    if(fillArr(maleArr).length < len){ 
        maleArr[len-1] = 0;
    }
    if(fillArr(femaleArr).length < len){ 
        femaleArr[len-1] = 0;
    }
    if(fillArr(otherArr).length < len){ 
        otherArr[len-1] = 0;
    }
    return {labels: labels, 
            maleArr: maleArr, 
            femaleArr: femaleArr, 
            otherArr: otherArr};
}
// Define a plugin to provide data labels
Chart.plugins.register({
    afterDatasetsDraw: function(chartInstance, easing) {
        // To only draw at the end of animation, check for easing === 1
        var ctx = chartInstance.chart.ctx;
        var sum = new Array(chartInstance.data.datasets[0].data.length);
        fillArr(sum);
        chartInstance.data.datasets.forEach(function (dataset, i) {
            var meta = chartInstance.getDatasetMeta(i);
            if (!meta.hidden) {

                meta.data.forEach(function(element, index) {
                    // Draw the text in black, with the specified font
                    ctx.fillStyle = 'rgb(0, 0, 0)';
                    var fontSize = 16;
                    var fontStyle = 'normal';
                    var fontFamily = '微软雅黑';
                    ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);

                    // Just naively convert to string for now                 
                    sum[index] += parseInt(dataset.data[index]);
                    
                    // Make sure alignment settings are correct
                    ctx.textAlign = 'left';
                    ctx.textBaseline = 'middle';

                    var elementLast = chartInstance.getDatasetMeta(2).data[index];
                    var position = elementLast.tooltipPosition();
                    if(i == 2){
                        ctx.fillText(sum[index], position.x, position.y);    
                    }
                });
            }
        });
    }
});
window.onload = function() {
    var ctx = document.getElementById("myChart").getContext("2d"),
        ctx2 = document.getElementById("myChart2").getContext("2d");
    $.ajax({
        url: Routing.generate('admin_feasibility_area_distribution_data'),
        post: "GET",
        success: function(data){
            areaData = eval("(" + data + ")");
            var chartData = { 
                            mauData: transferData(areaData.distributionMau), 
                            sixauData: transferData(areaData.distribution6au)
                            };
            var barChartMauData = {
                    labels: chartData.mauData.labels,
                    datasets: [{
                        label: '男生',
                        backgroundColor: window.chartColors.blue,
                        data: chartData.mauData.maleArr
                    },{
                        label: '女生',
                        backgroundColor: window.chartColors.red,
                        data: chartData.mauData.femaleArr
                    },{
                        label: '其他',
                        backgroundColor: window.chartColors.grey,
                        data: chartData.mauData.otherArr
                    }]
                },
                barChart6auData  = {
                    labels: chartData.sixauData.labels,
                    datasets: [{
                        label: '男生',
                        backgroundColor: window.chartColors.blue,
                        data: chartData.sixauData.maleArr
                    },{
                        label: '女生',
                        backgroundColor: window.chartColors.red,
                        data: chartData.sixauData.femaleArr
                    },{
                        label: '其他',
                        backgroundColor: window.chartColors.grey,
                        data: chartData.sixauData.otherArr
                    }]
                };
            window.mauBar = new Chart(ctx, {
                type: 'horizontalBar',
                data: barChartMauData,
                options: {
                    title:{
                        display:true,
                        text:"MAU"
                    },
                    tooltips: {
                        mode: 'index',
                        callbacks: {
                            // Use the footer callback to display the sum of the items showing in the tooltip
                            footer: function(tooltipItems, data) {
                                var sum = 0;

                                tooltipItems.forEach(function(tooltipItem) {
                                    sum += parseInt(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]);
                                });
                                return '总人数: ' + sum;
                            },
                        },
                        footerFontStyle: 'normal',
                        intersect: true
                    },
                    responsive: true,
                    scales: {
                        xAxes: [{
                            stacked: true,
                        }],
                        yAxes: [{
                            stacked: true
                        }]
                    }
                }
            });
            window.sixauBar2 = new Chart(ctx2, {
                type: 'horizontalBar',
                data: barChart6auData,
                options: {
                    title:{
                        display:true,
                        text:"6AU"
                    },
                    tooltips: {
                        mode: 'index',
                        callbacks: {
                            // Use the footer callback to display the sum of the items showing in the tooltip
                            footer: function(tooltipItems, data) {
                                var sum = 0;

                                tooltipItems.forEach(function(tooltipItem) {
                                    sum += parseInt(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]);
                                });
                                return '总人数: ' + sum;
                            },
                        },
                        intersect: true
                    },
                    responsive: true,
                    scales: {
                        xAxes: [{
                            stacked: true,
                        }],
                        yAxes: [{
                            stacked: true
                        }]
                    }
                }
            });
        }
    });   
};