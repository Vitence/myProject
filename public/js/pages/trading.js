    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('tradingCenterCharts'));
$(function(){
    
    // 使用刚指定的配置项和数据显示图表。
    myChart.hideLoading()
    myChart.setOption(cbfcharts);
    setInterval(changeKlineData,2000);
    /**
     * 买入，卖出验证 总价计算
     * @type {RegExp}
     */
    var intNumber =  /^[0-9]*[1-9][0-9]*$/;
    var numberEle = $("#buy").find('input').eq(1);
    var priceEle  = $("#buy").find('input').eq(0);
    numberEle.keyup(function(){
        if(!intNumber.test(numberEle.val())){
            userError(numberEle,'请输入整数')
        }
        var price = priceEle.val();
        if(price == ""){
            price = 0;
        }
        $("#buy li").eq(2).find('span').html('总计：'+ (isNaN(numberEle.val() * price) ? 0 : numberEle.val() * price));
    });
    priceEle.keyup(function(){
        if(parseFloat(priceEle.val()) <= 0){
            userError(priceEle,'请输入正确的价格');
        }
        var number = numberEle.val();
        if(number == ""){
            number = 0;
        }
        $("#buy li").eq(2).find('span').html('总计：'+ (isNaN(number * priceEle.val()) ? 0 : number * priceEle.val()));
    });

    var salenumberEle = $("#sale").find('input').eq(1);
    var salepriceEle  = $("#sale").find('input').eq(0);
    salenumberEle.keyup(function(){
        if(!intNumber.test(salenumberEle.val())){
            userError(salenumberEle,'请输入整数')
        }
        var price = salepriceEle.val();
        if(price == ""){
            price = 0;
        }
        $("#sale li").eq(2).find('span').html('总计：'+ (isNaN(salenumberEle.val() * price) ? 0 : salenumberEle.val() * price));
    });
    salepriceEle.keyup(function(){
        if(parseFloat(salepriceEle.val()) <= 0){
            userError(salepriceEle,'请输入正确的价格');
        }
        var number = salenumberEle.val();
        if(number == ""){
            number = 0;
        }
        $("#sale li").eq(2).find('span').html('总计：'+ (isNaN(number * salepriceEle.val()) ? 0 : number * salepriceEle.val()));
    });


    /**
     * 卖出
     */
    $(".saleButton").click(function(){
        var self = $(this);
        var intNumber =  /^[0-9]*[1-9][0-9]*$/;
        var numberEle = $("#sale").find('input').eq(1);
        var priceEle  = $("#sale").find('input').eq(0);
        var passwordElle = $("#sale").find('input').eq(2);
        var saleNumber = numberEle.val().trim();
        var price = priceEle.val().trim();
        if(!intNumber.test(saleNumber)){
            userError(numberEle,'请输入整数');return;
        }
        if(parseFloat(price) <= 0){
            userError(priceEle,'请输入正确的价格');return;
        }
        if(passwordElle.val().trim() == ""){
            userError(passwordElle,'请输入交易密码')
        }
        disableButton(self);

        var tokenName = $("input[name='tokenName']").val();
        var token = $("input[name='token']").val();
        var type = $(".showChartName").attr("currency");
        $.post('/transaction/sale',{
            number:saleNumber,
            price:price,
            type:type,
            password:passwordElle.val().trim(),
            tokenName:tokenName,
            token:token
        },function(json){
            enableButton(self);
            numberEle.val('');
            priceEle.val('');
            passwordElle.val('');
            $("#sale li").eq(2).find('span').html('总计：0');
            $("input[name='tokenName']").val(json.data.tokenName);
            $("input[name='token']").val(json.data.token);
            if(json.code == '0000'){
                getKinfo();
                getGuadanData();
                getAllGuadan();
                getOrder();
                errMsg('卖出成功')
            }else if (json.code == '1010'){
                errMsg('数量不足')
            }else if(json.code == '1020'){
                window.location.href='/user/login'
            }else if(json.code=='1011'){
                userError(passwordElle,'交易密码错误')
            }else if(json.code=='1030'){
                errMsg('开盘时间为早上8点到晚上7点')
            }else{
                errMsg('卖出失败，请重试')
            }
        });
    });

    /**
     * 买入
     */
    $(".buyButton").click(function(){
        var self = $(this);
        var intNumber =  /^[0-9]*[1-9][0-9]*$/;
        var numberEle = $("#buy").find('input').eq(1);
        var priceEle  = $("#buy").find('input').eq(0);
        var passwordElle = $("#buy").find('input').eq(2);
        var buyNumber = numberEle.val().trim();
        var price = priceEle.val().trim();
        if(!intNumber.test(buyNumber)){
            userError(numberEle,'请输入整数');return;
        }
        if(parseFloat(price) <= 0){
            userError(priceEle,'请输入正确的价格');return;
        }
        if(passwordElle.val().trim() == ""){
            userError(passwordElle,'请输入交易密码')
        }
        disableButton(self);

        var tokenName = $("input[name='tokenName']").val();
        var token = $("input[name='token']").val();
        var type = $(".showChartName").attr("currency");
        $.post('/transaction/buy',{
            number:buyNumber,
            price:price,
            type:type,
            password:passwordElle.val().trim(),
            tokenName:tokenName,
            token:token
        },function(json){
            enableButton(self);
            numberEle.val('');
            priceEle.val('');
            passwordElle.val('');
            $("#buy li").eq(2).find('span').html('总计：0');
            $("input[name='tokenName']").val(json.data.tokenName);
            $("input[name='token']").val(json.data.token);
            if(json.code == '0000'){
                getKinfo();
                getGuadanData();
                getAllGuadan();
                getOrder();
                errMsg('买入成功')
            }else if (json.code == '1010'){
                errMsg('余额不足')
            }else if(json.code == '1020'){
                window.location.href='/user/login'
            }else if(json.code=='1011'){
                userError(passwordElle,'交易密码错误')
            }else if(json.code=='1030'){
                errMsg('开盘时间为早上8点到晚上7点')
            }else{
                errMsg('买入失败，请重试')
            }
        });
    });
});

// k线变化
function changeKlineData(){
    var kType = $('.showChartName').attr('currency');
    switch(kType){
        case '1':
            if(Number($('.info li').eq(0).find('.number').text()) > twdhqInitData[twdhqInitData.length-1][4]){
                twdhqInitData[twdhqInitData.length-1][4] = Number($('.info li').eq(0).find('.number').text());
                twdhqInitData[twdhqInitData.length-1][5] = Number($('.info li').eq(7).find('.number').text());
            }else if(Number($('.info li').eq(0).find('.number').text()) < twdhqInitData[twdhqInitData.length-1][3]){
                twdhqInitData[twdhqInitData.length-1][3] = Number($('.info li').eq(0).find('.number').text());
                twdhqInitData[twdhqInitData.length-1][5] = Number($('.info li').eq(7).find('.number').text());
            }else{
                twdhqInitData[twdhqInitData.length-1][5] = Number($('.info li').eq(7).find('.number').text());
            }
            twdhqInitData[twdhqInitData.length-1][2] = Number($('.info li').eq(0).find('.number').text());
            $.extend(true,twdhqTempleData,twdhqInitData);
            twdhqdata = splitData(twdhqTempleData);
            myChart.setOption(twdhqcharts);
        break;
        case '2':
            if(Number($('.info li').eq(0).find('.number').text()) > myjfInitData[myjfInitData.length-1][4]){
                myjfInitData[myjfInitData.length-1][4] = Number($('.info li').eq(0).find('.number').text());
                myjfInitData[myjfInitData.length-1][5] = Number($('.info li').eq(7).find('.number').text());
            }else if(Number($('.info li').eq(0).find('.number').text()) < myjfInitData[myjfInitData.length-1][3]){
                myjfInitData[myjfInitData.length-1][4] = Number($('.info li').eq(0).find('.number').text());
                myjfInitData[myjfInitData.length-1][5] = Number($('.info li').eq(7).find('.number').text());
            }else{
                myjfInitData[myjfInitData.length-1][5] = Number($('.info li').eq(7).find('.number').text());
            }
            myjfInitData[myjfInitData.length-1][2] = Number($('.info li').eq(0).find('.number').text());
            $.extend(true,myjfTempleData,myjfInitData);
            myjfdata = splitData(myjfTempleData);
            myChart.setOption(myjfcharts);
        break;
        default:
        break;
    }
}
/**
 * 获取委托数据，切换币种和卖出或者卖出都需要重新执行
 */
function getGuadanData(){
    var type = $(".showChartName").attr("currency");
    $.get('/transaction/getGuadanData',{
        type:type
    },function(json){
        if(json.code=='0000'){
            // $(".guadan").nextAll().remove()
            if(json.data.length > 0){
                var items = json.data;
                var html = '';
                for (var i = 0; i< items.length; i++){
                    var item = items[i];
                    html += `<tr>
                                <td>${item.time}</td>
                                <td class="${item.type == 1 ? 'buyType' : 'saleType'}">${item.type == 1 ? '买入' : '卖出'}</td>
                                <td>${item.price}</td>
                                <td>${item.number}</td>
                                <td>${item.number - item.surplus_number}</td>
                                <td>${item.surplus_number}</td>
                                <td>
                                    <input data-id="${item.id}" type="button" class="chedan" value="撤单">
                                </td>
                            </tr>`;
                }
                $(".guadan").nextAll().remove().end().after(html);
            }
            // setTimeout("getGuadanData()",3000)
        }
    })
}
getGuadanData();

/**
 * 成交记录数据，切换币种和卖出或者卖出都需要重新执行
 */
function getOrder(){
    var type = $(".showChartName").attr("currency");
    $.get('/transaction/getOrder',{
        type:type
    },function(json){
        if(json.code=='0000'){
            // $(".order").nextAll().remove()
            if(json.data.length > 0){
                var items = json.data;
                var html = '';
                for (var i = 0; i< items.length; i++){
                    var item = items[i];
                    html += ` <tr>
                        <td>${item.price}</td>
                        <td>${item.number}</td>
                        <td>${item.total_price}</td>
                        <td>${item.time}</td>
                    </tr>`;
                }
                $(".order").nextAll().remove().end().after(html);
            }
            setTimeout("getOrder()",2000)
        }
    })
}
getOrder();

/**
 * 成交理事委托信息数据，切换币种和卖出或者卖出都需要重新执行
 */
function getAllGuadan(){
    var type = $(".showChartName").attr("currency");
    $.get('/transaction/getAllGuandan',{
        type:type
    },function(json){
        if(json.code=='0000'){
            // $(".allguandan").nextAll().remove();
            var items;
            var html = '';
            if(json.data.sale.length > 0){
                items = json.data.sale;
                for(var i = 0;i < items.length; i++){
                    var item = items[i];
                    html += `<tr class="saleRow">
                        <td class="greenWord">卖${4-i}</td>
                        <td>${item.price}</td>
                        <td>${item.surplus_number}</td>
                        <td>${item.time}</td>
                    </tr>`;
                }
            }
            setTimeout(function(){
                if(json.data.buy.length > 0){
                    items = json.data.buy;
                    for (var i = 0; i< items.length; i++){
                        var item = items[i];
                        html += `<tr class="saleRow">
                        <td class="redWord">买${i+1}</td>
                        <td>${item.price}</td>
                        <td>${item.surplus_number}</td>
                        <td>${item.time}</td>
                    </tr>`;
                    }
                }
                $(".allguandan").nextAll().remove().end().after(html);
                setTimeout("getAllGuadan()",2000)
            },200);
        }
    })
}
getAllGuadan();

function getKinfo(){
    var type = $(".showChartName").attr("currency");
    $.get('/transaction/getKOrder',{
        type:type
    },function(json){
        if(json.code=='0000'){
            var color = '';
            var jiajian = '';
            if(parseFloat(json.data.rise) > 0){
                color = 'up';
                jiajian = '+';
            }else if(parseFloat(json.data.rise) < 0 ){
                color = 'down';
                jiajian = '-';
            }
            $(".info li").eq(0).find('p:first').html(json.data.new_price);
            $(".info li").eq(1).find('p:first').html(jiajian + json.data.rise+"%");
            $(".info li").eq(1).find('p:first').removeClass('up');
            $(".info li").eq(1).find('p:first').removeClass('down');
            $(".info li").eq(1).find('p:first').addClass(color);
            $(".info li").eq(2).find('p:first').html(json.data.max);
            $(".info li").eq(3).find('p:first').html(json.data.min);
            $(".info li").eq(4).find('p:first').html(parseInt(json.data.buy_first) <= 0 ? '--' : json.data.buy_first);
            $(".info li").eq(5).find('p:first').html(parseInt(json.data.sale_first) <= 0 ? '--' : json.data.sale_first);
            $(".info li").eq(6).find('p:first').html(json.data.total_price);
            $(".info li").eq(7).find('p:first').html(json.data.total_number);
            setTimeout("getKinfo()",2000)
        }
    })
}
getKinfo();

function changeCharts(chart_i,chartName){
    $('.showChartName').text($('.coinOptionBlock li').eq(chart_i).text());
    $('.showChartName').attr('currency',$('.coinOptionBlock li').eq(chart_i).data('id'));
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('tradingCenterCharts'));
    // 使用刚指定的配置项和数据显示图表。
    myChart.hideLoading();
    myChart.setOption(chartName);
    getKinfo();
    getGuadanData();
    getOrder();
    getAllGuadan();
}

$(document).delegate('.chedan','click',function(){
    var id = $(this).data('id');
    var type = $(".showChartName").attr("currency");
    $.post('/transaction/cancelOrder',{
        type:type,
        id:id
    },function(json){
        if(json.code == '0000'){
            getGuadanData();
            getOrder();
            getAllGuadan();
            successMsg('撤单成功');
        }else{
            errMsg(json.msg);
        }
    })
});