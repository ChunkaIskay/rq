function number_format(number, decimals, dec_point, thousands_sep) {
    // *     example: number_format(1234.56, 2, ',', ' ');
    // *     return: '1 234,56'
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
      prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
      sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
      dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
      s = '',
      toFixedFix = function(n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
      };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
      s[1] = s[1] || '';
      s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}


window.onload = function() {
  var ctx = document.getElementById('reporte1').getContext('2d');
  window.myBar = new Chart(ctx, {
    type: 'bar',
    data: barChartData,
    options: {
      responsive: true,
      legend: {
        display: true,
        labels: {
           fontColor: 'rgb(255, 99, 132)'
           
        },
        position: 'bottom',
      },
      title: {
        display: true,
        text: 'Total de requerimientos por mes'
      },
      layout: {
        padding: {
          left: 10,
          right: 25,
          top: 25,
          bottom: 210
        }
      },
      tooltips: {
       //titleMarginBottom: 14,
       //titleFontColor: '#6e707e',
        titleFontColor: '#000',
        titleFontSize: 14,
        bodyFontColor: "#000",
        bodyFontSize: 14,
        //backgroundColor: "rgb(255,255,255)",
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)'
        ],
        borderColor: '#dddfeb',
        borderWidth: 1,
        xPadding: 15,
        yPadding: 15,
        displayColors: true,
        caretPadding: 10,
        callbacks: {
          label: function(tooltipItem, chart) { 
            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
            return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
          }
        }
      },

      scales: {
                xAxes: [{
                  time: {
                    unit: 'month'
                  },
                  gridLines: {
                    display: true,
                    drawBorder: true
                  },
                  ticks: {
                    maxTicksLimit: 12,
                    beginAtZero: true
                  },
                  maxBarThickness: 90,
                }],
                yAxes: [{
                  ticks: {
                    min: 0,
                    max: 100,
                    maxTicksLimit: 8,
                    padding: 10,
                    // Include a dollar sign in the ticks
                    callback: function(value, index, values) {
                      return ' ' + number_format(value);
                    }
                  },
                  gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                  }
                }],
              },
    }
  });

};
