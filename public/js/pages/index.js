// 指定图表的配置项和数据
var cbfcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:30","9:00","9:30","10:00","10:30",
               "11:00","11:30","12:00","12:30","13:00","13:30",
               "14:00","14:30","15:00","15:30","16:00","16:30",
               "17:00"]
    },
    yAxis: {
        position:'right',
        scale:true
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
        data: [0.20,0.20,0.20,0.20,0.20,0.20,
               0.20,0.20,0.20,0.20,0.20,0.20,
               0.20,0.20,0.20,0.20,0.20,0.20,
               0.20]
    }]
};
var twdhqcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:30","9:00","9:30","10:00","10:30",
               "11:00","11:30","12:00","12:30","13:00","13:30",
               "14:00","14:30","15:00","15:30","16:00","16:30",
               "17:00"]
    },
    yAxis: {
        position:'right',
        scale:true
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
        data: [20.9,20.4,20.7,20.1,20.0,19.6,
               19.6,19.9,19.8,20.4,20.3,20.2,
               20.7,20.8,20.9,21.2,20.3,20.5,
               20.00]
    }]
};
var myjfcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:30","9:00","9:30","10:00","10:30",
               "11:00","11:30","12:00","12:30","13:00","13:30",
               "14:00","14:30","15:00","15:30","16:00","16:30",
               "17:00"]
    },
    yAxis: {
        position:'right',
        scale:true
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
        data: [10.44,10.44,10.44,10.44,10.44,10.44,
               10.44,10.44,10.44,10.44,10.44,10.44,
               10.44,10.44,10.44,10.44,10.44,10.44,
               11.44]
    }]
};
var qrtbcharts = {
    title: {
        show: false
    },
    tooltip: {},
    xAxis: {
        boundaryGap : false,
        data: ["8:00","8:30","9:00","9:30","10:00","10:30",
               "11:00","11:30","12:00","12:30","13:00","13:30",
               "14:00","14:30","15:00","15:30","16:00","16:30",
               "17:00"]
    },
    yAxis: {
        position:'right',
        scale:true
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
        data: [42.00,42.00,42.00,42.00,42.00,42.00,
               42.00,42.00,42.00,42.00,42.00,42.00,
               42.00,42.00,42.00,42.00,42.00,42.00,
               42.00]
    }]
};
function index_changeCharts(charts_i,chartName){
    var myChart = echarts.init(document.getElementById('indexEcharts'));
    $('.navBlock td').removeClass('checked');
    $('.navBlock td').eq(charts_i).addClass('checked');

    // $('.chartInfo').hide();
    // $('.chartInfo').eq(charts_i).show();
    myChart.setOption(chartName);
}