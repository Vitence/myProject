$.get('/finance/getMyOrder',{},function(json){
    if(json.code == '0000'){
        if(json.data.length > 0 ){
            var items = json.data;
            var html = '';
            for (var i = 0; i < items.length; i++){
                var item = items[i];
                html += '<tr><td>'+item.pay_at+'</td>\n' +
                    '                        <td>'+item.name+'</td>\n' +
                    '                        <td>'+item.number+'</td>\n' +
                    '                        <td>'+item.price+'</td>\n' +
                    '                        <td>'+item.total_price+'</td>\n' +
                    '                        <td>'+(parseFloat(item.procedures) > 0 ? item.procedures : '--')+'</td>\n' +
                    '                        <td class="'+(parseInt(item.type) == 1 ? 'redWord' : 'greenWord')+'">'+(parseInt(item.type) == 1 ? '买入' : '卖出')+'</td></tr>';
            }
            html += '';
        }

        $(".titleRow").nextAll().remove().end().after(html);
    }
});