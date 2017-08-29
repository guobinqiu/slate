function fillArr(arr) {
  for (var i = 0; i < arr.length; i++) {
    if (isNaN(arr[i])) {
      arr[i] = 0;
    }
  }
  return arr;
}

function transferData(sourceData) {
  var charData = {
      labels: [],
      maleArr: [],
      femaleArr: [],
      otherArr: []
    };

  //Extract the data
  for (var i = 0, j = 0; i < sourceData.length; i++) {
    var index = charData.labels.indexOf(sourceData[i].areaName);
    if (index == -1) {
      charData.labels[j] = sourceData[i].areaName;
      index = j;
      j++;
    }
    switch (sourceData[i].gender) {
      case "1":
        charData.maleArr[index] = sourceData[i].cnt;
        break;
      case "2":
        charData.femaleArr[index] = sourceData[i].cnt;
        break;
      default:
        charData.otherArr[index] = sourceData[i].cnt;
        break;
    }
  }

  //fill the array
  var len = charData.labels.length,
      maleLen = fillArr(charData.maleArr).length,
      femaleLen = fillArr(charData.femaleArr).length,
      otherLen = fillArr(charData.otherArr).length;
  if (maleLen < len) {
    charData.maleArr = charData.maleArr.concat(Array(len-maleLen).fill(0));
  }
  if (femaleLen < len) {
    charData.femaleArr = charData.femaleArr.concat(Array(len-femaleLen).fill(0));
  }
  if (otherLen < len) {
    charData.otherArr = charData.otherArr.concat(Array(len-otherLen).fill(0));
  }

  return charData;
}

// Define a plugin to provide data labels
Chart.plugins.register({
  afterDatasetsDraw: function(chartInstance, easing) {
    // To only draw at the end of animation, check for easing === 1
    var ctx = chartInstance.chart.ctx;
    var sum = new Array(chartInstance.data.datasets[0].data.length);
    fillArr(sum);
    chartInstance.data.datasets.forEach(function(dataset, i) {
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
          if (i == 2) {
            ctx.fillText(sum[index], position.x, position.y);
          }
        });
      }
    });
  }
});

function load(areaData) {
  var ctx = document.getElementById("myChart").getContext("2d");
  var ctx2 = document.getElementById("myChart2").getContext("2d");

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
      }, {
        label: '女生',
        backgroundColor: window.chartColors.red,
        data: chartData.mauData.femaleArr
      }, {
        label: '其他',
        backgroundColor: window.chartColors.grey,
        data: chartData.mauData.otherArr
      }]
    },
    barChart6auData = {
      labels: chartData.sixauData.labels,
      datasets: [{
        label: '男生',
        backgroundColor: window.chartColors.blue,
        data: chartData.sixauData.maleArr
      }, {
        label: '女生',
        backgroundColor: window.chartColors.red,
        data: chartData.sixauData.femaleArr
      }, {
        label: '其他',
        backgroundColor: window.chartColors.grey,
        data: chartData.sixauData.otherArr
      }]
    };
  window.mauBar = new Chart(ctx, {
    type: 'horizontalBar',
    data: barChartMauData,
    options: {
      title: {
        display: true,
        text: "MAU"
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
  window.sixauBar = new Chart(ctx2, {
    type: 'horizontalBar',
    data: barChart6auData,
    options: {
      title: {
        display: true,
        text: "6AU"
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