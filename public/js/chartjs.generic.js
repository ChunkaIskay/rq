window.onload = function() {
  var ctx = document.getElementById(canvas1).getContext('2d');
  window.myBar = new Chart(ctx, {
    type: tipo,
     data: barChartData ,
   options: {

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
                  id: 'A',
                 // type: 'linear',
                  position: 'left',
                  ticks: {
                    max: 100,
                    min: 0,
                    maxTicksLimit: 10,
                              padding: 5,
                  },
                  gridLines: {
                              color: "rgb(234, 236, 244)",
                              zeroLineColor: "rgb(234, 236, 244)",
                              drawBorder: true,
                              borderDash: [2],
                              zeroLineBorderDash: [2]
                            }
                }, {
                  id: 'B',
                 // type: 'linear',
                  position: 'right',
                  ticks: {
                    max: 100,
                    min: 0,
                    maxTicksLimit: 10,
                              padding: 5,
                  },
                  gridLines: {
                              color: "rgb(234, 236, 244)",
                              zeroLineColor: "rgb(234, 236, 244)",
                              drawBorder: true,
                              borderDash: [2],
                              zeroLineBorderDash: [2]
                            }
                },
      ]},
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
          text: 'Reporte estadistico Comparativo'
        },
        layout: {
          padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 210
          }
        },
  },

  });

};