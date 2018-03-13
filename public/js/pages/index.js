// 指定图表的配置项和数据
var cbfcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2,0.2]
    }]
};
var twdhqcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:20,
        // splitNumber:7,
        // minInterval:0.5

    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [25.85,26.03,26.53,26.86,26.35,26.46,
26.72,26.93,27.11,27.11,27.11,26.9,
27.11,27.11,27.11,26.87,26.9,27.09,
27.11,27.11,27.11,27.11,26.69,26.79,
26.99,26.99,27.11,27.11,27.11,26.77,
26.8,26.81,27.11,27.11,26.77,27.01,
26.55,26.54,26.44,26.56,26.59,26.96,
26.82,26.45,26.25,26.63,26.8,26.34,
26.16,25.93,26.21,26.14,26.35,26.43,
27.07]
    }]
};
var twdhq_pre_data = [25.85,26.03,26.53,26.86,26.35,26.46,
26.72,26.93,27.11,27.11,27.11,26.9,
27.11,27.11,27.11,26.87,26.9,27.09,
27.11,27.11,27.11,27.11,26.69,26.79,
26.99,26.99,27.11,27.11,27.11,26.77,
26.8,26.81,27.11,27.11,26.77,27.01,
26.55,26.54,26.44,26.56,26.59,26.96,
26.82,26.45,26.25,26.63,26.8,26.34,
26.16,25.93,26.21,26.14,26.35,26.43,
27.07];
var startNum = twdhq_pre_data[0];
var maxNum = startNum;
var minNum = startNum;
// function reset_twdhqcharts_data(){
//     var today_date = new Date();
//     var now_hour = today_date.getHours();
//     var now_minute = today_date.getMinutes();
//     var showNum = (now_hour>8)?(now_hour - 8)*6 + Math.floor(now_minute/10):0;
//     switch(showNum){
//         case 0:
//             $('.chartInfo').eq(0).find('.number').html('0');
//             $('.chartInfo').eq(0).find('.number').eq(2).html('0%');
//             console.log('not trueTime');
//         break;
//         default:
//         var show_data = new Array();
//         for(var i = 0 ; i < showNum ; i++){
//             show_data[i] = twdhq_pre_data[i];
//             maxNum = (show_data[i]>maxNum)?show_data[i]:maxNum;
//             minNum = (show_data[i]<minNum)?show_data[i]:minNum;
//         }
//         twdhqcharts.series[0].data = show_data;
//         var newPrice = Number($('.latest_price').eq(0).html());
//         $('.chartInfo').eq(0).find('.number').eq(0).html(maxNum);
//         $('.chartInfo').eq(0).find('.number').eq(1).html(minNum);
//         $('.chartInfo').eq(0).find('.number').eq(2).html(Math.floor((newPrice-startNum)/startNum*10000) / 100);
//         var myChart = echarts.init(document.getElementById('indexEcharts'));
//         myChart.setOption(twdhqcharts);
//         console.log('tradingTime');
//     }
// }
// var twdhq_interval = setInterval(reset_twdhqcharts_data,1000);
var myjfcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44,11.44]
    }]
};
var qrtbcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00,42.00]
    }]
};
var jdtxcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]
    }]
};
var mztbcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1]
    }]
};
var dyjfcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3]
    }]
};
var xnzzcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2]
    }]
};
var mdsqcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5]
    }]
};
var tttbcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5]
    }]
};
var jtxjcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10]
    }]
};
var scrscharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8]
    }]
};
var ddmmcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5,0.5]
    }]
};
var xdkcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8,1.8]
    }]
};
var jbtcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:10","8:20","8:30","8:40","8:50","9:00","9:10","9:20","9:30","9:40","9:50","10:00","10:10","10:20","10:30","10:40","10:50","11:00","11:10","11:20","11:30","11:40","11:50","12:00","12:10","12:20","12:30","12:40","12:50","13:00","13:10","13:20","13:30","13:40","13:50","14:00","14:10","14:20","14:30","14:40","14:50","15:00","15:10","15:20","15:30","15:40","15:50","16:00","16:10","16:20","16:30","16:40","16:50","17:00"]
    },
    yAxis: {
        position:'right',
        scale:true,
        min:null
    },
    color:[
        '#00aaee'
    ],
    series: [{
        name: '销量',
        type: 'line',
        showSymbol:false,
        hoverAnimation:false,
        lineStyle:{
            normal:{
                width:'1'
            }
        },
        areaStyle:{
            normal:{
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0, color: '#cef2fb' // 0% 处的颜色
                    }, {
                        offset: 1, color: '#f7fbff' // 100% 处的颜色
                    }],
                    globalCoord: false // 缺省为 false
                }
            }
        },
        data: [2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2]
    }]
};
function index_changeCharts(charts_i,chartName){
    var myChart = echarts.init(document.getElementById('indexEcharts'));
    switch(charts_i){
        case 0:
            myChart.setOption(chartName); 
            twdhq_interval =setInterval(reset_twdhqcharts_data,1000);
        break;
        default:
            window.clearInterval(twdhq_interval);
            myChart.setOption(chartName);
    }
    $('.navBlock td').removeClass('checked');
    $('.navBlock td').eq(charts_i).addClass('checked');
    $('.chartInfo').hide();
    $('.chartInfo').eq(charts_i).show();
    
}