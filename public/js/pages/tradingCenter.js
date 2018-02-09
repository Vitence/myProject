
//数组处理
function splitData(rawData) {
  var datas = [];
  var times = [];
  var vols = [];
  var macds = []; var difs = []; var deas = [];
  for (var i = 0; i < rawData.length; i++) {
	  datas.push(rawData[i]);
	  times.push(rawData[i].splice(0, 1)[0]);
	  vols.push(rawData[i][4]);
	  macds.push(rawData[i][6]);
	  difs.push(rawData[i][7]);
	  deas.push(rawData[i][8]);
  }
  return {
      datas: datas,
      times: times,
      vols: vols,
      macds: macds,
      difs: difs,
      deas: deas
  };
}
//分段计算
function fenduans(data){
  var markLineData = [];
  var idx = 0; var tag = 0; var vols = 0;
  for (var i = 0; i < data.times.length; i++) {
	  //初始化数据
      if(data.datas[i][5] != 0 && tag == 0){
          idx = i; vols = data.datas[i][4]; tag = 0;
      }
      if(tag == 1){ vols += data.datas[i][4]; }
      if(data.datas[i][5] != 0 && tag == 1){
          markLineData.push([{
              xAxis: idx,
              yAxis: data.datas[idx][1]>data.datas[idx][0]?(data.datas[idx][3]).toFixed(2):(data.datas[idx][2]).toFixed(2),
              // value: vols
          }, {
              xAxis: i,
              yAxis: data.datas[i][1]>data.datas[i][0]?(data.datas[i][3]).toFixed(2):(data.datas[i][2]).toFixed(2)
          }]);
          idx = i; vols = data.datas[i][4]; tag = 0;
      }
  }
  return markLineData;
}

//MA计算公式
function calculateMA(dayCount,data) {
  var result = [];
  for (var i = 0, len = data.times.length; i < len; i++) {
      if (i < dayCount-1) {
          result.push('-');
          continue;
      }
      var sum = 0;
      for (var j = 0; j < dayCount; j++) {
          sum += data.datas[i - j][1];
      }
      result.push((sum / dayCount).toFixed(2));
  }
  return result;
}

//数据模型 time0 open1 close2 min3 max4 vol5 tag6 macd7 dif8 dea9
// 日期 开盘价 收盘价 最低成交价 最高成交价
//['2015-10-19',18.56,18.25,18.19,18.56,55.00,0,-0.00,0.08,0.09] 
var cbfdata=splitData([['2018-01-22', 0.20, 0.20, 0.20, 0.20, 0.00, 0, 0.11, -0.28, -0.34]]);

var twdhqdata =splitData([['2016-11-15', 14.5, 14.66, 14.47, 14.82, 19.00, 0, 0.11, -0.28, -0.34],
                        ['2016-11-16', 14.77, 14.94, 14.72, 15.05, 26.00, 0, 0.14, -0.28, -0.35],
                        ['2016-11-17', 14.95, 15.03, 14.88,15.07, 38.00, 0, 0.12, -0.31, -0.37],
                        ['2016-11-18', 14.95, 14.9, 14.87, 15.06, 28.00, 0, 0.07, -0.35, -0.39],
                        ['2016-11-19', 14.9, 14.75, 14.68, 14.94, 22.00, 0, 0.03, -0.38, -0.40],
                        ['2016-11-22', 14.88, 15.01, 14.79,15.11, 38.00, 1, 0.01, -0.40, -0.40],
                        ['2016-11-23', 15.01, 14.83, 14.72, 15.01, 24.00, 0, -0.09, -0.45, -0.40],
                        ['2016-11-24', 14.75, 14.81, 14.67, 14.87, 21.00, 0, -0.17, -0.48, -0.39],
                        ['2016-11-25', 14.81, 14.25,14.21, 14.81, 51.00, 1, -0.27, -0.50, -0.37],
                        ['2016-11-26', 14.35, 14.45, 14.28, 14.57, 28.00, 0, -0.26, -0.46, -0.33],
                        ['2016-11-29', 14.43, 14.56, 14.04, 14.6, 48.00, 0, -0.25, -0.41, -0.29],
                        ['2016-12-01', 14.56,14.65, 14.36, 14.78, 32.00, 0, -0.21, -0.36, -0.25],
                        ['2016-12-02', 14.79, 14.96, 14.72, 14.97, 60.00, 0, -0.13, -0.29, -0.22],
                        ['2016-12-03', 14.95, 15.15, 14.91, 15.19, 53.00, 1, -0.05, -0.23, -0.21],
                        ['2016-12-04', 15.14, 15.97, 15.02, 16.02, 164.00, 1, 0.06, -0.17, -0.20],
                        ['2016-12-07', 15.9, 15.78, 15.65,16.0, 41.00, 0, 0.04, -0.19, -0.21],
                        ['2016-12-08', 15.78, 15.96, 15.21, 15.99, 45.00, 0, 0.05, -0.19, -0.21],
                        ['2016-12-09', 15.73, 16.05, 15.41, 16.08, 74.00, 0, 0.03, -0.20, -0.22],
                        ['2016-12-10', 15.82, 15.66, 15.65,15.98, 19.00, 0, -0.02, -0.23, -0.22],
                        ['2016-12-11', 15.59, 15.76, 15.42, 15.78, 32.00, 0, 0.01, -0.22, -0.22],
                        ['2016-12-14', 15.78, 15.72, 15.65, 16.04, 31.00, 0, 0.03, -0.20, -0.22],
                        ['2016-12-15', 15.81, 15.86,15.6, 15.99, 35.00, 0, 0.10, -0.18, -0.23],
                        ['2016-12-16', 15.88, 16.42, 15.79, 16.45, 123.00, 0, 0.17, -0.16, -0.24]]);

var myjfdata=splitData([['2016-11-15', 10, 11, 10, 11, 19.00, 0, 0.11, -0.28, -0.34],
                        ['2016-11-16', 11, 12.1, 11, 12.1, 26.00, 0, 0.14, -0.28, -0.35],
                        ['2016-11-17', 12.5, 13.75, 12.5,13.75, 38.00, 0, 0.12, -0.31, -0.37],
                        ['2016-11-18', 14, 15.4, 14, 15.4, 28.00, 0, 0.07, -0.35, -0.39],
                        ['2016-11-19', 15.5, 17, 15.5, 17, 22.00, 0, 0.03, -0.38, -0.40],
                        ['2016-11-22', 17.3, 19, 17.3,19, 38.00, 1, 0.01, -0.40, -0.40],
                        ['2016-11-23', 19.1, 20, 19.1, 20, 24.00, 0, -0.09, -0.45, -0.40],
                        ['2016-11-24', 20, 22, 20, 22, 21.00, 0, -0.17, -0.48, -0.39],
                        ['2016-11-25', 22.2, 24.42,22.2, 24.42, 51.00, 1, -0.27, -0.50, -0.37],
                        ['2016-11-26', 24.5, 26.9, 24.5, 26.9, 28.00, 0, -0.26, -0.46, -0.33],
                        ['2016-11-29', 27, 29.7, 27, 29.7, 48.00, 0, -0.25, -0.41, -0.29],
                        ['2016-12-01', 30,33, 30, 33, 32.00, 0, -0.21, -0.36, -0.25],
                        ['2016-12-02', 33.5, 36.8, 33.5, 36.8, 60.00, 0, -0.13, -0.29, -0.22],
                        ['2016-12-03', 37, 40.7, 37, 40.7, 53.00, 1, -0.05, -0.23, -0.21],
                        ['2016-12-04', 40.7, 44.77, 40.7, 44.77, 164.00, 1, 0.06, -0.17, -0.20],
                        ['2016-12-07', 45, 49.5, 45,49.5, 41.00, 0, 0.04, -0.19, -0.21],
                        ['2016-12-08', 50, 55, 50, 55, 45.00, 0, 0.05, -0.19, -0.21],
                        ['2016-12-09', 55.2, 60.7, 55.2, 60.7, 74.00, 0, 0.03, -0.20, -0.22],
                        ['2016-12-10', 61, 67.1, 61,67.1, 19.00, 0, -0.02, -0.23, -0.22],
                        ['2016-12-11', 67, 73.7, 67, 73.7, 32.00, 0, 0.01, -0.22, -0.22],
                        ['2016-12-14', 74, 81,74, 81, 31.00, 0, 0.03, -0.20, -0.22],
                        ['2016-12-15', 81, 88.1,81, 88.1, 35.00, 0, 0.10, -0.18, -0.23],
                        ['2016-12-16', 88, 96, 88, 96, 343.00, 0, 0.17, -0.16, -0.24]]);;

var qrtbdata=splitData([['2016-11-15', 42, 42, 42, 42, 0.00, 0, 0.11, -0.28, -0.34],
                        ['2016-11-16',  42, 42, 42, 42, 0.00,0, 0.14, -0.28, -0.35],
                        ['2016-11-17', 42, 42, 42, 42, 0.00, 0, 0.12, -0.31, -0.37],
                        ['2016-11-18',  42, 42, 42, 42, 0.00, 0, 0.07, -0.35, -0.39],
                        ['2016-11-19',  42, 42, 42, 42, 0.00, 0, 0.03, -0.38, -0.40],
                        ['2016-11-22',  42, 42, 42, 42, 0.00, 1, 0.01, -0.40, -0.40],
                        ['2016-11-23',  42, 42, 42, 42, 0.00, 0, -0.09, -0.45, -0.40],
                        ['2016-11-24',  42, 42, 42, 42, 0.00, 0, -0.17, -0.48, -0.39],
                        ['2016-11-25',  42, 42, 42, 42, 0.00, 1, -0.27, -0.50, -0.37],
                        ['2016-11-26',  42, 42, 42, 42, 0.00, 0, -0.26, -0.46, -0.33],
                        ['2016-11-29',  42, 42, 42, 42, 0.00, 0, -0.25, -0.41, -0.29],
                        ['2016-12-01',  42, 42, 42, 42, 0.00, 0, -0.21, -0.36, -0.25],
                        ['2016-12-02',  42, 42, 42, 42, 0.00, 60.00, 0, -0.13, -0.29, -0.22],
                        ['2016-12-03',  42, 42, 42, 42, 0.00, 1, -0.05, -0.23, -0.21],
                        ['2016-12-04',   42, 42, 42, 42, 0.00, 1, 0.06, -0.17, -0.20],
                        ['2016-12-07',  42, 42, 42, 42, 0.00, 0, 0.04, -0.19, -0.21],
                        ['2016-12-08',  42, 42, 42, 42, 0.00, 0, 0.05, -0.19, -0.21],
                        ['2016-12-09',  42, 42, 42, 42, 0.00, 74.00, 0, 0.03, -0.20, -0.22],
                        ['2016-12-10',  42, 42, 42, 42, 0.00, 0, -0.02, -0.23, -0.22],
                        ['2016-12-11',  42, 42, 42, 42, 0.00, 0, 0.01, -0.22, -0.22],
                        ['2016-12-14',  42, 42, 42, 42, 0.00, 0, 0.03, -0.20, -0.22],
                        ['2016-12-15',  42, 42, 42, 42, 0.00, 0, 0.10, -0.18, -0.23],
                        ['2016-12-16',  42, 42, 42, 42, 0.00, 0, 0.17, -0.16, -0.24]]);

var cbfcharts = {
  title: {
      show:false
  },
  tooltip: {
      trigger: 'axis',
      axisPointer: {
          type: 'line'
      }
  },
  legend: {
      data: ['KLine', 'MA5']
  },
  grid:[           {
      left: '3%',
      right: '1%',
      height: '60%'
  },{
      left: '3%',
      right: '1%',
      top: '76%',
      height: '15%'
  }
  ],
  xAxis: [{
      type: 'category',
      data: cbfdata.times,
      scale: true,
      boundaryGap: false,
      axisLine: { onZero: false },
      splitLine: { show: false },
      splitNumber: 20,
      min: 'dataMin',
      max: 'dataMax'
  },{
      type: 'category',
      gridIndex: 1,
      data: cbfdata.times,
      axisLabel: {show: false}
  }
  ],
  yAxis: [{
      scale: true,
      splitArea: {
          show: false
      }
  },{
      gridIndex: 1,
      splitNumber: 3,
      axisLine: {onZero: false},
      axisTick: {show: false},
      splitLine: {show: false},
      axisLabel: {show: true}
  }
  ],
  dataZoom: [{
        type: 'inside',
          xAxisIndex: [0, 0],
          start: 20,
          end: 100
    },{
          show: true,
          xAxisIndex: [0, 1],
          type: 'slider',
          top: '93%',
          start: 20,
          end: 100
    }
  ],
  series: [{
          name: 'K线周期图表(matols.com)',
          type: 'candlestick',
          data: cbfdata.datas,
          itemStyle: {
              normal: {
          color: '#ef232a',
            color0: '#14b143',
            borderColor: '#ef232a',
            borderColor0: '#14b143'
              }
          },
          markLine: {
              label: {
                  normal: {
                      position: 'middle',
                      textStyle:{color:'Blue',fontSize: 15}
                  }
              },
              data: fenduans(cbfdata),
              symbol: ['circle', 'none']
              
          }
      }, 
      {
          name: 'MA5',
          type: 'line',
          data: calculateMA(5,cbfdata),
          smooth: true,
          lineStyle: {
              normal: {
                  opacity: 0.5
              }
          }
      },
      {
          name: 'Volumn',
          type: 'bar',
          xAxisIndex: 1,
          yAxisIndex: 1,
          data: cbfdata.vols,
          itemStyle: {
          normal: {
              color: function(params) {
                  var colorList;
                  if (cbfdata.datas[params.dataIndex][1]>cbfdata.datas[params.dataIndex][0]) {
                      colorList = '#ef232a';
                  } else {
                      colorList = '#14b143';
                  }
                  return colorList;
              },
          }
        }
      },
  ]
};
var twdhqcharts = {
  title: {
      show:false
  },
  tooltip: {
      trigger: 'axis',
      axisPointer: {
          type: 'line'
      }
  },
  legend: {
      data: ['KLine', 'MA5']
  },
  grid:[           {
      left: '3%',
      right: '1%',
      height: '60%'
  },{
      left: '3%',
      right: '1%',
      top: '76%',
      height: '15%'
  }
  ],
  xAxis: [{
      type: 'category',
      data: twdhqdata.times,
      scale: true,
      boundaryGap: false,
      axisLine: { onZero: false },
      splitLine: { show: false },
      splitNumber: 20,
      min: 'dataMin',
      max: 'dataMax'
  },{
      type: 'category',
      gridIndex: 1,
      data: twdhqdata.times,
      axisLabel: {show: false}
  }
  ],
  yAxis: [{
      scale: true,
      splitArea: {
          show: false
      }
  },{
      gridIndex: 1,
      splitNumber: 3,
      axisLine: {onZero: false},
      axisTick: {show: false},
      splitLine: {show: false},
      axisLabel: {show: true}
  }
  ],
  dataZoom: [{
    	  type: 'inside',
          xAxisIndex: [0, 0],
          start: 20,
          end: 100
  	},{
          show: true,
          xAxisIndex: [0, 1],
          type: 'slider',
          top: '93%',
          start: 20,
          end: 100
  	}
  ],
  series: [{
          name: 'K线周期图表(matols.com)',
          type: 'candlestick',
          data: twdhqdata.datas,
          itemStyle: {
              normal: {
				  color: '#ef232a',
			      color0: '#14b143',
			      borderColor: '#ef232a',
			      borderColor0: '#14b143'
              }
          },
          markLine: {
              label: {
                  normal: {
                      position: 'middle',
                      textStyle:{color:'Blue',fontSize: 15}
                  }
              },
              data: fenduans(twdhqdata),
              symbol: ['circle', 'none']
              
          }
      }, 
      {
          name: 'MA5',
          type: 'line',
          data: calculateMA(5,twdhqdata),
          smooth: true,
          lineStyle: {
              normal: {
                  opacity: 0.5
              }
          }
      },
      {
          name: 'Volumn',
          type: 'bar',
          xAxisIndex: 1,
          yAxisIndex: 1,
          data: twdhqdata.vols,
          itemStyle: {
	    	  normal: {
		          color: function(params) {
		              var colorList;
		              if (twdhqdata.datas[params.dataIndex][1]>twdhqdata.datas[params.dataIndex][0]) {
		                  colorList = '#ef232a';
		              } else {
		                  colorList = '#14b143';
		              }
		              return colorList;
		          },
		      }
	      }
      },
  ]
};	

var myjfcharts = {
  title: {
      show:false
  },
  tooltip: {
      trigger: 'axis',
      axisPointer: {
          type: 'line'
      }
  },
  legend: {
      data: ['KLine', 'MA5']
  },
  grid:[           {
      left: '3%',
      right: '1%',
      height: '60%'
  },{
      left: '3%',
      right: '1%',
      top: '76%',
      height: '15%'
  }
  ],
  xAxis: [{
      type: 'category',
      data: myjfdata.times,
      scale: true,
      boundaryGap: false,
      axisLine: { onZero: false },
      splitLine: { show: false },
      splitNumber: 20,
      min: 'dataMin',
      max: 'dataMax'
  },{
      type: 'category',
      gridIndex: 1,
      data: myjfdata.times,
      axisLabel: {show: false}
  }
  ],
  yAxis: [{
      scale: true,
      splitArea: {
          show: false
      }
  },{
      gridIndex: 1,
      splitNumber: 3,
      axisLine: {onZero: false},
      axisTick: {show: false},
      splitLine: {show: false},
      axisLabel: {show: true}
  }
  ],
  dataZoom: [{
    	  type: 'inside',
          xAxisIndex: [0, 0],
          start: 20,
          end: 100
  	},{
          show: true,
          xAxisIndex: [0, 1],
          type: 'slider',
          top: '93%',
          start: 20,
          end: 100
  	}
  ],
  series: [{
          name: 'K线周期图表(matols.com)',
          type: 'candlestick',
          data: myjfdata.datas,
          itemStyle: {
              normal: {
				  color: '#ef232a',
			      color0: '#14b143',
			      borderColor: '#ef232a',
			      borderColor0: '#14b143'
              }
          },
          markLine: {
              label: {
                  normal: {
                      position: 'middle',
                      textStyle:{color:'Blue',fontSize: 15}
                  }
              },
              data: fenduans(myjfdata),
              symbol: ['circle', 'none']
              
          }
      }, 
      {
          name: 'MA5',
          type: 'line',
          data: calculateMA(5,myjfdata),
          smooth: true,
          lineStyle: {
              normal: {
                  opacity: 0.5
              }
          }
      },
      {
          name: 'Volumn',
          type: 'bar',
          xAxisIndex: 1,
          yAxisIndex: 1,
          data: myjfdata.vols,
          itemStyle: {
	    	  normal: {
		          color: function(params) {
		              var colorList;
		              if (myjfdata.datas[params.dataIndex][1]>myjfdata.datas[params.dataIndex][0]) {
		                  colorList = '#ef232a';
		              } else {
		                  colorList = '#14b143';
		              }
		              return colorList;
		          },
		      }
	      }
      },
  ]
};

var qrtbcharts = {
  title: {
      show:false
  },
  tooltip: {
      trigger: 'axis',
      axisPointer: {
          type: 'line'
      }
  },
  legend: {
      data: ['KLine', 'MA5']
  },
  grid:[           {
      left: '3%',
      right: '1%',
      height: '60%'
  },{
      left: '3%',
      right: '1%',
      top: '76%',
      height: '15%'
  }
  ],
  xAxis: [{
      type: 'category',
      data: qrtbdata.times,
      scale: true,
      boundaryGap: false,
      axisLine: { onZero: false },
      splitLine: { show: false },
      splitNumber: 20,
      min: 'dataMin',
      max: 'dataMax'
  },{
      type: 'category',
      gridIndex: 1,
      data: qrtbdata.times,
      axisLabel: {show: false}
  }
  ],
  yAxis: [{
      scale: true,
      splitArea: {
          show: false
      }
  },{
      gridIndex: 1,
      splitNumber: 3,
      axisLine: {onZero: false},
      axisTick: {show: false},
      splitLine: {show: false},
      axisLabel: {show: true}
  }
  ],
  dataZoom: [{
    	  type: 'inside',
          xAxisIndex: [0, 0],
          start: 20,
          end: 100
  	},{
          show: true,
          xAxisIndex: [0, 1],
          type: 'slider',
          top: '93%',
          start: 20,
          end: 100
  	}
  ],
  series: [{
          name: 'K线周期图表(matols.com)',
          type: 'candlestick',
          data: qrtbdata.datas,
          itemStyle: {
              normal: {
				  color: '#ef232a',
			      color0: '#14b143',
			      borderColor: '#ef232a',
			      borderColor0: '#14b143'
              }
          },
          markLine: {
              label: {
                  normal: {
                      position: 'middle',
                      textStyle:{color:'Blue',fontSize: 15}
                  }
              },
              data: fenduans(qrtbdata),
              symbol: ['circle', 'none']
              
          }
      }, 
      {
          name: 'MA5',
          type: 'line',
          data: calculateMA(5,qrtbdata),
          smooth: true,
          lineStyle: {
              normal: {
                  opacity: 0.5
              }
          }
      },
      {
          name: 'Volumn',
          type: 'bar',
          xAxisIndex: 1,
          yAxisIndex: 1,
          data: qrtbdata.vols,
          itemStyle: {
	    	  normal: {
		          color: function(params) {
		              var colorList;
		              if (qrtbdata.datas[params.dataIndex][1]>qrtbdata.datas[params.dataIndex][0]) {
		                  colorList = '#ef232a';
		              } else {
		                  colorList = '#14b143';
		              }
		              return colorList;
		          },
		      }
	      }
      },
  ]
};


